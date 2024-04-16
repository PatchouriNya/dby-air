<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\User\Create_record
 *
 * @property int $corporation_id id信息
 * @property string|null $company_name 公司名称
 * @property string|null $avatar_url 头像路径
 * @property string|null $user_name 昵称
 * @property int|null $parentId 上一级id
 * @property string|null $province 省份
 * @property string|null $city 城市
 * @property string|null $district 区
 * @property string|null $scope1 一级
 * @property string|null $scope2 二级
 * @property string|null $scope3 三级
 * @property string|null $scope4 四级
 * @property string|null $scope5 五级
 * @property int|null $type type
 * @property string|null $flag 升级权限标志位
 * @property string|null $sub_accounts_type 子账号标志记录
 * @property string|null $name 账号信息
 * @property string|null $password 账号密码
 * @property string|null $air_conditioning_power 空调功率
 * @property string|null $Air_startup_temperature 开机温度
 * @property string|null $Air_Boot_quantity 开机数量
 * @property string|null $dtu_quantity DTU数量-DTU串口归属-DTU 1-128归属地
 * @property string|null $air_quantity 空调数量
 * @property int|null $company_state 账号是否停用状态
 * @property int|null $Account_offline_status 账号离线状态
 * @property string|null $createAt 最新创建时间
 * @property string|null $updateAt 最新更新时间
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record query()
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereAccountOfflineStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereAirBootQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereAirConditioningPower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereAirQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereAirStartupTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereCompanyState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereCorporationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereCreateAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereDtuQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereFlag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereScope1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereScope2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereScope3($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereScope4($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereScope5($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereSubAccountsType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereUpdateAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Create_record whereUserName($value)
 * @mixin \Eloquent
 */
class Create_record extends Model
{
    protected $table = 'create_record';

    //用户列表
    public function user_info(int $size = 6)
    {
        return $this->orderBy('corporation_id','asc')->limit($size)->get();
    }

    public function getUserById($id)
    {
        return $this->where('corporation_id',$id)->get();
    }
}
