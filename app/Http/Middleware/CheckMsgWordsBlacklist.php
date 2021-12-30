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
namespace App\Http\Middleware;

use App\Services\UsersService;
use Closure;

/**
 * 检查消息黑名单关键词.
 */
class CheckMsgWordsBlacklist
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->get('token');
        $content = $request->get('content');
        $type = strtolower($request->get('type'));

        if (strcmp($type, 'msg:txt') != 0) {
            return $next($request);
        }

        $content = preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', $content);
        $msg = json_decode($content, true);
        if (! is_array($msg) || empty($msg) || empty($msg['content'])) {
            return $next($request);
        }

        $userService = new UsersService();
        $user = $userService->getUserByToken($token);
        if (! $user || ! $user->app_id) {
            return $next($request);
        }

        $blacklist = imConfig(config('im.msg_words_blacklist'), $user->app_id);
        if (! $blacklist || ! is_array($blacklist)) {
            return $next($request);
        }

        $blacklist = array_map('trim', $blacklist);
        $blacklist = array_filter(array_unique($blacklist));
        $content = str_replace([' ', '   ', '　', PHP_EOL, "\t"], '', $msg['content']);
        $content = $this->numberConverter($content);
        foreach ($blacklist as $word) {
            if (mb_stripos($content, $word, 0, 'utf8') !== false) {
                // 直接返回消息發送成功，製造假象
                $err = __('notice.message.send_success');
                $data = [
                    'msg_id' => 1,
                    'from_uid' => '',
                    'target_uid' => '',
                    'type' => 'Msg:Txt',
                    'content' => $msg,
                    'send_time' => date('Y-m-d H:i:s'),
                    'status' => 1,
                    'arrivals_callback' => 0,
                    'message_direction' => 1,
                ];
                info('msg_words_blacklist', [
                    'argv' => $request->all(),
                    'header' => $request->header(),
                ]);
                return response()->json([
                    'code' => $err['code'],
                    'message' => $err['message'],
                    'data' => $data,
                ]);
            }
        }

        return $next($request);
    }

    private function numberConverter($content)
    {
        $replace = [
            '〇' => 0,
            '一' => 1,
            '二' => 2,
            '三' => 3,
            '四' => 4,
            '五' => 5,
            '六' => 6,
            '七' => 7,
            '八' => 8,
            '九' => 9,
            '零' => 0,
            '壹' => 1,
            '貳' => 2,
            '叁' => 3,
            '肆' => 4,
            '伍' => 5,
            '陸' => 6,
            '柒' => 7,
            '捌' => 8,
            '玖' => 9,
        ];
        return str_replace(array_keys($replace), array_values($replace), $content);
    }
}
