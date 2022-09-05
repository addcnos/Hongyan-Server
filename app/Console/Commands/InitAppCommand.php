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
namespace App\Console\Commands;

use App\Models\AppsModel;
use App\Services\AppsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InitAppCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'init-app';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '初始建構';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('start init app...');
        
        // 1. php artisan migrate
        // 2. INSERT INTO `im_apps` ....

        // Artisan::call('migrate');

        $name = 'demo_app';
        $description = 'a demo app';
        (new AppsService())->create($name, $description);

        $this->info('init app success.');
    }
}
