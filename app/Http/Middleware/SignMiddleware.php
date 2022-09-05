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
namespace App\Http\Middleware;

use App\Models\AppsModel;
use Closure;

class SignMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $nonce = $request->header('nonce', '');
        $timeStamp = $request->header('time-stamp', '');
        $sign = $request->header('sign', '');
        $appKey = $request->header('app-key', '');
        $appInfo = AppsModel::where([['key', $appKey], ['status', 1]])->first();

        if (! $appInfo) {
            return response()->json([
                'code' => __('notice.key_error')['code'],
                'msg' => __('notice.key_error')['message'],
            ]);
        }
        $checkResult = $this->signCheck($nonce, $timeStamp, $sign, $appInfo->secret);
        if (! $checkResult) {
            return response()->json([
                'code' => __('notice.sign_error')['code'],
                'message' => __('notice.sign_error')['message'],
            ]);
        }
        $request['app_id'] = $appInfo->id;
        return $next($request);
    }

    /**
     * @param $nonce 随机数
     * @param $timeStamp 时间戳
     * @param $sign 签名字符串
     * @param $secret
     */
    private function signCheck($nonce, $timeStamp, $sign, $secret)
    {
        return sha1($secret . $nonce . $timeStamp) === $sign;
    }
}
