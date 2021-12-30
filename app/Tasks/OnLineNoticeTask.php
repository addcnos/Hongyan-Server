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

use App\Services\Messages\Message;
use App\Services\UsersService;
use Hhxsv5\LaravelS\Swoole\Task\Task;

class OnLineNoticeTask extends Task
{
    private $token = '';

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function handle()
    {
        try {
            $usersService = new UsersService();
            $user = $usersService->getUserByToken($this->token);
            if (empty($user)) {
                return;
            }
            $data = [
                'type' => 'Ntf:Online',
                'from_uid' => $user->uid,
                'content' => 't',
            ];
            info('OnLineNoticeTask', $data);
            Message::create($data)->sendToGroup($user->app_id);
        } catch (\Throwable $th) {
            app('Psr\Log\LoggerInterface')->error($th->__toString());
        }
    }
}
