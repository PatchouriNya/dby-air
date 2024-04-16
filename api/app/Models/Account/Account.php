<?php

namespace App\Models\Account;

use App\Models\Client\Client_account_relationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Account\Account
 *
 * @property int $id
 * @property string $account 账号
 * @property string $password 密码
 * @property string $avatar 头像
 * @property string|null $nickname 昵称
 * @property string|null $email 邮箱
 * @property string|null $mobile 手机号
 * @property string|null $last_login_ip 最近登录的ip
 * @property string|null $last_login_time 最近登录的时间
 * @property string|null $last_logout_time 最近登出的时间
 * @property string|null $sub_account_record 子账号标识记录
 * @property string|null $upgrade_permission_flag 升级权限标志位
 * @property int $account_offline_status 账号是否离线
 * @property int $account_status 账号是否停用
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAccountOfflineStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAccountStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLastLoginIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLastLoginTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLastLogoutTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSubAccountRecord($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpgradePermissionFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Account withoutTrashed()
 * @property-read Client_account_relationship|null $withClient
 * @mixin \Eloquent
 */
class Account extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    public function withClient()
    {
        return $this->hasOne(Client_account_relationship::class,'account_id','id');
    }

    public function getClient($id)
    {
        return $this->with(['withClient:account_id,client_id'])->where('id',$id)->first()->toArray();
    }


}
