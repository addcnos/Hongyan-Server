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
 * @contact  imtoogle@gmail.com
 */
namespace App\Tasks;

use App\Services\Messages\Message;
use Hhxsv5\LaravelS\Swoole\Task\Task;

class SendReadMsgTask extends Task
{
    private $clientUid = '';

    private $data = [];

    public function __construct($clientUid, $fromUid, $targetUid, $status = 1)
    {
        $this->clientUid = $clientUid;
        $this->data = [
            'from_uid' => $fromUid,
            'target_uid' => $targetUid,
            'status' => $status,
        ];
    }

    public function handle()
    {
        try {
            $data = [
                'type' => 'Sys:MsgRead',
                'from_uid' => '',
                'target_uid' => '',
                'send_time' => date('Y-m-d H:i:s'),
                'status' => 1,
                'arrivals_callback' => 0,
                'content' => '',
            ];

            $data = array_merge($data, $this->data);

            Message::create($data)->sendToUid($this->clientUid);
        } catch (\Throwable $th) {
            app('Psr\Log\LoggerInterface')->error($th->__toString());
        }
    }
}
