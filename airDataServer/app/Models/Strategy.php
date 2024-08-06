<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Strategy
 *
 * @property int $id
 * @property int $client_id 所属客户ID
 * @property string $name
 * @property string $info 策略概述
 * @property string $power_state 开机状态
 * @property string $operation_mode 运行模式
 * @property string $wind_speed 风速
 * @property string $wind_mode 风向模式
 * @property string $set_temperature 设置温度
 * @property int|null $delta ±区间
 * @property string|null $electrify_state 通电状态
 * @property string|null $start_date 开始日期
 * @property string|null $end_date 结束日期
 * @property string $start_time 开始时间
 * @property string $end_time 结束时间
 * @property int $interval_time 执行间隔时间,单位分钟
 * @property string $week_days 运行周期
 * @property int $status 是否在运行1:在运行 0:停止
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy query()
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereDelta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereElectrifyState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereIntervalTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereOperationMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy wherePowerState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereSetTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereWeekDays($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereWindMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereWindSpeed($value)
 * @mixin \Eloquent
 */
class Strategy extends Model
{
    use HasFactory;
}
