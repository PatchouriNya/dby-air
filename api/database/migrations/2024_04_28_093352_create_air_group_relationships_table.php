<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirGroupRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('air_group_relationships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('air_id')->comment('空调ID');
            $table->unsignedBigInteger('group_id')->comment('分组ID');
            // 设备外键
            $table->foreign('air_id')->references('id')->on('air_details')->onDelete('cascade');
            // 分组外键
            $table->foreign('group_id')->references('id')->on('air_groups')->onDelete('cascade');
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
        Schema::dropIfExists('air_group_relationships');
    }
}
