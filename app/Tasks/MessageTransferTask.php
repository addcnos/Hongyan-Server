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
use Illuminate\Support\Str;

class MessageTransferTask extends Task
{
    private $from_uid;

    private $target_uid;

    private $type;

    private $content;

    private $created_at;

    private $app_id;

    public function __construct($fromUId, $targetUid, $type, $content, $createdAt, $appId)
    {
        $this->from_uid = $fromUId;
        $this->target_uid = $targetUid;
        $this->type = $type;
        $this->content = $content;
        $this->created_at = $createdAt;
        $this->app_id = $appId;
    }

    public function handle()
    {
        //消息入库
        $this->insertToDatabase();
        //在redis记录最后一条消息
        $this->lastMessageRecord();
    }

    private function insertToDatabase()
    {
        $fromId = $this->app_id . '_' . $this->from_uid;
        $targetId = $this->app_id . '_' . $this->target_uid;
        $conversation = [$fromId, $targetId];
        sort($conversation);
        MessageModel::create([
            'msg_id' => (string) Str::uuid(),
            'app_id' => $this->app_id,
            'conversation' => implode(',', $conversation),
            'from_uid' => $fromId,
            'target_uid' => $targetId,
            'content' => $this->content,
            'status' => 1,
            'type' => $this->type,
            'created_at' => $this->created_at,
        ]);
    }

    /**
     * 记录AB的最后一条消息.
     */
    private function lastMessageRecord()
    {
        //有序用于联络人列表分页与排序
        Redis::zadd(sprintf(
            '%s_%s',
            config('im.last_msg_set') . ':' . $this->app_id,
            $this->target_uid
        ), strtotime($this->created_at), $this->from_uid);
        Redis::EXPIRE(sprintf(
            '%s_%s',
            config('im.last_msg_set') . ':' . $this->app_id,
            $this->target_uid
        ), config('im.liaison_person'));

        Redis::zadd(
            sprintf('%s_%s', config('im.last_msg_set') . ':' . $this->app_id, $this->from_uid),
            strtotime($this->created_at),
            $this->target_uid
        );
        Redis::EXPIRE(sprintf(
            '%s_%s',
            config('im.last_msg_set') . ':' . $this->app_id,
            $this->from_uid
        ), config('im.liaison_person'));

        //联络人列表
        $content = json_encode([
            'content' => $this->content,
            'last_time' => $this->created_at,
            'type' => $this->type,
        ]);

        $from = sprintf('%s_%s', config('im.last_msg') . ':' . $this->app_id, $this->from_uid);
        $target = sprintf('%s_%s', config('im.last_msg') . ':' . $this->app_id, $this->target_uid);

        Redis::hset($target, $this->from_uid, $content);
        Redis::EXPIRE($target, config('im.liaison_person'));

        Redis::hset($from, $this->target_uid, $content);
        Redis::EXPIRE($from, config('im.liaison_person'));
    }
}
