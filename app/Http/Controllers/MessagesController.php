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

use App\Services\MessagesService;
use Illuminate\Http\Request;

class MessagesController extends BaseController
{
    /**
     * @apiDefine messages 消息类
     */
    public function __construct()
    {
        $this->middleware('sign', ['only' => ['messageTransfer', 'sendByApps']]);
    }

    /**
     * @api                 {get} /messages/getHistoricalMessage 获取历史消息
     * @apiGroup            messages
     * @apiName             /messages/getHistoricalMessage
     * @apiVersion          1.0.0
     * @apiParam {String}   token token
     * @apiParam {String}   link_user 聊天对象
     * @apiParam {String}   [node_marker] 查询标记
     * @apiParam {String}   [limit=10] 每次拉取条数
     * @apiSuccessExample
     *   {
     *       "code": 200,
     *       "message": "请求成功",
     *       "data": {
     *           "data": [
     *               {
     *                   "msg_id": "f001017e-26d3-4366-9555-a709151453ac",
     *                   "from_uid": "1",
     *                   "target_uid": "3",
     *                   "type": "msg:text",
     *                   "content": "123",
     *                   "send_time": "2019-08-26 14:37:21",
     *                   "status": 0,
     *                   "arrivals_callback": 1,
     *                   "id": 87,
     *                   "created_at": "2019-08-26 14:37:21",
     *                   "read":1, //0未读1已读
     *               }
     *           ],
     *           "total": 1
     *       }
     *   }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function getHistoricalMessage(Request $request, MessagesService $messagesService)
    {
        $token = $request->input('token', '');
        $linkUser = $request->input('link_user', '');
        $nodeMarker = $request->input('node_marker', 0);
        $limit = $request->input('limit', 10);

        if (! $token || ! $linkUser) {
            return $this->error('notice.parameter_error');
        }

        $result = $messagesService->getHistoricalMessage($token, $linkUser, $nodeMarker, $limit);
        if ($result === false) {
            return $this->error($messagesService->langKey);
        }
        return $this->success('notice.success', $result);
    }

    /**
     * @api                 {post} /messages/send 发消息
     * @apiGroup            messages
     * @apiName             /messages/send
     * @apiVersion          1.0.0
     * @apiParam {String}   token token
     * @apiParam {String}   type 消息类型
     * @apiParam {String}   target_uid 接收者id
     * @apiParam {String}   content 内容
     * @apiParam {int}   [push=1] 是否推送:0否1是
     * @apiParam {String}   [device] 设备
     * @apiParam {String}   [version] 版本号
     * @apiParam {String}   [_appid] _appid
     * @apiParam {String}   [_randomstr] 随机数
     * @apiParam {String}   [_timestamp] 时间戳(s)
     * @apiParam {String}   [_signature] 签名
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "消息發送成功",
     *      "data": {
     *          "msg_id": "消息uid",
     *          "from_uid": "接受者id",
     *          "target_uid": "发送者id",
     *          "type": "消息类型",
     *          "content": "消息内容",
     *          "send_time": "发送时间",
     *          "status": '状态',
     *          "arrivals_callback": "消息是否需要回传,0否1是",
     *          "message_direction":"1是自己发出的消息,2是接收的消息",
     *      }
     *  }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function send(Request $request, MessagesService $messagesService)
    {
        $token = $request->input('token', '');
        $type = $request->input('type', '');
        $targetUid = $request->input('target_uid', '');
        $content = $request->input('content', '');
        $push = $request->input('push', 1);

        if (! $token || ! $targetUid) {
            return $this->error('notice.parameter_error');
        }

        $messageInfo = $messagesService->send($token, $type, $targetUid, $content, $push);
        if ($messageInfo === false) {
            return $this->error($messagesService->langKey);
        }
        return $this->success('notice.message.send_success', $messageInfo);
    }

    /**
     * @api                 {post} /messages/sendByApps 发消息(从业务端发起)
     * @apiGroup            messages
     * @apiName             /messages/sendByApps
     * @apiVersion          1.0.0
     * @apiParam {String}   from_uid 发送者
     * @apiParam {String}   type 消息类型
     * @apiParam {String}   target_uid 接收者id
     * @apiParam {String}   content 内容
     * @apiParam {int}   [push=1] 是否推送:0否1是
     * @apiHeader {String}   nonce 随机数
     * @apiHeader {String}   time-stamp 时间戳
     * @apiHeader {String}   sign 签名
     * @apiHeader {String}   app-key appKey
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "消息發送成功",
     *      "data": {
     *          "msg_id": "消息uid",
     *          "from_uid": "接受者id",
     *          "target_uid": "发送者id",
     *          "type": "消息类型",
     *          "content": "消息内容",
     *          "send_time": "发送时间",
     *          "status": '状态',
     *          "arrivals_callback": "消息是否需要回传,0否1是",
     *          "message_direction":"1是自己发出的消息,2是接收的消息",
     *      }
     *  }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function sendByApps(Request $request, MessagesService $messagesService)
    {
        $appId = $request->input('app_id', 2);
        $fromUid = $request->input('from_uid', '');
        $type = $request->input('type', '');
        $targetUid = $request->input('target_uid', '');
        $content = $request->input('content', '');
        $push = $request->input('push', 1);

        if (! $fromUid || ! $targetUid) {
            return $this->error('notice.parameter_error');
        }

        $messageInfo = $messagesService->sendByApps($appId, $fromUid, $type, $targetUid, $content, $push);
        if ($messageInfo === false) {
            return $this->error($messagesService->langKey);
        }
        return $this->success('notice.message.send_success', $messageInfo);
    }

    /**
     * @api                 {post} /messages/messageArrival 到达回调
     * @apiGroup            messages
     * @apiName             /messages/messageArrival
     * @apiVersion          1.0.0
     * @apiParam {String}   token token
     * @apiParam {String}   msg_id 消息id
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": null
     *  }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function messageArrival(Request $request, MessagesService $messagesService)
    {
        $token = $request->input('token', '');
        $msgId = $request->input('msg_id', '');

        if (! $token || ! $msgId) {
            return $this->error('notice.parameter_error');
        }

        $messagesService->messageArrival($token, $msgId);

        return $this->success('notice.do_success');
    }

    /**
     * @api                 {post} /messages/onlineNotice 上线广播
     * @apiGroup            messages
     * @apiName             /messages/onlineNotice
     * @apiVersion          1.0.0
     * @apiParam {String}   token token
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": null
     *  }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function onlineNotice(Request $request, MessagesService $messagesService)
    {
        $token = $request->input('token', '');

        if (! $token) {
            return $this->error('notice.parameter_error');
        }

        $result = $messagesService->onlineNotice($token);
        if ($result === false) {
            return $this->error($messagesService->langKey);
        }
        return $this->success('notice.do_success');
    }

    /**
     * @api                 {post} /messages/messageTransfer 旧消息迁移到IM
     * @apiGroup            messages
     * @apiName             /messages/messageTransfer
     * @apiVersion          1.0.0
     * @apiParam {String}   from_uid 发送者
     * @apiParam {String}   target_uid 接受者
     * @apiParam {String}   type 类型
     * @apiParam {String}   content 消息体
     * @apiParam {String}   created_at 发送时间
     * @apiParam {int}   app_id 应用id
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": null
     *  }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function messageTransfer(Request $request, MessagesService $messagesService)
    {
        $fromUId = $request->input('from_uid', '');
        $targetUid = $request->input('target_uid', '');
        $type = $request->input('type', 'Msg:Customize');
        $content = $request->input('content', '');
        $createdAt = $request->input('created_at', '');
        $appId = $request->input('app_id', 0);

        if (empty($fromUId) || empty($targetUid) || empty($content) || empty($appId)) {
            return $this->error('notice.parameter_error');
        }

        $messagesService->messageTransfer($fromUId, $targetUid, $type, $content, $createdAt, $appId);

        return $this->success('notice.do_success');
    }

    /**
     * @api                 {post} /messages/messageSynchronization 消息同步
     * @apiGroup            messages
     * @apiName             /messages/messageSynchronization
     * @apiVersion          1.0.0
     * @apiParam {String}   token 同步到用户的token
     * @apiParam {String}   from_uid 被同步者uid
     * @apiParam {int}   [limit=365] 同步的天数
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": true
     *  }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function messageSynchronization(Request $request, MessagesService $messagesService)
    {
        $token = $request->input('token', '');
        $fromUid = $request->input('from_uid', '');
        $limit = $request->input('limit', 365);

        if (! $token || ! $fromUid) {
            return $this->error('notice.parameter_error');
        }

        $result = $messagesService->messageSynchronization($token, $fromUid, $limit);
        if ($result === false) {
            return $this->error($messagesService->langKey);
        }
        return $this->success('notice.success', $result);
    }

    /**
     * @api                 {post} /messages/pictureUpload 图片上传
     * @apiGroup            messages
     * @apiName             /messages/pictureUpload
     * @apiVersion          1.0.0
     * @apiParam {file}   picture 图片
     * @apiParam {String}   token token
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": null
     *  }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function pictureUpload(Request $request, MessagesService $messagesService)
    {
        $picture = $request->file('picture', '');
        $token = $request->input('token', '');

        if (! $token || ! $picture) {
            return $this->error('notice.parameter_error');
        }

        $result = $messagesService->pictureUpload($picture, $token);

        if ($result === false) {
            return $this->error($messagesService->langKey);
        }
        return $this->success('notice.success', $result);
    }

    /**
     * @api                 {post} /messages/delLiaisonPerson 删除联络人
     * @apiGroup            messages
     * @apiName             /messages/delLiaisonPerson
     * @apiVersion          1.0.0
     * @apiParam {String}   target_uid 被删者uid
     * @apiParam {String}   token token
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": true
     *  }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function delLiaisonPerson(Request $request, MessagesService $messagesService)
    {
        $token = $request->input('token', '');
        $targetUid = $request->input('target_uid', '');

        if (! $token || ! $targetUid) {
            return $this->error('notice.parameter_error');
        }
        $result = $messagesService->delLiaisonPerson($token, $targetUid);

        if ($result === false) {
            return $this->error($messagesService->langKey);
        }
        return $this->success('notice.success', $result);
    }
}
