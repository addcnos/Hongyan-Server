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
namespace App\Tasks;

use App\Models\MessageModel;
use App\Services\UsersService;
use Hhxsv5\LaravelS\Swoole\Task\Task;

class MessageArrivalTask extends Task
{
    private $token = '';

    private $msgId = '';

    public function __construct($token, $msgId)
    {
        $this->token = $token;
        $this->msgId = $msgId;
    }

    public function handle()
    {
        $usersService = new UsersService();
        $user = $usersService->getUserByToken($this->token);
        if (empty($user)) {
            return;
        }

        $fromUid = $user->app_id . '_' . $user->uid;
        MessageModel::where([['msg_id', $this->msgId], ['target_uid', $fromUid]])->update(['status' => 2]);
    }
}
