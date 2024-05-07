<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStrategiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('info')->comment('策略概述');
            $table->string('power_state', 255)->comment('开机状态');
            $table->string('operation_mode', 255)->comment('运行模式');
            $table->string('wind_speed', 255)->comment('风速');
            $table->string('wind_mode', 255)->comment('风向模式');
            $table->string('set_temperature', 255)->comment('设置温度');
            $table->string('electrify_state', 255)->nullable()->comment('通电状态');
            $table->string('start_time', 30)->comment('开始时间');
            $table->string('end_time', 30)->comment('结束时间');
            $table->tinyInteger('interval_time')->default(5)->comment('执行间隔时间,单位分钟');
            $table->tinyInteger('status')->default(0)->comment('是否在运行1:在运行 0:停止');
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
        Schema::dropIfExists('strategies');
    }
}
