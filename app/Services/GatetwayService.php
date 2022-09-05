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
use App\Services\Messages\Message;

class GatetwayService extends BaseService
{
    /**
     * 建立连接.
     * @param mixed $clientId
     * @param mixed $data
     */
    public function onWebSocketConnect($clientId, $data)
    {
        try {
            //token校验
            $userInfo = app(UsersService::class)->getUserByToken($data['get']['token']);

            if ($userInfo) {
                //返回
                $data = [
                    'type' => 'Sys:Connect',
                    'content' => ['client_id' => $clientId],
                    'arrivals_callback' => 0,
                ];

                $appIdUid = $userInfo->app_id . '_' . $userInfo->uid;

                //绑定
                Client::bindUid($clientId, $appIdUid);

                //绑定到group,便于广播
                Client::joinGroup($clientId, $userInfo->app_id);

                Message::create($data)->sendToClient($clientId);
            } else {
                $data = [
                    'type' => 'Sys:Disconnect',
                    'content' => [
                        'code' => __('notice.user_not_exists')['code'],
                        'msg' => __('notice.user_not_exists')['message'],
                    ],
                ];
                Message::create($data)->sendToClient($clientId);
                //断开连接
                Client::closeClient($clientId);
            }
        } catch (\Exception $e) {
            info(__METHOD__, ['error' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $data = [
                'type' => 'Sys:Disconnect',
                'content' => [
                    'code' => __('notice.system_error')['code'],
                    'msg' => __('notice.system_error')['message'],
                ],
            ];
            Message::create($data)->sendToClient($clientId);
            //断开连接
            Client::closeClient($clientId);
        }
    }

    /**
     * 接收消息事件.
     * @param mixed $clientId
     * @param mixed $message
     */
    public function onMessage($clientId, $message)
    {
    }

    public function onClose($clientId)
    {
        $data = [
            'type' => 'Sys:Disconnect',
            'content' => [
                'code' => __('notice.ws_close')['code'],
                'msg' => __('notice.ws_close')['message'],
            ],
        ];
        echo json_encode($data);
    }
}
