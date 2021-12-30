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
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Indicates if facade aliases are enabled for the console, default true.
     *
     * @var bool
     */
    protected $aliases = false;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\OverdueMessages::class,
        Commands\InitAppCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('OverdueMessages')->dailyAt('04:30')->withoutOverlapping();
    }
}
