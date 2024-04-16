<?php

namespace App\Models\Client;

use App\Models\Account\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Client\Client
 *
 * @property int $id
 * @property string $clientname 客户公司名称
 * @property string|null $province 省
 * @property string|null $city 城
 * @property string|null $district 区
 * @property int|null $pid 父级id,若为NULL代表祖先
 * @property int $level 层级,默认为1级
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Client onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Client query()
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereClientname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereProvince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Client withoutTrashed()
 * @property int $type 判断类型，0为目录，1为可点击
 * @property string|null $info
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Client> $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Client> $childsSelect
 * @property-read int|null $childs_select_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client\Client_account_relationship> $withAccount
 * @property-read int|null $with_account_count
 * @property-read \App\Models\Client\Client_overview|null $withOverview
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Client whereType($value)
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];
    public function withAccount()
    {
        return $this->hasMany(Client_account_relationship::class,'client_id','id');
    }
    public function withOverview()
    {
        return $this->hasOne(Client_overview::class,'client_id','id');
    }


    public function children()
    {
        return $this->hasMany(Client::class, 'pid', 'id');
    }

    public static function getAccountClients($accountId, $clientId)
    {
        // 获取账号底下所有的客户以及客户的子客户，排除指定的客户和type为1的客户
        $clients = static::whereHas('withAccount', function ($query) use ($accountId) {
            $query->where('account_id', $accountId);
        })
            ->with(['childsSelect' => function ($query) use ($clientId) {
                $query->where('id', '!=', $clientId); // 排除指定的客户
            }])
            ->where('id', '!=', $clientId) // 排除指定的客户
            ->where('type', '!=', 1) // 排除 type 为 1 的客户
            ->get();

        return $clients;
    }

    public function childsSelect()
    {
        return $this->hasMany(Client::class, 'pid', 'id')
            ->with('childsSelect')
            ->where('type', '!=', 1); // 排除 type 为 1 的客户
    }



    public function childs()
    {
        return $this->children()->with('withOverview')->with('childs');
    }

    public function getOverview($id)
    {
        return $this->with('withOverview')->with('childs')->where('id',$id)->first()->toArray();
    }
    public function getAllChildrenWithOverview($parentId, $pageSize = 15, $keyword = null)
    {
        $query = $this->with('withOverview')->where('pid', $parentId);

        if ($keyword) {
            $query->where(function ($query) use ($keyword) {
                $query->where('clientname', 'like', "%$keyword%")
                    ->orWhereHas('children', function ($query) use ($keyword) {
                        $query->where('clientname', 'like', "%$keyword%");
                    });
            });
        }

        if ($pageSize) {
            $children = $query->paginate($pageSize);
        } else {
            $children = $query->get();
        }

        $result = [];
        foreach ($children as $child) {
            $childData = [
                'id' => $child->id,
                'clientname' => $child->clientname,
                'province' =>$child->province,
                'city' =>$child->city,
                'district' =>$child->district,
                'type' => $child->type,
                'info' =>$child->info,
                'pid'   =>$child->pid,
                'overview' => $child->withOverview ?? null
            ];

            $grandchildren = $this->getAllChildrenWithOverview($child->id, null, $keyword);
            if (!empty($grandchildren)) {
                $childData['children'] = $grandchildren;
            }

            $result[] = $childData;
        }

        if ($pageSize) {
            $children->setCollection(collect($result));
            return $children;
        }

        return $result;
    }




}
