<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('clientname', 50)->unique()->comment('客户公司名称');
            $table->string('province', 10)->comment('省');
            $table->string('city', 10)->comment('城');
            $table->string('district', 10)->comment('区');
            $table->bigInteger('pid', false)->default(0)->comment('父级id,默认为0代表顶级');
            $table->tinyInteger('level', false)->default(1)->comment('0为目录，1为可点击');
            $table->string('host_address', 20)->nullable()->comment('主机地址');
            $table->string('com_port', 10)->nullable()->comment('串口');
            $table->string('start_address', 20)->nullable()->comment('16进制起始地址');
            $table->integer('total_air', false)->nullable()->comment('总空调数量');
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
        Schema::dropIfExists('clients');
    }
}
