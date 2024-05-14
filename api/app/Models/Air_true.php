<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Air_true extends Model
{
    use HasFactory;

    protected $guarded = [];
    // 只允许查看id字段
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'is_grouped', 'designation', 'responsible_person'];
}
