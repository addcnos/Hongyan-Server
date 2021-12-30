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
require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
 */

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades(false);

$app->withEloquent();

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
 */

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

//跨域中间件
$app->middleware([
    \Barryvdh\Cors\HandleCors::class,
    //    App\Http\Middleware\SignMiddleware::class, //验签中间件
]);
//验签中间件
$app->routeMiddleware([
    'sign' => App\Http\Middleware\SignMiddleware::class,
    'checkSignature' => App\Http\Middleware\CheckSignatureMiddleware::class,
    'check.logined' => App\Http\Middleware\CheckLogined::class,
    'check_uid' => App\Http\Middleware\CheckUid::class,
    'checkMsgWordsBlacklist' => App\Http\Middleware\CheckMsgWordsBlacklist::class,
]);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
 */

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
 */

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(Hhxsv5\LaravelS\Illuminate\LaravelSServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(Barryvdh\Cors\ServiceProvider::class);
$app->register(Laravel\Tinker\TinkerServiceProvider::class);
$app->register(Addcnos\GatewayWorker\GatewayWorkerServiceProvider::class);
$app->register(Intervention\Image\ImageServiceProvider::class);

if (class_exists('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider')) {
    $app->register(Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
}

if (class_exists('Addcnos\SwooleIdeHelper\SwooleIdeHelperServiceProvider')) {
    $app->register(Addcnos\SwooleIdeHelper\SwooleIdeHelperServiceProvider::class);
}

if (class_exists('Addcnos\RedisIdeHelper\RedisIdeHelperServiceProvider')) {
    $app->register(Addcnos\RedisIdeHelper\RedisIdeHelperServiceProvider::class);
}

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
 */

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

$app->configure('app');
$app->configure('cors');
$app->configure('database');
$app->configure('gatewayworker');
$app->configure('im');
$app->configure('laravels');
$app->configure('queue');
$app->configure('filesystems');
$app->configure('push');
$app->configure('image');

return $app;
