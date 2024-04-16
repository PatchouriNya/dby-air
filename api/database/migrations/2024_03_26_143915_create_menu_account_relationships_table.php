<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuAccountRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_account_relationships', function (Blueprint $table) {
            $table->id();
            $table->integer('account_id',false)->index('account_id')->comment('可以使用该菜单的账号id');
            $table->integer('menu_id',false)->index('menu_id')->comment('菜单id');
            $table->unique(['menu_id','account_id']);
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
        Schema::dropIfExists('menu_account_relationships');
    }
}
