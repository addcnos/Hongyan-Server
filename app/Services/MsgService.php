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

use App\Models\MessageModel;
use Illuminate\Support\Facades\Redis;

/**
 * 消息類（限內部接口邏輯）.
 */
class MsgService extends BaseService
{
    // 获取聊过天的人
    public function getChatUsers($uid)
    {
        if (! $uid) {
            return [];
        }

        preg_match('/^(\d+)_(.+)$/', $uid, $match);
        // $appId  = $match[1] ?? 0;
        $userId = $match[2] ?? '';  // 业务uid

        $rKey = config('im.chat_users_list') . $uid;

        if (Redis::exists($rKey)) {
            $list = Redis::SMEMBERS($rKey);
        } else {
            $list = MessageModel::where('from_uid', $uid)
                ->orWhere('target_uid', $uid)
                ->selectRaw('DISTINCT from_uid, target_uid')
                ->get()
                ->toArray();
            if ($list) {
                $fUids = array_unique(array_column($list, 'from_uid'));
                $tUids = array_unique(array_column($list, 'target_uid'));
                $uids = array_merge($fUids, $tUids);
                $list = array_unique($uids);
            } else {
                $list = [$userId];  // 為了始終能走到redis
            }

            foreach ($list as $item) {
                Redis::sAdd($rKey, $item);
            }
            $seconds = mt_rand(30, 50) * 86400;  // 動態設置有效時間
            Redis::EXPIRE($rKey, $seconds);
        }

        return $list ? array_diff($list, [$userId]) : [];
    }
}
