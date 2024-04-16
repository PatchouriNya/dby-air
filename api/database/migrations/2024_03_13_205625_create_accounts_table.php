<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account',50)->unique()->comment('账号');
            $table->string('password',255)->comment('密码');
            $table->string('avatar',255)->default('/uploads/default.jpg')->comment('头像');
            $table->string('nickname',15)->nullable()->comment('昵称');
            $table->string('email',30)->nullable()->comment('邮箱');
            $table->char('mobile',11)->nullable()->comment('手机号');
            $table->ipAddress('last_login_ip')->nullable()->comment('最近登录的ip');
            $table->timestamp('last_login_time')->nullable()->comment('最近登录的时间');
            $table->timestamp('last_logout_time')->nullable()->comment('最近登出的时间');
            $table->string('sub_account_record',30)->nullable()->comment('子账号标识记录');
            $table->string('upgrade_permission_flag',30)->nullable()->comment('升级权限标志位');
            $table->boolean('account_offline_status')->default(false)->comment('账号是否离线');
            $table->boolean('account_status')->default(false)->comment('账号是否停用');
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
        Schema::dropIfExists('users');
    }
}
