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
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('app_id')->default(0)->comment('应用id');
            $table->string('uid', 100)->default('')->comment('用户唯一标识');
            $table->string('nickname', 100)->default('')->comment('昵称');
            $table->string('avatar')->default('')->comment('头像');
            $table->timestamps();
            $table->mediumText('extend')->comment('扩展字段');
            $table->index(['app_id', 'uid']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
