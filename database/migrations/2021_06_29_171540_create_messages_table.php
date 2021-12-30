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

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('msg_id', 50)->unique()->comment('消息uuid');
            $table->integer('app_id')->default(0)->comment('应用id');
            $table->string('conversation', 200)->default('')->index()->comment('聊天标识');
            $table->string('from_uid', 100)->default('')->index()->comment('消息发送者');
            $table->string('target_uid', 100)->default('')->index()->comment('消息接受者');
            $table->text('content')->comment('信息内容');
            $table->string('type', 20)->default('')->comment('消息类型');
            $table->tinyInteger('status')->default(0)->comment('0待推;1已推;2已送达;3已读');
            $table->timestamps();
            $table->tinyInteger('del_status')->default(0)->comment('0未删,1A删,2B删,3AB删');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
