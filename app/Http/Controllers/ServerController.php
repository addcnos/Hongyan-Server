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
namespace App\Http\Controllers;

use Addcnos\GatewayWorker\Client;

class ServerController extends BaseController
{
    /**
     * @apiDefine users 用户类
     */
    public function __construct()
    {
        $this->middleware('sign', ['only' => ['register']]);
    }

    /**
     * @api                 {post} /server/info 查看server连接情况
     * @apiGroup            server
     * @apiName             /server/info
     * @apiVersion          1.0.0
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": {
     *          "start_time": "服务器启动的时间",
     *          "connection_num": "当前连接的数量",
     *          "accept_count": "接受了多少个连接",
     *          "close_count": "关闭的连接数量",
     *          "tasking_num": "当前正在排队的任务数",
     *          "request_count": "Server 收到的请求次数",
     *          "worker_request_count": "当前 Worker 进程收到的请求次数",
     *          "coroutine_num": "当前协程数量 coroutine_num"
     *      }
     *  }
     */
    public function info()
    {
        $swoole = app('swoole');
        $result = $swoole->stats();
        return $this->success('notice.success', $result);
    }

    /**
     * @api                 {post} /server/info 查看websocket连接数
     * @apiGroup            server
     * @apiName             /server/getAllUidCount
     * @apiVersion          1.0.0
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": {
     *          "count": "用户连接数",
     *          "websocket_count":"websocket连接数"
     *      }
     *  }
     */
    public function getAllUidCount()
    {
        $userCount = Client::getAllUidCount();
        $websocketCount = Client::getAllClientIdCount();

        $result = [
            'user_count' => $userCount,
            'websocket_count' => $websocketCount,
        ];

        return $this->success('notice.success', $result);
    }
}
