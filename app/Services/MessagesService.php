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

use App\Jobs\SendMessageJob;
use App\Models\MessageModel;
use App\Services\Messages\Message;
use App\Tasks\MessageArrivalTask;
use App\Tasks\MessageSynchronization;
use App\Tasks\MessageTransferTask;
use App\Tasks\OnLineNoticeTask;
use App\Tasks\SendReadMsgTask;
use App\Tasks\SendTask;
use Hhxsv5\LaravelS\Swoole\Task\Task;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;

class MessagesService extends BaseService
{
    /**
     * 获取历史消息.
     *
     * @param $token
     * @param $linkUser
     * @param $nodeMarker
     * @param $limit
     */
    public function getHistoricalMessage($token, $linkUser, $nodeMarker, $limit)
    {
        $user = app(UsersService::class)->getUserByToken($token);
        if (empty($user)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }
        $lists = $this->getMessage($user, $linkUser, $nodeMarker, $limit);

        //获取对方最后一次阅读的时间
        $lastReadTime = $this->getLastReadTime($user, $linkUser);

        $listsNew = array_map(function ($data) use ($user, $lastReadTime) {
            $data['send_time'] = $data['created_at'];
            if ($data['from_uid'] === $user->uid) {
                $data['message_direction'] = 1;
            }
            $content = json_decode($data['content'], true);
            //iOS 闪退临时处理(当解析为null会闪退)
            $content = empty($content) ? ['content' => '', 'extra' => null] : $content;
            $data['content'] = $content;
            //设置未读标识
            if ($data['created_at'] > $lastReadTime && $data['created_at'] > '2020-08-04 10:00:00' && $data['from_uid'] == $user->uid) {
                $data['read'] = 0; //1已读0未读
            } else {
                $data['read'] = 1;
            }
            return Message::create($data)->data;
        }, $lists['data']);

        return ['data' => $listsNew, 'total' => $lists['total']];
    }

    /**
     * 发消息.
     *
     * @param $token
     * @param $type
     * @param $targetUid
     * @param $content
     * @param $push
     * @return bool
     */
    public function send($token, $type, $targetUid, $content, $push)
    {
        $user = app(UsersService::class)->getUserByToken($token);
        if (empty($user)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }

        //檢測賬號是否可用
        $checkUser = $this->checkUserStatus($user->app_id, $user->uid);
        if (! $checkUser) {
            $res = $this->sendTask($user->app_id, $token, $type, $targetUid, $content, $push);
            if (! $res) {
                $this->langKey = 'notice.server_error';
                return false;
            }
            // $task = new SendTask($token, $type, $targetUid, $content, $push);
            // Task::deliver($task);
        }
//        else{
//            //被拉黑
//            $this->langKey = 'notice.account_disabled';
//            return false;
//        }

        $conversation = Message::conversation($user->app_id, $user->uid, $targetUid);
        //格式化消息体
        $data = [
            'msg_id' => 1,
            'conversation' => $conversation,
            'content' => json_decode($content, true),
            'type' => $type,
            'arrivals_callback' => 0,
            'message_direction' => 1,
        ];
        return Message::create($data)->data;
    }

    public function sendByApps($appId, $fromUid, $type, $targetUid, $content, $push)
    {
        //檢測賬號是否可用
        $checkUser = $this->checkUserStatus($appId, $fromUid);
        if (! $checkUser) {
            //根据 uid 找到 token
            $token = Redis::get(sprintf('%s:%s', config('im.uid_token'), $appId . '_' . $fromUid));
            //投递 task 任务
            $res = $this->sendTask($appId, $token, $type, $targetUid, $content, $push);
            if (! $res) {
                $this->langKey = 'notice.server_error';
                return false;
            }
            // $task = new SendTask($token, $type, $targetUid, $content, $push);
            // Task::deliver($task);
        }

        //格式化消息体
        $data = [
            'msg_id' => 1,
            'content' => json_decode($content, true),
            'type' => $type,
            'arrivals_callback' => 0,
            'message_direction' => 1,
        ];
        return Message::create($data)->data;
    }

    /**
     * 消息到达回调.
     *
     * @param $token
     * @param $msgId
     */
    public function messageArrival($token, $msgId)
    {
        $task = new MessageArrivalTask($token, $msgId);
        Task::deliver($task);
    }

    /**
     * 在线广播.
     *
     * @param $token
     * @return bool
     */
    public function onlineNotice($token)
    {
        $task = new OnLineNoticeTask($token);
        Task::deliver($task);
    }

    /**
     * 发送已读通知.
     *
     * @param mixed $uid
     * @param mixed $appId
     * @param mixed $fromUid
     */
    public function sendReadMsg($uid, $appId, $fromUid)
    {
        $clientUid = $this->getBindUid($appId, $uid);
        $targetUid = $uid;
        $sendReadMsgTask = new SendReadMsgTask($clientUid, $fromUid, $targetUid);
        Task::deliver($sendReadMsgTask);
        return true;
    }

    public function messageTransfer($fromUId, $targetUid, $type, $content, $createdAt, $appId)
    {
        $task = new messageTransferTask($fromUId, $targetUid, $type, $content, $createdAt, $appId);
        Task::deliver($task);
        return true;
    }

    public function messageSynchronization($token, $fromUid, $limit)
    {
        $user = app(UsersService::class)->getUserByToken($token);
        if (empty($user)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }

        //不能同步自己
        if ($user->uid == $fromUid) {
            $this->langKey = 'notice.parameter_error';
            return false;
        }

        //开始同步(把 $fromUid 的消息复制一份给 $token 对应的用户,投递异步任务)
        $messageSynchronization = new MessageSynchronization($user, $fromUid, $limit);
        Task::deliver($messageSynchronization);
        return true;
    }

    public function pictureUpload($picture, $token)
    {
        $user = app(UsersService::class)->getUserByToken($token);
        if (empty($user)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }

        //扩展名
        $ext = $picture->extension();
        if (! in_array($ext, config('image.ext'))) {
            $this->langKey = 'notice.ext_error';
            return false;
        }
        //大小
        $filesize = $picture->getClientSize();
        if ($filesize > config('image.size')) {
            $this->langKey = 'notice.size_error';
            return false;
        }

        $picturePath = $user->app_id . '/' . date('Ymd');
        $imageName = $user->uid . '_' . time() . mt_rand(1, 1000);

        //原图
        $originalPicture = $picturePath . '/' . $imageName . '.' . $ext;
        //缩略图
        $thumbnailPicture = $picturePath . '/' . $imageName . '_thumb.' . $ext;

        $height = ImageManagerStatic::make($picture)->height();
        $width = ImageManagerStatic::make($picture)->width();

        $thumbnailSize = config('image.thumbnail_size');
        if ($height > $width) {
            $newHeight = $thumbnailSize;
            $newWidth = $thumbnailSize * $width / $height;
        } else {
            $newWidth = $thumbnailSize;
            $newHeight = $thumbnailSize * $height / $width;
        }

        $imageThum = ImageManagerStatic::make($picture)->resize($newWidth, $newHeight)->stream($ext, 80);

        $s3 = Storage::disk('s3');
        $s3->put($originalPicture, file_get_contents($picture));
        $s3->put($thumbnailPicture, $imageThum);

        $imgUrl = $s3->url($originalPicture);
        $thumbnailUrl = $s3->url($thumbnailPicture);

        return ['img_url' => $imgUrl, 'thumbnail_url' => $thumbnailUrl];
    }

    public function delLiaisonPerson($token, $targetUid)
    {
        $user = app(UsersService::class)->getUserByToken($token);
        if (empty($user)) {
            $this->langKey = 'notice.user_not_exists';
            return false;
        }
        //记录删除的位置
        $this->delMessageRemark($user, $targetUid);
        //联络人,最后一条消息,消息提醒删除
        $this->delLiaisonPersonOfRedis($user, $targetUid);

        return true;
    }

    protected function sendTask($appId, $token, $type, $targetUid, $content, $push)
    {
        dispatch(new SendMessageJob($token, $type, $targetUid, $content, $push))->onConnection('sender');   // or add method: ->onQueue('app_id_'.$appId)
        return true;
    }

    private function getMessage($user, $linkUser, $nodeMarker, $limit)
    {
        $conversation = [
            $this->getBindUid($user->app_id, $user->uid),
            $this->getBindUid($user->app_id, $linkUser),
        ];
        sort($conversation);
        if ($conversation[0] === $user->app_id . '_' . $user->uid) {
            $valueIn = [0, 2];
        } else {
            $valueIn = [0, 1];
        }
        $conversationStr = implode(',', $conversation);

        $lists = MessageModel::select(
            'id',
            'msg_id',
            'from_uid',
            'target_uid',
            'type',
            'content',
            'created_at',
            'status'
        )->where('conversation', $conversationStr)->when(
                $nodeMarker,
                function ($query) use ($nodeMarker) {
                $query->where('id', '<', $nodeMarker);
            }
            )
            ->whereIn('del_status', $valueIn)
            ->orderBy('id', 'desc')
            ->paginate($limit)->toArray();
        //总条数
        $lists['total'] = MessageModel::where('conversation', $conversationStr)->whereIn('del_status', $valueIn)->count();
        return $lists;
    }

    private function getLastReadTime($user, $linkUser)
    {
        $cacheKeyOfLastRead = sprintf('%s:%d_%s', config('im.last_read'), $user->app_id, $linkUser);
        $res = Redis::hGet($cacheKeyOfLastRead, $user->uid);
        if (empty($res)) {
            //从未读过
            return '1970-01-01 12:00:00';
        }
        return $res;
    }

    private function checkUserStatus($appId, $uid)
    {
        $res = Redis::SISMEMBER(sprintf('%s_%s', 'blacklist', $appId), $uid);
        return empty($res) ? false : true;
    }

    private function delMessageRemark($user, $targetUid)
    {
        $conversation = [
            $this->getBindUid($user->app_id, $user->uid),
            $this->getBindUid($user->app_id, $targetUid),
        ];
        sort($conversation);
        if ($conversation[0] === $user->app_id . '_' . $user->uid) {
            $value = 1;
            $valueIn = [0, 2];
        } else {
            $value = 2;
            $valueIn = [0, 1];
        }
        $conversationStr = implode(',', $conversation);
        //删除对应消息
        MessageModel::where('conversation', $conversationStr)->whereIn('del_status', $valueIn)->increment(
            'del_status',
            $value
        );
    }

    private function delLiaisonPersonOfRedis($user, $targetUid)
    {
        //从集合中删除联络人
        Redis::ZREM(sprintf('%s_%s', config('im.last_msg_set') . ':' . $user->app_id, $user->uid), $targetUid);
        //从 hash 表删除最后一条消息
        $hashKey = sprintf('%s_%s', config('im.last_msg') . ':' . $user->app_id, $user->uid);
        Redis::HDEL($hashKey, $targetUid);
        //从 hash 表删除消息提醒
        Redis::HDEL(sprintf('%s_%s', config('im.msg_count') . ':' . $user->app_id, $user->uid), $targetUid);
    }
}
