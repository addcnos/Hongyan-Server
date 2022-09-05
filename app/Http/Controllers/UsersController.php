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
namespace App\Http\Controllers;

use App\Services\UsersService;
use Illuminate\Http\Request;

class UsersController extends BaseController
{
    /**
     * @apiDefine users 用户类
     */
    public function __construct()
    {
        $this->middleware('sign', ['only' => ['register', 'block', 'unBlock', 'edit']]);
        $this->middleware('check_uid', ['only' => ['register']]);
    }

    /**
     * @api                 {post} /users/register 注册
     * @apiGroup            users
     * @apiName             /users/register
     * @apiVersion          1.0.0
     * @apiParam {String}   uid 用户id
     * @apiParam {String}   [nickname] 用户昵称
     * @apiParam {String}   [avatar] 用户头像链接
     * @apiParam {String}   [extend] 扩展字段,json字符串
     * @apiHeader {String}   nonce 随机数
     * @apiHeader {String}   time-stamp 时间戳
     * @apiHeader {String}   sign 签名
     * @apiHeader {String}   app-key appKey
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": {
     *          "token": "24354534"
     *      }
     *  }
     * @apiErrorExample
     *   {
     *       "code": 4001,
     *       "message": "错误提示",
     *       "data": null
     *   }
     */
    public function register(Request $request, UsersService $usersService)
    {
        $appId = $request->input('app_id', 0);
        $uid = $request->input('uid', '');
        $nickname = $request->input('nickname', '');
        $avatar = $request->input('avatar', '');
        $extend = $request->input('extend', '');

        if (! $appId || ! $uid) {
            return $this->error('notice.parameter_error');
        }

        $result = $usersService->register($appId, $uid, $nickname, $avatar, $extend);

        if ($result === false) {
            return $this->error($usersService->langKey);
        }
        return $this->success($usersService->langKey, $result);
    }

    /**
     * @api                 {post} /users/block 拉黑
     * @apiGroup            users
     * @apiName             /users/block
     * @apiVersion          1.0.0
     * @apiDescription  被拉黑用戶不能发送消息(提示发送成功,但是消息没有被处理)
     * @apiParam {String}   uid 被拉黑的用户id
     * @apiHeader {String}   nonce 随机数
     * @apiHeader {String}   time-stamp 时间戳
     * @apiHeader {String}   sign 签名
     * @apiHeader {String}   app-key appKey
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": null
     *  }
     */
    public function block(Request $request, UsersService $usersService)
    {
        $appId = $request->input('app_id', 0);
        $uid = $request->input('uid', '');

        if (! $appId || ! $uid) {
            return $this->error('notice.parameter_error');
        }

        $result = $usersService->block($appId, $uid);

        if ($result === false) {
            return $this->error($usersService->langKey);
        }
        return $this->success($usersService->langKey);
    }

    /**
     * @api                 {post} /users/unBlock 解除拉黑
     * @apiGroup            users
     * @apiName             /users/unBlock
     * @apiVersion          1.0.0
     * @apiParam {String}   uid 被拉黑的用户id
     * @apiHeader {String}   nonce 随机数
     * @apiHeader {String}   time-stamp 时间戳
     * @apiHeader {String}   sign 签名
     * @apiHeader {String}   app-key appKey
     * @apiSuccessExample
     *  {
     *      "code": 200,
     *      "message": "操作成功",
     *      "data": null
     *  }
     */
    public function unBlock(Request $request, UsersService $usersService)
    {
        $appId = $request->input('app_id', 0);
        $uid = $request->input('uid', '');

        if (! $appId || ! $uid) {
            return $this->error('notice.parameter_error');
        }

        $result = $usersService->unBlock($appId, $uid);

        if ($result === false) {
            return $this->error($usersService->langKey);
        }
        return $this->success($usersService->langKey);
    }

    /**
     * @api                 {get} /users/edit 修改用户信息
     * @apiGroup            users
     * @apiName             /users/edit
     * @apiVersion          1.0.0
     * @apiParam {String}   uid 用户uid
     * @apiParam {String}   nickname 昵称
     * @apiParam {String}   avatar 头像
     * @apiParam {json}     extend 扩展资料
     * @apiHeader {String}   nonce 随机数
     * @apiHeader {String}   time-stamp 时间戳
     * @apiHeader {String}   sign 签名
     * @apiHeader {String}   app-key appKey
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
    public function edit(Request $request, UsersService $usersService)
    {
        $uid = $request->get('uid');
        $appId = $request->get('app_id');
        $nickname = $request->get('nickname');
        $avatar = $request->get('avatar');
        $extend = $request->get('extend');

        $result = $usersService->edit($appId, $uid, $nickname, $avatar, $extend);
        if ($result == false) {
            return $this->error($usersService->getLangKey());
        }
        return $this->success('notice.do_success');
    }
}
