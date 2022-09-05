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
namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $log = app('Psr\Log\LoggerInterface');
        $log->debug('request', [
            'url' => $request->url(),
            'method' => $request->method(),
            'argv' => $request->all(),
            'header' => $request->header(),
        ]);
        $log->error($exception->__toString());

        $data = __('notice.server_error');
        if ($exception instanceof ValidationException) {
            $error = $exception->errors();
            if (is_array($error) && $error) {
                $data = ['code' => 4100, 'message' => reset($error)[0] ?? $data['message']];
            }
            return response()->json($data);
        }
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
            return response()->json(['code' => 401, 'message' => $exception->getMessage() ?: '未登入'])->setStatusCode(401);
        }
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->json(['code' => 404, 'message' => '資料不存在！'])->setStatusCode(404);
        }
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return response()->json(['code' => 405, 'message' => '操作錯誤，請確認請求方式！'])->setStatusCode(405);
        }
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException) {
            return response()->json(['code' => 503, 'message' => '系統異常，稍後再試！'])->setStatusCode(503);
        }
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
            return response()->json(['code' => $exception->getStatusCode(), 'message' => '系統異常，請稍後再試！'])->setStatusCode($exception->getStatusCode());
        }

        return response()->json($data);
        // return parent::render($request, $exception);
    }
}
