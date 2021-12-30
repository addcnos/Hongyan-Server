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
namespace App\Console\Commands;

use App\Models\MessageModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class OverdueMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'OverdueMessages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '过期消息删除';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        MessageModel::where('created_at', '<', date('Y-m-d H:i:s', strtotime('-1 years')))->groupBy('conversation')->chunkById(200, function ($messages) {
            foreach ($messages as $message) {
                $this->delLastMsgSet($message);
            }
        });
        //从mysql物理删除消息(每次删除1000条)
        MessageModel::where('created_at', '<', date('Y-m-d H:i:s', strtotime('-1 years')))->chunkById(
            1000,
            function ($messages) {
                $delIds = $messages->map(function ($item, $key) {
                    return $item['id'];
                });
                $delIds = $delIds->all();
                MessageModel::whereIn('id', $delIds)->delete();
            }
        );
    }

    private function delLastMsgSet($message)
    {
        $count = MessageModel::where([
            ['conversation', $message->conversation],
            ['created_at', '>=', date('Y-m-d H:i:s', strtotime('-1 years'))],
        ])->count();
        if (empty($count)) {
            //一年内未产生新的对话
            $conversation = explode(',', $message->conversation);
            $user1 = explode('_', $conversation[0]);
            $user2 = explode('_', $conversation[1]);
            //从各自的联络人集合中删除
            Redis::ZREM(sprintf('%s_%s', config('im.last_msg_set') . ':' . $user1[0], $user1[1]), $user2[1]);
            Redis::ZREM(sprintf('%s_%s', config('im.last_msg_set') . ':' . $user2[0], $user2[1]), $user1[1]);
            //消息提醒删除
            Redis::hdel(sprintf('%s_%s', config('im.msg_count') . ':' . $user1[0], $user1[1]), $user2[1]);
            Redis::hdel(sprintf('%s_%s', config('im.msg_count') . ':' . $user1[0], $user2[1]), $user1[1]);
            //最后一条消息删除
            Redis::hdel(sprintf('%s_%s', config('im.last_msg') . ':' . $user1[0], $user1[1]), $user2[1]);
            Redis::hdel(sprintf('%s_%s', config('im.last_msg') . ':' . $user1[0], $user2[1]), $user1[1]);
        }
    }
}
