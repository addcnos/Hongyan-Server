<?php

declare(strict_types=1);
/**
 * This file is part of RCS.
 *
 * @link     https://github.com
 * @document https://github.com/addcnos/hongyan/blob/master/README.md
 * @license  https://github.com/addcnos/hongyan/blob/master/LICENSE
 * @author   Addcn.Inc
 * @contact  huangdijia@gmail.com
 * @contact  365039476@qq.com
 */
namespace App\Tasks;

use App\Models\MessageModel;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Redis;

class MessageSynchronization extends Task
{
    private $user;

    private $from_uid;

    private $limit;

    public function __construct($user, $fromUid, $limit)
    {
        $this->user = $user;
        $this->from_uid = $fromUid;
        $this->limit = $limit;
    }

    public function handle()
    {
        try {
            //message表数据修改
            $this->imMessageOfMySql();
            //生成 set 与 hash 表
            $this->updateLastMsgOfRedis();
            //删除被同步者的联络人列表
            $this->liaisonPersonOffromUid();
        } catch (\Throwable $th) {
            app('Psr\Log\LoggerInterface')->error($th->__toString());
        }
    }

    private function imMessageOfMySql()
    {
        $originalFromUid = $this->from_uid;
        $fromUid = $this->user->app_id . '_' . $this->from_uid;
        $user = $this->user;
        $limitData = date('Y-m-d', strtotime('-' . $this->limit . ' days'));

        $messages = MessageModel::where([['app_id', '=', $this->user->app_id], ['created_at', '>', $limitData]])
            ->where(function ($query) use ($fromUid) {
                $query->where('from_uid', $fromUid)->orWhere('target_uid', $fromUid);
            })->get([
                'id',
                'msg_id',
                'app_id',
                'conversation',
                'from_uid',
                'target_uid',
                'type',
                'status',
                'created_at',
                'del_status',
            ]);
        if (empty($messages)) {
            return;
        }
        $messages = $messages->toArray();
        foreach ($messages as $message) {
            $checkDel = $this->checkDelMessage($message['conversation'], $originalFromUid, $message['del_status']);
            if ($checkDel) {
                continue;
            }
            $targetUid = $fromUid = '';
            if ($message['from_uid'] === $originalFromUid) {
                $fromUid = $user->app_id . '_' . $user->uid;
                $targetUid = $user->app_id . '_' . $message['target_uid'];
            }
            if ($message['target_uid'] === $originalFromUid) {
                $targetUid = $user->app_id . '_' . $user->uid;
                $fromUid = $user->app_id . '_' . $message['from_uid'];
            }
            if (! $targetUid || ! $fromUid) {
                continue;
            }
            //导致自己与自己聊天的消息不会同步
            if ($targetUid == $fromUid) {
                continue;
            }
            $conversation = [$fromUid, $targetUid];
            sort($conversation);
            //校正删除状态字段
            if ($message['del_status'] > 0) {
                //对方删了
                if ($conversation[0] !== $user->app_id . '_' . $user->uid) {
                    $delStatus = 1;
                } else {
                    $delStatus = 2;
                }
            } else {
                $delStatus = 0;
            }
            $message['conversation'] = $conversation;
//            $message['msg_id'] = (string)Str::uuid();
            MessageModel::where('id', $message['id'])->update([
                'conversation' => implode(',', $conversation),
                'from_uid' => $fromUid,
                'target_uid' => $targetUid,
                'del_status' => $delStatus,
            ]);
        }
    }

    private function checkDelMessage($conversation, $originalUid, $delStatus)
    {
        $conversation = explode(',', $conversation);
        if ($conversation[0] === $this->user->app_id . '_' . $originalUid) {
            $valueIn = [0, 2];
        } else {
            $valueIn = [0, 1];
        }
        if (! in_array($delStatus, $valueIn)) {
            return true;
        }
        return false;
    }

    private function updateLastMsgOfRedis()
    {
        $limitData = date('Y-m-d', strtotime('-' . $this->limit . ' days'));
        $user = $this->user;
        //修复同步后,被删除的联络人又出现在列表的BUG
        $conversations = MessageModel::where([
            ['app_id', $this->user->app_id],
            ['conversation', 'like', '%' . $user->app_id . '_' . $user->uid . '%'],
            ['created_at', '>', $limitData],
        ])
            ->groupBy('conversation')->pluck('conversation');
        foreach ($conversations as $conversation) {
            $maxId = $this->getMaxId($conversation);
            if (empty($maxId)) {
                continue;
            }
            $message = MessageModel::where('id', $maxId)->first();
            $message = $message->toArray();
            $content = json_encode([
                'content' => $message['content'],
                'last_time' => $message['created_at'],
                'type' => $message['type'],
            ]);
            if ($message['from_uid'] === $user->uid) {
                $fieldId = $message['target_uid'];
            } else {
                $fieldId = $message['from_uid'];
            }
            Redis::hset(
                sprintf('%s_%s', config('im.last_msg') . ':' . $user->app_id, $user->uid),
                $fieldId,
                $content
            );

            Redis::zadd(sprintf(
                '%s_%s',
                config('im.last_msg_set') . ':' . $user->app_id,
                $user->uid
            ), strtotime($message['created_at']), $fieldId);
            //更新聊天对象的 redis 数据
            $res = Redis::hdel(sprintf('%s_%s', config('im.last_msg') . ':' . $user->app_id, $fieldId), $this->from_uid);
            Redis::hset(
                sprintf('%s_%s', config('im.last_msg') . ':' . $user->app_id, $fieldId),
                $user->uid,
                $content
            );
            Redis::Zrem(sprintf(
                '%s_%s',
                config('im.last_msg_set') . ':' . $user->app_id,
                $fieldId
            ), $this->from_uid);
            Redis::zadd(sprintf(
                '%s_%s',
                config('im.last_msg_set') . ':' . $user->app_id,
                $fieldId
            ), strtotime($message['created_at']), $user->uid);
            Redis::hdel(sprintf('%s_%s', config('im.msg_count') . ':' . $user->app_id, $fieldId), $this->from_uid);
        }

        //加过期时间
        Redis::EXPIRE(sprintf(
            '%s_%s',
            config('im.last_msg_set') . ':' . $user->app_id,
            $user->uid
        ), config('im.liaison_person'));
        Redis::EXPIRE(
            sprintf('%s_%s', config('im.last_msg') . ':' . $user->app_id, $user->uid),
            config('im.liaison_person')
        );
    }

    private function getMaxId($conversation)
    {
        $conversationArr = explode(',', $conversation);
        if ($conversationArr[0] === $this->user->app_id . '_' . $this->user->uid) {
            $valueIn = [0, 2];
        } else {
            $valueIn = [0, 1];
        }
        return MessageModel::where([
            ['app_id', $this->user->app_id],
            ['conversation', $conversation],
        ])
            ->whereIn('del_status', $valueIn)->max('id');
    }

    private function liaisonPersonOffromUid()
    {
        $user = $this->user;
        Redis::del(sprintf('%s_%s', config('im.last_msg') . ':' . $user->app_id, $this->from_uid));
        Redis::del(sprintf('%s_%s', config('im.last_msg_set') . ':' . $user->app_id, $this->from_uid));
        Redis::del(sprintf('%s_%s', config('im.msg_count') . ':' . $user->app_id, $this->from_uid));
    }
}
