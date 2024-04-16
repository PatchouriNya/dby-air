<?php

namespace App\Models\Menu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Menu\menu_account_relationship
 *
 * @property int $id
 * @property int $account_id 可以使用该菜单的账号id
 * @property int $menu_id 菜单id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|menu_account_relationship newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|menu_account_relationship newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|menu_account_relationship query()
 * @method static \Illuminate\Database\Eloquent\Builder|menu_account_relationship whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|menu_account_relationship whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|menu_account_relationship whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|menu_account_relationship whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|menu_account_relationship whereMenuId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|menu_account_relationship whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class menu_account_relationship extends Model
{
    use HasFactory;
}
