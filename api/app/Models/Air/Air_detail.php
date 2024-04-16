<?php

namespace App\Models\Air;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Air_detail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $hidden = ['client_id','created_at','updated_at','deleted_at'];
    protected $fillable = [
        'designation',
        'responsible_person',
        'air_brand',
        'online_state',
        'electrify_state',
        'power_state',
        'operation_mode',
        'wind_speed',
        'wind_mode',
        'set_temperature',
        'room_temperature',
        'voltage',
        'electric_current',
        'power',
        'electric_quantity',
        'standby_mode'
    ];
    public function getOneClient($id, $pageSize, $filters = [])
    {
        $query = $this->where('client_id', $id);

        // 根据传入的筛选条件动态构建查询
        foreach ($filters as $column => $value) {
            if (!empty($value)) {
                $query->where($column, 'like', '%' . $value . '%');
            }
        }
        return $query->paginate($pageSize);
    }

    public function updateData(array $data)
    {
        $this->update($data);
        return $this;
    }
}
