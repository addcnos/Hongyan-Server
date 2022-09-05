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
namespace App\Traits;

use Illuminate\Support\Facades\Redis;

trait ImCommon
{
    /**
     * 获得绑定client的uid.
     *
     * @param int $appId
     * @param string $uid
     * @return string
     */
    public function getBindUid($appId, $uid)
    {
        return $appId . '_' . $uid;
    }

    /**
     * 根据token获取用户信息.
     *
     * @param $token
     */
    public function getUserCommon($token)
    {
        $user = Redis::get(sprintf('%s:%s', config('im.token_uid'), $token));

        if (empty($user)) {
            return [];
        }
        $user = json_decode($user);
        if (gettype($user) != 'object') {
            return [];
        }
        $tokenIm = Redis::get(sprintf('%s:%s', config('im.uid_token'), $user->app_id . '_' . $user->uid));

        if ($tokenIm != $token) {
            return [];
        }
        return $user;
    }
}
