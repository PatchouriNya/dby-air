<?php

namespace App\Models\Log;

use App\Models\Account\Account;
use App\Models\Client\Client;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Log\Log
 *
 * @property int $id
 * @property int $account_id 账号id
 * @property string $account 账号名
 * @property int|null $client_id 客户id
 * @property string|null $client 客户名
 * @property int $type 日志类型,1为登录日志,2为空调操控日志
 * @property string $content 日志内容
 * @property string $ip ip地址
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereClient($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Log extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function account_detail()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function client_detail()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');

    }
}
