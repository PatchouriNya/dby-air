<?php

namespace App\Models\Client;

use App\Models\Air\Air_detail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Client\Air_group_relationship
 *
 * @property int $id
 * @property int $air_id 空调ID
 * @property int $group_id 分组ID
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group_relationship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group_relationship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group_relationship query()
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group_relationship whereAirId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group_relationship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group_relationship whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group_relationship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Air_group_relationship whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Air_group_relationship extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at'];

    public function airDetail()
    {
        return $this->belongsTo(Air_detail::class, 'air_id', 'id');
    }

    use HasFactory;
}
