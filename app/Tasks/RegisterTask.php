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
namespace App\Tasks;

use App\Models\UserModel;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Redis;

class RegisterTask extends Task
{
    private $appId;

    private $uid;

    private $data;

    private $token;

    public function __construct($appId, $uid, $token, $data)
    {
        $this->appId = $appId;
        $this->uid = $uid;
        $this->token = $token;
        $this->data = $data;
    }

    public function handle()
    {
        $res = UserModel::updateOrCreate(['app_id' => $this->appId, 'uid' => $this->uid], $this->data);
        if ($res) {
            $this->tokenToRedis($this->appId, $res, $this->token);
        }
    }

    private function tokenToRedis($appId, $res, $token)
    {
        Redis::SETEX(
            sprintf('%s:%s', config('im.uid_token'), $appId . '_' . $res->uid),
            config('im.token_expire_time'),
            $token
        );
        Redis::SETEX(
            sprintf('%s:%s', config('im.token_uid'), $token),
            config('im.token_expire_time'),
            json_encode($res)
        );
    }
}
