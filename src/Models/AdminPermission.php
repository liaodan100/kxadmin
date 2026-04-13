<?php

namespace KxAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use KxAdmin\Models\Concerns\HasDateTimeFormat;

class AdminPermission extends Model
{
    use HasDateTimeFormat;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'group',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function getTable()
    {
        return config('admin.tables.permissions', 'admin_permissions');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminRole::class,
            config('admin.tables.role_permissions', 'admin_permission_role'),
            'permission_id',
            'role_id'
        );
    }

    public function apis(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminApi::class,
            config('admin.tables.permission_apis', 'admin_api_permission'),
            'permission_id',
            'api_id'
        );
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', true);
    }
}
