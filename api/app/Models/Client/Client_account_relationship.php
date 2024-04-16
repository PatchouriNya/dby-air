<?php

namespace App\Models\Client;

use App\Models\Account\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Client\Client_account_relationship
 *
 * @property int $id
 * @property int $client_id 客户id
 * @property int $account_id 属于该客户的账号id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Client_account_relationship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client_account_relationship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client_account_relationship query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client_account_relationship whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_account_relationship whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_account_relationship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_account_relationship whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_account_relationship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client_account_relationship whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Client_account_relationship extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
    public function getAccountList($id)
    {
        return $this->with('account')->where('client_id',$id,)->get()->toArray();
    }
}
