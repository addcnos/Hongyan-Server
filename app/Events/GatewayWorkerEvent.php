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
 */
namespace App\Events;

use Addcnos\GatewayWorker\GatewayWorkerEventInterface;
use App\Services\GatetwayService;

class GatewayWorkerEvent implements GatewayWorkerEventInterface
{
    /**
     * BusinessWorker 启动.
     *
     * @param mixed $businessWorker
     */
    public static function onWorkerStart($businessWorker)
    {
        echo "BusinessWorker Start\n";
    }

    /**
     * 建立连接.
     *
     * @param mixed $clientId
     */
    public static function onConnect($clientId)
    {
    }

    /**
     * 建立连接.
     *
     * @param mixed $clientId
     * @param mixed $data
     */
    public static function onWebSocketConnect($clientId, $data)
    {
        app(GatetwayService::class)->onWebSocketConnect($clientId, $data);
    }

    /**
     * 接收消息.
     *
     * @param mixed $clientId
     * @param string $message
     */
    public static function onMessage($clientId, $message)
    {
        app(GatetwayService::class)->onMessage($clientId, $message);
    }

    /**
     * 断开连接.
     *
     * @param [type] $clientId
     */
    public static function onClose($clientId)
    {
        // echo 'close connection', $clientId, "\n";
        app(GatetwayService::class)->onClose($clientId);
    }
}
