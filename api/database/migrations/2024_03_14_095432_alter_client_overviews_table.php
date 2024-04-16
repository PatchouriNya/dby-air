<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterClientOverviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('client_overviews', function (Blueprint $table) {
            $table->integer('air_quantity',false)->nullable()->comment('空调数量')->change();
            $table->integer('air_boot_quantity',false)->nullable()->comment('开机数量')->change();
            $table->integer('air_startup_temperature',false)->nullable()->comment('开机温度')->change();
            $table->integer('air_conditioning_power',false)->nullable()->comment('空调功率')->change();
            $table->integer('dtu_quantity',false)->nullable()->comment('DTU数量-DTU串口归属-DTU 1-128归属地')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('client_overviews', function (Blueprint $table) {
            //
        });
    }
}
