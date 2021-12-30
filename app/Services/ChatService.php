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
namespace App\Services;

use Addcnos\GatewayWorker\Client;
use App\Models\UserModel;
use Illuminate\Support\Facades\Redis;

class ChatService extends BaseService
{
    /**
     * 根据token获取联系人列表.
     *
     * @param string $token 用户token
     * @param mixed $limit
     * @param mixed $page
     * @return array
     */
    public function getChatUsersByToken($token, $limit, $page)
    {
        $this->langKey = '';
        $usersService = app(UsersService::class);
        $user = $usersService->getUserByToken($token);

        if (empty($user) || empty($user->id)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }

        return $this->chatUsers($user->uid, $user->app_id, $limit, $page);
    }

    /**
     * 获取联系人列表.
     *
     * @param string $uid
     * @param int $appId
     * @param mixed $limit
     * @param mixed $page
     * @return array
     */
    public function chatUsers($uid, $appId, $limit, $page)
    {
        //获取总条数
        $total = Redis::ZCARD(sprintf('%s_%s', config('im.last_msg_set') . ':' . $appId, $uid));

        $start = $limit * ($page - 1);
        $end = $start + $limit - 1;
        $liaisonPerson = Redis::ZREVRANGE(
            sprintf('%s_%s', config('im.last_msg_set') . ':' . $appId, $uid),
            $start,
            $end
        );

        if (empty($liaisonPerson)) {
            return ['data' => [], 'total' => $total];
        }

        $data = [];
        foreach ($liaisonPerson as $link) {
            $user = UserModel::where([['app_id', $appId], ['uid', $link]])
                ->select(['uid', 'nickname', 'avatar', 'extend'])
                ->first();
            if (empty($user)) {
                //从集合剔除
                Redis::ZREM(sprintf('%s_%s', config('im.last_msg_set') . ':' . $appId, $uid), $link);
                //消息提醒删除
                Redis::hdel(sprintf('%s_%s', config('im.msg_count') . ':' . $appId, $uid), $link);
                //最后一条消息删除
                Redis::hdel(sprintf('%s_%s', config('im.last_msg') . ':' . $appId, $uid), $link);
                continue;
            }
            $user = $user->toArray();

            $newMsgCount = Redis::hget(sprintf('%s_%s', config('im.msg_count') . ':' . $appId, $uid), $user['uid']);
            $user['new_msg_count'] = ! empty($newMsgCount) ? $newMsgCount : '0';
            $lastMessage = Redis::hget(sprintf('%s_%s', config('im.last_msg') . ':' . $appId, $uid), $user['uid']);
            $lastMessage = json_decode($lastMessage, true) ?: [];
            $user['last_time'] = isset($lastMessage['last_time']) ? $lastMessage['last_time'] : '0';
            $content = isset($lastMessage['content']) ? json_decode($lastMessage['content'], true) : '';
            $user['content'] = empty($content) ? ['content' => '', 'extra' => null] : $content;
            $user['type'] = isset($lastMessage['type']) ? $lastMessage['type'] : '';
            array_push($data, $user);
        }

        return ['data' => $data, 'total' => $total];
    }

    /**
     * 用户是否在线（批量）.
     *
     * @param int $appId
     * @return [uid => bool]
     */
    public function isUidsOnline(array $uids, $appId)
    {
        $res = [];
        foreach ($uids as $uid) {
            $isOnline = Client::isUidOnline($this->getBindUid($appId, $uid));
            $res[$uid] = (int) $isOnline;
        }
        return $res;
    }

    /**
     * 用户是否在线（单个）.
     *
     * @param string $uid
     * @param int $appId
     * @return bool
     */
    public function isOnline($uid, $appId)
    {
        $res = $this->isUidsOnline([$uid], $appId);
        return isset($res[$uid]) ? $res[$uid] : 0;
    }

    /**
     * 设置消息已读.
     *
     * @param string $token
     * @param string $uid
     * @return bool
     */
    public function readMsg($token, $uid)
    {
        $this->langKey = '';
        $usersService = app(UsersService::class);
        $user = $usersService->getUserByToken($token);

        if (empty($user) || empty($user->id)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }

        //置0
        $cacheKey = sprintf('%s:%d_%s', config('im.msg_count'), $user->app_id, $user->uid);
        $res = Redis::hSet($cacheKey, $uid, 0);
        Redis::EXPIRE($cacheKey, config('im.liaison_person'));

        //记录最后一次的阅读时间
        $cacheKeyOfLastRead = sprintf('%s:%d_%s', config('im.last_read'), $user->app_id, $user->uid);
        Redis::hSet($cacheKeyOfLastRead, $uid, date('Y-m-d H:i:s'));
        Redis::EXPIRE($cacheKeyOfLastRead, config('im.liaison_person'));

        //发送已读通知
        app(MessagesService::class)->sendReadMsg($uid, $user->app_id, $user->uid);

        return $res ? true : false;
    }

    /**
     * 联系人在线状态
     *
     * @param string $token
     * @param array $uids
     * @return array
     */
    public function onlineStatus($token, $uids)
    {
        $this->langKey = '';
        $usersService = app(UsersService::class);
        $user = $usersService->getUserByToken($token);

        if (empty($user) || empty($user->id)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }

        if (! is_array($uids) || empty($uids)) {
            $this->langKey = 'notice.uid_invalid';
            return false;
        }

        return $this->isUidsOnline($uids, $user->app_id);
    }

    /**
     * 联系人在线状态
     *
     * @param array $uids
     * @param int $appId
     * @return array
     */
    public function onlineStatusByUids($uids, $appId)
    {
        if (! is_array($uids) || empty($uids)) {
            $this->langKey = 'notice.uid_invalid';
            return false;
        }
        return $this->isUidsOnline($uids, $appId);
    }

    public function getAllNewMessage($token)
    {
        $this->langKey = '';
        $usersService = app(UsersService::class);
        $user = $usersService->getUserByToken($token);

        if (empty($user)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }

        $cacheKey = sprintf('%s:%d_%s', config('im.msg_count'), $user->app_id, $user->uid);

        $newMessageCount = Redis::hgetall($cacheKey) ?: [];
        return ['count' => array_sum($newMessageCount)];
    }

    public function lastMsgClear($token, $days, $limit)
    {
        $this->langKey = '';
        $usersService = app(UsersService::class);
        $user = $usersService->getUserByToken($token);

        if (empty($user)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }
        $cKey = sprintf('%s:%d_%s', config('im.last_msg'), $user->app_id, $user->uid);
        $linkUser = Redis::hGetAll($cKey);

        if (empty($linkUser)) {
            return true;
        }
        $tagTime = strtotime('-' . $days . 'days');

        try {
            $count = 0;
            foreach ($linkUser as $k => $u) {
                ++$count;
                $u = json_decode($u);
                if (strtotime($u->last_time) < $tagTime || $count > $limit) {
                    Redis::hdel(
                        sprintf('%s_%s', config('im.last_msg') . ':' . $user->app_id, $user->uid),
                        $k
                    );
                }
            }
        } catch (\Exception $e) {
            $this->langKey = 'notice.server_error';
            return false;
        }
    }

    public function getConversationInfo($token, $targetUid)
    {
        //获取自己的信息
        $user = app(UsersService::class)->getUserByToken($token);
        if (empty($user)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }
        //获取目标的信息
        $targetToken = Redis::get(sprintf('%s:%s', config('im.uid_token'), $user->app_id . '_' . $targetUid));
        $targetInfo = Redis::get(sprintf('%s:%s', config('im.token_uid'), $targetToken));
        if (empty($targetInfo)) {
            $targetInfo = UserModel::where([
                ['app_id', $user->app_id],
                ['uid', $targetUid],
            ])->first();
            $targetInfo = $targetInfo ? $targetInfo->toArray() : [];
        } else {
            $targetInfo = json_decode($targetInfo, true);
        }

        $user->is_online = $this->isOnline($user->uid, $user->app_id);

        if ($targetInfo) {
            $targetInfo['is_online'] = $this->isOnline($targetInfo['uid'] ?? '', $user->app_id);
        }

        return ['user' => $user, 'target' => $targetInfo ?? []];
    }
}
