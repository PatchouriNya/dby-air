<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientAccountRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_account_relationships', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id',false)->index('client_id')->comment('客户id');
            $table->integer('account_id',false)->index('account_id')->comment('属于该客户的账号id');
            $table->unique(['client_id','account_id']);
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
        Schema::dropIfExists('client_account_relationships');
    }
}
