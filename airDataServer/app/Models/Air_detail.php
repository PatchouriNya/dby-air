<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Air_detail
 *
 * @property int $id
 * @property int $client_id 客户公司id,用于关联表
 * @property int|null $show_id 项目内部号
 * @property string|null $designation 空调位置
 * @property string|null $responsible_person 责任人
 * @property string|null $air_brand 空调品牌
 * @property string|null $online_state 在线状态
 * @property string|null $electrify_state 通电状态
 * @property string|null $power_state 开机状态
 * @property string|null $operation_mode 运行模式
 * @property string|null $wind_speed 风速
 * @property string|null $wind_mode 风向模式,1为走风,2为扫风
 * @property string|null $set_temperature 设置温度
 * @property string|null $room_temperature 室温
 * @property string|null $voltage 电压
 * @property string|null $electric_current 电流
 * @property string|null $power 功率
 * @property string|null $electric_quantity 电量读取
 * @property int|null $standby_mode 待机状态
 * @property int|null $is_grouped 是否已被分组,是为1,否为0
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail query()
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereAirBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereDesignation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereElectricCurrent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereElectricQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereElectrifyState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereIsGrouped($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereOnlineState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereOperationMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail wherePower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail wherePowerState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereResponsiblePerson($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereRoomTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereSetTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereShowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereStandbyMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereVoltage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereWindMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereWindSpeed($value)
 * @property string|null $read_base_address 读取时寄存器起始地址
 * @property string|null $write_base_address 写入时寄存器起始地址
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereReadBaseAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_detail whereWriteBaseAddress($value)
 * @mixin \Eloquent
 */
class Air_detail extends Model
{
    use HasFactory;
    protected $guarded = [];

}
