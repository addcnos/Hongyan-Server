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
//定义https前缀
$httpPrx = 'http://';

//判断环境标识
if (env('APP_ENV') == 'dev') {
    $httpPrx = 'http://';
    $sign = '.dev';
} elseif (env('APP_ENV') == 'debug') {
    $sign = '.debug';
} else {
    $sign = '';
}

return [
    'base_url' => $httpPrx . 'im' . $sign . '.591.com.hk',  //基准url
];
