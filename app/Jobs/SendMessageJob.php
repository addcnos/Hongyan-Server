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
namespace App\Jobs;

use App\Services\MessageSenderService;

/**
 * 消息發送隊列.
 */
class SendMessageJob extends Job
{
    /**
     * 任务可以执行的最大秒数 (超时时间)。
     *
     * @var int
     */
    public $timeout = 90;

    private $data;

    /**
     * Create a new job instance.
     *
     * @param mixed $token
     * @param mixed $type
     * @param mixed $targetUid
     * @param mixed $content
     * @param mixed $push
     */
    public function __construct($token, $type, $targetUid, $content, $push)
    {
        $this->data = [
            'token' => $token,
            'type' => $type,
            'targetUid' => $targetUid,
            'content' => $content,
            'push' => $push,
        ];
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $sender = new MessageSenderService();
        $sender->send($this->data['token'], $this->data['type'], $this->data['targetUid'], $this->data['content'], $this->data['push']);
    }
}
