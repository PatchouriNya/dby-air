<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Air_group
 *
 * @property int $id
 * @property string $name
 * @property string|null $info
 * @property int $client_id 关联的客户ID
 * @property string|null $strategy_id 关联的策略ID
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
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group whereStrategyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Air_group extends Model
{
    use HasFactory;
}
