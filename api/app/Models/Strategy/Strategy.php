<?php

namespace App\Models\Strategy;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Strategy\Strategy
 *
 * @property int $id
 * @property string $name
 * @property string $info 策略概述
 * @property string $power_state 开机状态
 * @property string $operation_mode 运行模式
 * @property string $wind_speed 风速
 * @property string $wind_mode 风向模式
 * @property string $set_temperature 设置温度
 * @property string|null $electrify_state 通电状态
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy query()
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereElectrifyState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereOperationMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy wherePowerState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereSetTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereWindMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Strategy whereWindSpeed($value)
 * @mixin \Eloquent
 */
class Strategy extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];
    use HasFactory;


}
