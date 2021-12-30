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
/**
 * 获取毫秒时间戳.
 *
 * @return float
 */
function getMsectime()
{
    [$msec, $sec] = explode(' ', microtime());
    return (float) sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
}

/**
 * 获得配置.
 *
 * @param string $key
 * @param int $appId 应用ID，0 表示通用配置
 * @return array|string
 */
function imConfig($key, $appId = 0)
{
    $imConfig = [];

    if (isset($imConfig[$appId][$key])) {
        return $imConfig[$appId][$key];
    }
    $imConfig[$appId] = app(App\Services\ConfigService::class)->allConfig($appId);
    return isset($imConfig[$appId][$key]) ? $imConfig[$appId][$key] : null;
}

/**
 * 自定义LOG.
 *
 * @param string $driver
 * @param string $message
 * @param array $data
 */
function logging($driver, $message, $data)
{
    app('Psr\Log\LoggerInterface')->driver($driver)->info($message, $data);
}

/**
 * 访问日志.
 *
 * @param string $message
 * @param array $data
 */
function accessLog($message, $data)
{
    logging('access', $message, $data);
}

/**
 * 记录发送消息时的访问日志.
 *
 * @param \Illuminate\Http\Request $request
 * @param array $response
 */
function messageSenderAccessLog($request, $response)
{
    $data = [
        'request' => [
            'url' => $request->url(),
            'method' => $request->method(),
            'argv' => $request->all(),
            'header' => $request->header(),
        ],
        'response' => $response,
    ];
    accessLog('message_send', $data);
}
