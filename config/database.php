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
if (env('APP_ENV') == 'online') {
    $redis = [
        env('REDIS_SENTINEL_1'),
        env('REDIS_SENTINEL_2'),
        'options' => [
            'replication' => 'sentinel',
            'service' => env('REDIS_SENTINEL_SERVICE', 'myredis'),    //sentinel
            'parameters' => [
                'password' => env('REDIS_PASSWORD', null),    //redis的密码,没有时写null
                'database' => 0,
                'persistent' => true,   // 持久連接
            ],
        ],
    ];
} else {
    $redis = [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD', null),
        'port' => env('REDIS_PORT', 6379),
        'database' => env('REDIS_DATABASE', 0),
        // 'read_write_timeout' => 0,
        'persistent' => true,   // 持久連接
    ];
}

return [
    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
     */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
     */

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'read' => [
                'host' => env('DB_HOST_READ'),
            ],
            'write' => [
                'host' => env('DB_HOST_WRITE'),
            ],
            'port' => env('DB_PORT'),
            'database' => env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => env('DB_PREFIX', 'im_'),
            'strict' => false,
            'engine' => null,
            'unix_socket' => env('DB_SOCKET', ''),
            'sticky' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
     */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer set of commands than a typical key-value systems
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
     */

    'redis' => [
        'cluster' => false,
        //        'default'   => [
        //            'host'               => env('REDIS_HOST', '127.0.0.1'),
        //            'password'           => env('REDIS_PASSWORD', null),
        //            'port'               => env('REDIS_PORT', 6379),
        //            'database'           => env('REDIS_DATABASE', 0),
        //            // 'read_write_timeout' => 0,
        //            'persistent'         => true,   // 持久連接
        //        ],
        'default' => $redis,
        'subscribe' => [
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', 6379),
            'database' => env('REDIS_DATABASE', 0),
            // 'read_write_timeout' => 0,
            'persistent' => true,   // 持久連接
        ],
    ],
];
