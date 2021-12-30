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
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->default('')->comment('应用名称');
            $table->string('key', 50)->default('')->comment('应用key');
            $table->string('secret', 50)->default('')->comment('应用secret');
            $table->string('description')->default('')->comment('应用描述');
            $table->string('callback_url')->default('')->comment('回调地址');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态：0未启用、1启用中、2已停用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('apps');
    }
}
