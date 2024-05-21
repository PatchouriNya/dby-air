<?php

namespace App\Models\Client;

use App\Models\Strategy\Strategy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Client\Air_group
 *
 * @property int $id
 * @property string $name
 * @property string|null $info
 * @property int $client_id 关联的客户ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group query()
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group whereUpdatedAt($value)
 * @property int|null $strategy_id 关联的策略ID
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group whereStrategyId($value)
 * @mixin \Eloquent
 */
class Air_group extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];
    use HasFactory;

    public function withStrategy()
    {
        //        return $this->hasOne(Strategy::class, 'id', 'strategy_id');
    }

    // 定义 strategy_id 字段的访问器
    public function getStrategyIdAttribute($value)
    {
        return json_decode($value);
    }
}
