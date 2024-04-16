<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientOverviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_overviews', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id',false)->comment('客户公司id,用于关联表');
            $table->integer('air_quantity',false)->default(0)->comment('空调数量');
            $table->integer('air_boot_quantity',false)->default(0)->comment('开机数量');
            $table->integer('air_startup_temperature',false)->default(0)->comment('开机温度');
            $table->integer('air_conditioning_power',false)->default(0)->comment('空调功率');
            $table->integer('dtu_quantity',false)->default(0)->comment('DTU数量-DTU串口归属-DTU 1-128归属地');
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
        Schema::dropIfExists('client_overviews');
    }
}
