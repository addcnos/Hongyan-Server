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
 */
namespace App\Providers;

use App\Services\Im\FdManager;
use App\Services\Im\StatusManager;
use App\Services\Im\TokenManager;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // $this->app->singleton('im.fd', function () {
        //     return new FdManager();
        // });
        // $this->app->singleton('im.status', function () {
        //     return new StatusManager();
        // });
        // $this->app->singleton('im.token', function () {
        //     return new TokenManager();
        // });
    }
}
