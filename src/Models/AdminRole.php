<?php

namespace KxAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use KxAdmin\Models\Concerns\HasDateTimeFormat;

class AdminRole extends Model
{
    use HasDateTimeFormat;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function getTable()
    {
        return config('admin.tables.roles', 'admin_roles');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminUser::class,
            config('admin.tables.role_users', 'admin_role_user'),
            'role_id',
            'user_id'
        );
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminPermission::class,
            config('admin.tables.role_permissions', 'admin_permission_role'),
            'role_id',
            'permission_id'
        );
    }

    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminMenu::class,
            config('admin.tables.role_menus', 'admin_menu_role'),
            'role_id',
            'menu_id'
        );
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', true);
    }
}
