<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Client
 *
 * @property int $id
 * @property string $clientname 客户公司名称
 * @property string|null $province 省
 * @property string|null $city 城
 * @property string|null $district 区
 * @property int|null $pid 父级id,默认为0代表顶级
 * @property int $type 判断类型，0为目录，1为可点击
 * @property int $level 层级,默认为1级
 * @property string|null $info
 * @property string|null $com_port 串口
 * @property string|null $start_address 16进制起始地址
 * @property int|null $total_air 总空调数量
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereComPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereStartAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereTotalAir($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory;
}
