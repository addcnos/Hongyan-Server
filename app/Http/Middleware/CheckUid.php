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

use Closure;

/**
 * 注册/登录时检查uid是否合法.
 */
class CheckUid
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $uid = $request->get('uid');
        if (strpos($uid, '_') !== false || strpos($uid, ',') !== false) {
            $err = __('notice.uid_error');
            return response()->json([
                'code' => $err['code'],
                'msg' => $err['message'],
                'data' => null,
            ]);
        }

        return $next($request);
    }
}
