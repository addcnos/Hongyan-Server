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
namespace App\Tasks;

use Addcnos\GatewayWorker\Client;
use App\Models\MessageModel;
use App\Models\UserModel;
use App\Services\Messages\Message;
use App\Services\UsersService;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Redis;

class SendTask extends Task
{
    private $data = [];

    private $token;

    private $type;

    private $targetUid;

    private $content;

    private $push;

    public function __construct($token, $type, $targetUid, $content, $push)
    {
        Client::$registerAddress = config('gatewayworker.register_address');
        $this->token = $token;
        $this->type = $type;
        $this->targetUid = $targetUid;
        $this->content = $content;
        $this->push = $push;
    }

    public function handle()
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
            $this->data = $message->data;

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
        $fromId = $this->data['app_id'] . '_' . $this->data['from_uid'];
        $targetId = $this->data['app_id'] . '_' . $this->data['target_uid'];
        $conversation = Message::conversation($this->data['app_id'], $this->data['from_uid'], $this->data['target_uid']);

        return [
            'msg_id' => $this->data['msg_id'],
            'app_id' => $this->data['app_id'],
            'conversation' => $conversation,
            'from_uid' => $fromId,
            'target_uid' => $targetId,
            'content' => $this->data['content'],
            'status' => $this->data['status'],
            'type' => $this->data['type'],
        ];
    }

    /**
     * 消息提醒.
     */
    private function messageRemind()
    {
        Redis::hincrby(
            sprintf('%s_%s', config('im.msg_count') . ':' . $this->data['app_id'], $this->data['target_uid']),
            $this->data['from_uid'],
            1
        );
        Redis::EXPIRE(
            sprintf('%s_%s', config('im.msg_count') . ':' . $this->data['app_id'], $this->data['target_uid']),
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
            config('im.last_msg_set') . ':' . $this->data['app_id'],
            $this->data['target_uid']
        ), time(), $this->data['from_uid']);
        Redis::EXPIRE(sprintf(
            '%s_%s',
            config('im.last_msg_set') . ':' . $this->data['app_id'],
            $this->data['target_uid']
        ), config('im.liaison_person'));

        Redis::zadd(
            sprintf('%s_%s', config('im.last_msg_set') . ':' . $this->data['app_id'], $this->data['from_uid']),
            time(),
            $this->data['target_uid']
        );
        Redis::EXPIRE(sprintf(
            '%s_%s',
            config('im.last_msg_set') . ':' . $this->data['app_id'],
            $this->data['from_uid']
        ), config('im.liaison_person'));

        //联络人列表
        $content = json_encode([
            'content' => $this->data['content'],
            'last_time' => $this->data['send_time'],
            'type' => $this->data['type'],
        ]);

        $from = sprintf('%s_%s', config('im.last_msg') . ':' . $this->data['app_id'], $this->data['from_uid']);
        $target = sprintf('%s_%s', config('im.last_msg') . ':' . $this->data['app_id'], $this->data['target_uid']);

        Redis::hset($target, $this->data['from_uid'], $content);
        Redis::EXPIRE($target, config('im.liaison_person'));

        Redis::hset($from, $this->data['target_uid'], $content);
        Redis::EXPIRE($from, config('im.liaison_person'));
    }

    private function push($appId, $data, $nickname)
    {
        $data['nickname'] = $nickname;
        if (! isset(config('push.callback_url')[$appId])) {
            return true;
        }
        $url = config('push.callback_url')[$appId];
        $task = new PushTask($url, $data);
        Task::deliver($task, true);
    }

    // 好友列表寫入redis
    private function sAddChatUsers()
    {
        $fromId = $this->data['app_id'] . '_' . $this->data['from_uid'];
        $targetId = $this->data['app_id'] . '_' . $this->data['target_uid'];

        $fromKey = config('im.chat_users_list') . $fromId;
        $targetKey = config('im.chat_users_list') . $targetId;

        if (Redis::exists($fromKey)) {
            Redis::sAdd($fromKey, $this->data['target_uid']);
        }
        if (Redis::exists($targetKey)) {
            Redis::sAdd($targetKey, $this->data['from_uid']);
        }
    }
}
