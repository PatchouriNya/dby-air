<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id();

            $table->integer('account_id',false)->index('account_id')->comment('账号id');

            $table->string('account',50)->comment('账号名');

            $table->integer('client_id',false)->index('client_id')->nullable()->comment('客户id');

            $table->string('client',255)->nullable()->comment('客户名');

            $table->tinyInteger('type',false)->comment('日志类型,1为登录日志,2为空调操控日志');

            $table->text('content')->comment('日志内容');

            $table->string('ip',255)->comment('ip地址');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
