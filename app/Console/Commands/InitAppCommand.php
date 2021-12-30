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
namespace App\Console\Commands;

use App\Models\AppsModel;
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

        $data = [
            'name' => 'app_demo',
            'key' => '94a91dd7d8d67e7191d4ddeab6e2c11d5e3a2b37',
            'secret' => '3881e30fb8e7cbdc77560fd4c6bce1b822d528e3',
            'description' => 'app_demo is a demo app.',
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        AppsModel::firstOrCreate(['id' => 1], $data);

        $this->info('init app success.');
    }
}
