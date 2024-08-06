<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Client_overview
 *
 * @property int $id
 * @property int $client_id 客户公司id,用于关联表
 * @property int|null $air_quantity 空调数量
 * @property int|null $air_boot_quantity 开机数量
 * @property int|null $air_startup_temperature 开机温度
 * @property int|null $air_conditioning_power 空调功率
 * @property int|null $dtu_quantity DTU数量-DTU串口归属-DTU 1-128归属地
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview whereAirBootQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview whereAirConditioningPower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview whereAirQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview whereAirStartupTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview whereDtuQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_overview whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Client_overview extends Model
{
    use HasFactory;
}
