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
namespace App\Http\Controllers;

use App\Services\AppsService;
use App\Services\ChatService;
use Illuminate\Http\Request;

class ChatController extends BaseController
{
    protected $chatService;

    /**
     * @apiDefine chat 聊天类
     */
    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    /**
     * @api                 {get} /chat/users 聊天界面联系人列表
     * @apiGroup            chat
     * @apiName             /chat/users
     * @apiVersion          1.0.0
     * @apiParam {String}   token 用户token
     * @apiParam {int}   [limit = 20] 每页条数
     * @apiParam {int}   [page =1] 页码
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": [
     *           {
     *               "last_time": "消息发送时间",
     *               "nickname": "昵称",
     *               "avatar": "头像",
     *               "is_online": 1,
     *               "uid":目标id,
     *               "new_msg_count": 0,
     *               "content":{
     *                     'content':'最后一条消息',
     *                     'extra':''
     *               },
     *              "type": "消息类型",
     *              "extend": "擴展數據"
     *           }
     *       ],
     *       "total":4
     *   }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function users(Request $request)
    {
        $token = $request->input('token', '');
        $limit = $request->input('limit', 100);
        $page = $request->input('page', 1);

        if (! $token) {
            return $this->error('notice.parameter_error');
        }

        $user = $this->chatService->getChatUsersByToken($token, $limit, $page);
        if ($this->chatService->langKey) {
            return $this->error($this->chatService->langKey);
        }

        return $this->successWithTotal('notice.success', $user['data'], $user['total']);
    }

    /**
     * @api                 {post} /chat/readMsg 消息设置已读
     * @apiGroup            chat
     * @apiName             /chat/readMsg
     * @apiVersion          1.0.0
     * @apiParam {String}   token       用户token
     * @apiParam {String}   target_uid  当前联系人uid
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": true
     *   }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function readMsg(Request $request)
    {
        $token = $request->input('token', '');
        $target_uid = $request->input('target_uid', '');

        if (! $token || ! $target_uid) {
            return $this->error('notice.parameter_error');
        }

        $res = $this->chatService->readMsg($token, $target_uid);

        if ($this->chatService->langKey) {
            return $this->error($this->chatService->langKey);
        }

        return $this->success('notice.success', $res);
    }

    /**
     * @api                 {get} /chat/onlineStatus 获取联系人列表在线状态
     * @apiGroup            chat
     * @apiName             /chat/onlineStatus
     * @apiVersion          1.0.0
     * @apiParam {String}   token   用户token
     * @apiParam {String}   uids    所有联系人uid，json ["11","22"]
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": {
     *                  "uid1": 1,
     *                  "uid2": 0,
     *       }
     *   }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function onlineStatus(Request $request)
    {
        $token = $request->input('token', '');
        $uids = $request->input('uids', ''); //json ["11","22"]

        $uids = json_decode($uids, true) ?: [];
        $uids = array_unique(array_filter($uids));

        if (! $token || empty($uids)) {
            return $this->error('notice.parameter_error');
        }

        $onlineStatus = $this->chatService->onlineStatus($token, $uids);

        if ($this->chatService->langKey) {
            return $this->error($this->chatService->langKey);
        }

        return $this->success('notice.success', $onlineStatus);
    }

    /**
     * @api                 {get} /chat/onlineStatusByUids 获取联系人列表在线状态
     * @apiGroup            chat
     * @apiName             /chat/onlineStatusByUids
     * @apiVersion          1.0.0
     * @apiParam {String}   app_key 平台key
     * @apiParam {String}   app_secret 平台密钥
     * @apiParam {String}   uids    所有联系人uid，json ["11","22"]
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": {
     *                  "uid1": 1,
     *                  "uid2": 0,
     *       }
     *   }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function onlineStatusByUids(Request $request)
    {
        $appKey = $request->input('app_key', '');
        $appSecret = $request->input('app_secret', '');
        $uids = $request->input('uids', ''); //json ["11","22"]

        $uids = json_decode($uids, true) ?: [];
        $uids = array_unique(array_filter($uids));

        if (! $appKey || ! $appSecret || empty($uids)) {
            return $this->error('notice.parameter_error');
        }

        $appId = (new AppsService())->getAppId($appKey, $appSecret);
        if (! $appId) {
            return $this->error('notice.key_error');
        }

        $onlineStatus = $this->chatService->onlineStatusByUids($uids, $appId);

        if ($this->chatService->langKey) {
            return $this->error($this->chatService->langKey);
        }

        return $this->success('notice.success', $onlineStatus);
    }

    /**
     * @api                 {get} /chat/getAllNewMessage 获取新消息总数
     * @apiGroup            chat
     * @apiName             /chat/getAllNewMessage
     * @apiVersion          1.0.0
     * @apiParam {String}   token   用户token
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": {
     *                  "count": 11
     *       }
     *   }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function getAllNewMessage(Request $request)
    {
        $token = $request->input('token', '');

        if (! $token) {
            return $this->error('notice.parameter_error');
        }

        $messageCount = $this->chatService->getAllNewMessage($token);

        if ($this->chatService->langKey) {
            return $this->error($this->chatService->langKey);
        }

        return $this->success('notice.success', $messageCount);
    }

    /**
     * @api                 {post} /chat/lastMsgClear 删除会话数据
     * @apiGroup            chat
     * @apiName             /chat/lastMsgClear
     * @apiVersion          1.0.0
     * @apiParam {String}   token   用户token
     * @apiParam {int}   [days=30] 保留的天数
     * @apiParam {int}   [limit=100] 保留的条数
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": null
     *   }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function lastMsgClear(Request $request)
    {
        $token = $request->input('token', '');
        $days = $request->input('days', 30);
        $limit = $request->input('limit', 100);

        if (! $token) {
            return $this->error('notice.parameter_error');
        }

        $this->chatService->lastMsgClear($token, $days, $limit);

        if ($this->chatService->langKey) {
            return $this->error($this->chatService->langKey);
        }

        return $this->success('notice.success');
    }

    /**
     * @api                 {get} /chat/getConversationInfo 获取双方信息
     * @apiGroup            chat
     * @apiName             /chat/getConversationInfo
     * @apiVersion          1.0.0
     * @apiParam {String}   token   用户token
     * @apiParam {String}   target_uid   目标用户
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "請求成功",
     *       "data": {
     *           "user": {
     *               "id": 183,
     *               "app_id": 1,
     *               "uid": "268",
     *               "nickname": "test1 小姐",
     *               "avatar": "https://127.0.0.1/avatar/crop/2019/09/02/156739416662856507_90x90.jpg",
     *               "created_at": "2019-09-09 17:28:37",
     *               "updated_at": "2019-10-09 15:06:37",
     *               "extend": ""
     *           },
     *           "target": {
     *               "id": 183,
     *               "app_id": 1,
     *               "uid": "268",
     *               "nickname": "test1 小姐",
     *               "avatar": "https://127.0.0.1/avatar/crop/2019/09/02/156739416662856507_90x90.jpg",
     *               "created_at": "2019-09-09 17:28:37",
     *               "updated_at": "2019-10-09 15:06:37",
     *               "extend": ""
     *           }
     *       }
     *   }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function getConversationInfo(Request $request)
    {
        $token = $request->input('token', '');
        $targetUid = $request->input('target_uid', '');

        if (! $token || ! $targetUid) {
            return $this->error('notice.parameter_error');
        }

        $result = $this->chatService->getConversationInfo($token, $targetUid);
        if ($result === false) {
            return $this->error($this->chatService->langKey);
        }
        return $this->success('notice.success', $result);
    }
}
