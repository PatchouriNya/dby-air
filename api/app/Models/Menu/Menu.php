<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Menu\Menu
 *
 * @property int $id
 * @property string $name 名称
 * @property int $pid 父级id,默认为0代表顶级
 * @property int $sort 层级,默认为1级
 * @property string|null $icon 图标
 * @property string|null $url 路由
 * @property int $type 判断类型，1为目录，2为可点击
 * @property string|null $permission 权限
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Menu> $children
 * @property-read int|null $children_count
 * @method static \Illuminate\Database\Eloquent\Builder|Menu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu query()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu wherePermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu wherePid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereSort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Menu withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Menu withoutTrashed()
 * @property int $show 是否显示菜单,1是,0否
 * @method static \Illuminate\Database\Eloquent\Builder|Menu whereShow($value)
 * @mixin \Eloquent
 */
class Menu extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $guarded = [];

    protected $hidden = ['permission','created_at','updated_at','deleted_at'];
    /*public function children()
    {
        return $this->hasMany(Menu::class, 'pid', 'id');
    }

    public function childs()
    {
        return $this->children()->get();
    }*/

//    自定义顺序
/*    // 定义菜单项排序规则
    public static $order = [
        "仪表盘" => 1,
        "节能管理" => 2,
        "列表页面" => 3,
        "重置密码" => 4
    ];

 // 定义 scope 方法用于排序
   public function scopeOrdered($query)
    {
        return $query->orderByRaw('FIELD(`name`, "' . implode('", "', array_keys(self::$order)) . '")');
    }*/

    public function children()
    {
        return $this->hasMany(Menu::class, 'pid', 'id')
            ->when($this->type === 1, function ($query) {
                $query->with('children');
            })
            ->orderBy('sort', 'asc')->with('children');
    }

    /**
     * 获取指定 URL 对应的所有父节点
     *
     * @param string $url
     * @param array $ancestors
     * @return mixed
     */
/*    public static function getAncestorsByUrl($url, $ancestors = [])
    {
        $menu = self::where('url', $url)->first();

        if (!$menu) {
            return null;
        }

        // 如果当前节点是顶级节点（pid=0），直接返回结果
        if ($menu->pid == 0) {
            return array_reverse($ancestors);
        }

        // 找到当前节点的父节点，并将当前节点加入祖先列表
        $parent = self::find($menu->pid);
        if ($parent) {
            $ancestors[] = $parent;
            return self::getAncestorsByUrl($parent->url, $ancestors);
        }

        // 当前节点放在最后
        return null;
    }*/

    /**
     * 获取指定 URL 对应的所有父节点，包括当前节点，并将当前节点放在最后
     *
     * @param string $url
     * @param array $ancestors
     * @return mixed
     */
    public static function getAncestorsByUrl($url, $ancestors = [])
    {
        $menu = self::where('url', $url)->first();

        if (!$menu) {
            return null;
        }

        // 找到当前节点的父节点，并将当前节点加入祖先列表
        $parent = self::find($menu->pid);
        if ($parent) {
            // 递归查找祖先节点
            $ancestors = self::getAncestorsByUrl($parent->url, $ancestors);
            // 将当前节点放在最后
            $ancestors[] = $menu;
        } else {
            // 如果没有父节点，则当前节点就是顶级节点，将其放在最后
            $ancestors[] = $menu;
        }

        return $ancestors;
    }









}

