<?php

declare(strict_types=1);
/**
 * This file is part of RCS.
 *
 * @link     https://github.com
 * @document https://github.com/addcnos/hongyan/blob/master/README.md
 * @license  https://github.com/addcnos/hongyan/blob/master/LICENSE
 * @author   Addcn.Inc
 */
namespace App\Services;

use Addcnos\GatewayWorker\Client;
use App\Jobs\PushJob;
use App\Models\AppsModel;
use App\Models\MessageModel;
use App\Models\UserModel;
use App\Services\Messages\Message;
use Illuminate\Support\Facades\Redis;

/**
 * 消息發送器.
 */
class MessageSenderService extends BaseService
{
    private $msgData = [];

    private $token;

    private $type;

    private $targetUid;

    private $content;

    private $push;

    public function send($token, $type, $targetUid, $content, $push)
    {
        $this->token = $token;
        $this->type = $type;
        $this->targetUid = $targetUid;
        $this->content = $content;
        $this->push = $push;

        $this->execSend();
    }

    private function execSend()
    {
        try {
            $user = app(UsersService::class)->getUserByToken($this->token);
            if (empty($user)) {
                return;
            }
            //自己不与自己聊
            if ($user && $user->uid === $this->targetUid) {
                return;
            }
            $appId = $user->app_id;

            //目标id不存在返回
            $target = $this->checkTargetUser($appId, $this->targetUid);
            if (! $target) {
                return;
            }

            //针对content里面包含换行符导致json_decode无法解析的处理
            $content = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $this->content);

            $data = [
                'from_uid' => $user->uid,
                'target_uid' => $this->targetUid,
                'type' => $this->type,
                'content' => json_decode($content, true),
                'nickname' => $user->nickname,
            ];
            //检测在线
            $targetId = $appId . '_' . $this->targetUid;
            $message = Message::create($data);

            $isOnline = Client::isUidOnline($targetId);
            if ($isOnline) {
                $message->data['status'] = 1;
                $message->sendToUid($targetId);
            } else {
                //离线
                $message->data['status'] = 0;
                if ($this->push) {
                    $this->push($appId, $data, $user->nickname);
                }
            }
            $message->data['app_id'] = $appId;
            $message->data['content'] = $content;
            $this->msgData = $message->data;

            //此類型不入庫
            if (strtolower($this->type) == 'sys:customize') {
                return true;
            }

            //消息入库
            $this->insertToDatabase();
            //在redis记录消息条数
            $this->messageRemind();
            //在redis记录最后一条消息
            $this->lastMessageRecord();
            //記錄好友關係
            $this->sAddChatUsers();
        } catch (\Throwable $th) {
            app('Psr\Log\LoggerInterface')->error($th->__toString());
        }
    }

    private function checkTargetUser($appId, $targetId)
    {
        $target = UserModel::where([['app_id', $appId], ['uid', $targetId]])->count();
        return $target ? true : false;
    }

    private function insertToDatabase()
    {
        $data = $this->imMessageData();
        MessageModel::create($data);
    }

    private function imMessageData()
    {
        $fromId = $this->msgData['app_id'] . '_' . $this->msgData['from_uid'];
        $targetId = $this->msgData['app_id'] . '_' . $this->msgData['target_uid'];
        $conversation = Message::conversation($this->msgData['app_id'], $this->msgData['from_uid'], $this->msgData['target_uid']);

        return [
            'msg_id' => $this->msgData['msg_id'],
            'app_id' => $this->msgData['app_id'],
            'conversation' => $conversation,
            'from_uid' => $fromId,
            'target_uid' => $targetId,
            'content' => $this->msgData['content'],
            'status' => $this->msgData['status'],
            'type' => $this->msgData['type'],
        ];
    }

    /**
     * 消息提醒.
     */
    private function messageRemind()
    {
        Redis::hincrby(
            sprintf('%s_%s', config('im.msg_count') . ':' . $this->msgData['app_id'], $this->msgData['target_uid']),
            $this->msgData['from_uid'],
            1
        );
        Redis::EXPIRE(
            sprintf('%s_%s', config('im.msg_count') . ':' . $this->msgData['app_id'], $this->msgData['target_uid']),
            config('im.liaison_person')
        );
    }

    /**
     * 记录AB的最后一条消息.
     */
    private function lastMessageRecord()
    {
        //有序用于联络人列表分页与排序
        Redis::zadd(sprintf(
            '%s_%s',
            config('im.last_msg_set') . ':' . $this->msgData['app_id'],
            $this->msgData['target_uid']
        ), time(), $this->msgData['from_uid']);
        Redis::EXPIRE(sprintf(
            '%s_%s',
            config('im.last_msg_set') . ':' . $this->msgData['app_id'],
            $this->msgData['target_uid']
        ), config('im.liaison_person'));

        Redis::zadd(
            sprintf('%s_%s', config('im.last_msg_set') . ':' . $this->msgData['app_id'], $this->msgData['from_uid']),
            time(),
            $this->msgData['target_uid']
        );
        Redis::EXPIRE(sprintf(
            '%s_%s',
            config('im.last_msg_set') . ':' . $this->msgData['app_id'],
            $this->msgData['from_uid']
        ), config('im.liaison_person'));

        //联络人列表
        $content = json_encode([
            'content' => $this->msgData['content'],
            'last_time' => $this->msgData['send_time'],
            'type' => $this->msgData['type'],
        ]);

        $from = sprintf('%s_%s', config('im.last_msg') . ':' . $this->msgData['app_id'], $this->msgData['from_uid']);
        $target = sprintf('%s_%s', config('im.last_msg') . ':' . $this->msgData['app_id'], $this->msgData['target_uid']);

        Redis::hset($target, $this->msgData['from_uid'], $content);
        Redis::EXPIRE($target, config('im.liaison_person'));

        Redis::hset($from, $this->msgData['target_uid'], $content);
        Redis::EXPIRE($from, config('im.liaison_person'));
    }

    private function push($appId, $data, $nickname)
    {
        $data['nickname'] = $nickname;
        $app = AppsModel::where('id', $appId)->first();
        $url = $app->callback_url ?? '';

        if (! $url) {
            return true;
        }

        dispatch(new PushJob($url, $data));
    }

    // 好友列表寫入redis
    private function sAddChatUsers()
    {
        $fromId = $this->msgData['app_id'] . '_' . $this->msgData['from_uid'];
        $targetId = $this->msgData['app_id'] . '_' . $this->msgData['target_uid'];

        $fromKey = config('im.chat_users_list') . $fromId;
        $targetKey = config('im.chat_users_list') . $targetId;

        if (Redis::exists($fromKey)) {
            Redis::sAdd($fromKey, $this->msgData['target_uid']);
        }
        if (Redis::exists($targetKey)) {
            Redis::sAdd($targetKey, $this->msgData['from_uid']);
        }
    }
}
