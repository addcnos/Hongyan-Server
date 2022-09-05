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

use App\Models\UserModel;
use Illuminate\Support\Facades\Redis;

class UsersService extends BaseService
{
    /**
     * 注册逻辑.
     *
     * @param $appId
     * @param $uid
     * @param $nickname
     * @param $avatar
     * @param mixed $extend
     * @return bool
     */
    public function register($appId, $uid, $nickname, $avatar, $extend)
    {
        $token = sha1($appId . $uid . mt_rand() . time());
        $data = compact('nickname', 'avatar', 'extend');

        $res = UserModel::updateOrCreate(['app_id' => $appId, 'uid' => $uid], $data);

        if ($res) {
            $token = $this->tokenToRedis($appId, $res, $token, $uid);
        }

        $this->langKey = 'notice.do_success';
        return ['token' => $token];
    }

    public function getUserByToken($token)
    {
        if (empty($token)) {
            return [];
        }

        $user = $this->getUserCommon($token);

        $this->langKey = 'notice.success';

        return $user;
    }

    public function block($appId, $uid)
    {
        try {
            Redis::SADD(sprintf('%s_%s', 'blacklist', $appId), $uid);
            $this->langKey = 'notice.do_success';
            return true;
        } catch (\Exception $e) {
            $this->langKey = 'notice.server_error';
            return false;
        }
    }

    public function unBlock($appId, $uid)
    {
        try {
            Redis::SREM(sprintf('%s_%s', 'blacklist', $appId), $uid);
            $this->langKey = 'notice.do_success';
            return true;
        } catch (\Exception $e) {
            $this->langKey = 'notice.server_error';
            return false;
        }
    }

    public function getUserByUids($uids, $appId)
    {
        if (! $uids) {
            return [];
        }

        return UserModel::where('app_id', $appId)->whereIn('uid', $uids)->get()->toArray() ?: [];
    }

    /**
     * 判断是否已登录.
     *
     * @param string $token
     * @return bool
     */
    public function isLogined($token)
    {
        return $this->getUserByToken($token) ? true : false;
    }

    /**
     * 修改用户信息.
     * @param mixed $appId
     * @param mixed $uid
     * @param mixed $nickname
     * @param mixed $avatar
     * @param mixed $extend
     */
    public function edit($appId, $uid, $nickname, $avatar, $extend)
    {
        if (! $appId || ! $uid) {
            $this->setLangKey('notice.uid_error');
            return false;
        }

        $data = [];
        if ($nickname) {
            $data['nickname'] = $nickname;
        }
        if ($avatar) {
            $data['avatar'] = $avatar;
        }
        if ($extend) {
            $data['extend'] = $extend;
        }

        if (! $data) {
            $this->setLangKey('notice.none_edit');
            return false;
        }

        UserModel::query()->where('app_id', $appId)->where('uid', $uid)->update($data);

        // 更新缓存
        $tKey = sprintf('%s:%d_%s', config('im.uid_token'), $appId, $uid);
        $token = Redis::get($tKey);
        if ($token) {
            $uKey = sprintf('%s:%s', config('im.token_uid'), $token);
            $expireSeconds = Redis::TTL($uKey);
            if ($expireSeconds > 0) {
                $user = UserModel::onWriteConnection()->where('app_id', $appId)->where('uid', $uid)->first();
                $user = json_encode($user);
                Redis::SETEX($uKey, $expireSeconds, $user);
            }
        }

        return true;
    }

    private function tokenToRedis($appId, $res, $token, $uid)
    {
        $uKey = sprintf('%s:%s', config('im.token_uid'), $token);
        $tKey = sprintf('%s:%d_%s', config('im.uid_token'), $appId, $uid);

        $expireSeconds = Redis::TTL($tKey);
        if ($expireSeconds > 86400) {
            //未過期, token 不用更新 ,更新用戶信息
            $token = Redis::get($tKey);
            $uKey = sprintf('%s:%s', config('im.token_uid'), $token);
            Redis::SETEX($uKey, $expireSeconds, json_encode($res));
        } else {
            //重新生成
            Redis::SETEX($tKey, config('im.token_expire_time'), $token);
            Redis::SETEX($uKey, config('im.token_expire_time'), json_encode($res));
        }
        return $token;
    }
}
