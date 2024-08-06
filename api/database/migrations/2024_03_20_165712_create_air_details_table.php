<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('air_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id', false)->comment('客户公司id,用于关联表');
            $table->integer('show_id', false)->comment('表格中展示的id');
            $table->string('read_base_address', 20)->nullable()->comment('读取时寄存器起始地址');
            $table->string('write_base_address', 20)->nullable()->comment('写入时寄存器起始地址');
            $table->string('designation', 255)->nullable()->comment('名称');
            $table->string('responsible_person', 255)->nullable()->comment('责任人');
            $table->string('air_brand', 255)->nullable()->comment('品牌');
            $table->string('online_state', 255)->nullable()->comment('在线状态');
            $table->string('electrify_state', 255)->nullable()->comment('通电状态');
            $table->string('power_state', 255)->nullable()->comment('开机状态');
            $table->string('operation_mode', 255)->nullable()->comment('运行模式');
            $table->string('wind_speed', 255)->nullable()->comment('风速');
            $table->string('wind_mode', 255)->nullable()->comment('风向模式');
            $table->string('set_temperature', 255)->nullable()->comment('设置温度');
            $table->string('room_temperature', 255)->nullable()->comment('室温');
            $table->string('voltage', 255)->nullable()->comment('电压');
            $table->string('electric_current', 255)->nullable()->comment('电流');
            $table->string('power', 255)->nullable()->comment('功率');
            $table->string('electric_quantity', 255)->nullable()->comment('电量读取');
            $table->tinyInteger('standby_mode', false)->nullable()->comment('待机状态');
            $table->unsignedTinyInteger('is_grouped', false)->default(0)->comment('是否已被分组,是为1,否为0');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('air_details');
    }
}
