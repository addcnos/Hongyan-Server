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

use App\Services\UsersService;
use Closure;

/**
 * 检查是否已登录.
 */
class CheckLogined
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

        $userService = new UsersService();

        if ($userService->isLogined($token) == false) {
            $err = __('notice.user_not_exists');
            return response()->json([
                'code' => $err['code'],
                'msg' => $err['message'],
                'data' => null,
            ]);
        }

        return $next($request);
    }
}
