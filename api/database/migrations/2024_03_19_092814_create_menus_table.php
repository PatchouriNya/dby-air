<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name',50)->comment('名称');
            $table->bigInteger('pid',false)->default(0)->comment('父级id,默认为0代表顶级');
            $table->tinyInteger('sort',false)->default(1)->comment('显示顺序');
            $table->string('icon',50)->nullable()->comment('图标');
            $table->string('url',50)->nullable()->comment('路由');
            $table->string('permission',100)->nullable()->comment('权限');
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
        Schema::dropIfExists('menuses');
    }
}
