<?php

namespace KxAdmin\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use KxAdmin\Models\Concerns\HasDateTimeFormat;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AdminUser extends Authenticatable implements JWTSubject
{
    use HasDateTimeFormat;
    use SoftDeletes;

    protected $fillable = [
        'username',
        'password',
        'name',
        'avatar',
        'email',
        'mobile',
        'status',
        'is_super',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_super' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function getTable()
    {
        return config('admin.tables.users', 'admin_users');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminRole::class,
            config('admin.tables.role_users', 'admin_role_user'),
            'user_id',
            'role_id'
        );
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', true);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super;
    }

    public function syncRoles(array $roleIds): void
    {
        $this->roles()->sync($roleIds);
    }

    public function permissionCodes(): array
    {
        if ($this->isSuperAdmin()) {
            return AdminPermission::query()->enabled()->pluck('code')->all();
        }

        return $this->loadMissing('roles.permissions')
            ->roles
            ->flatMap(fn (AdminRole $role) => $role->permissions)
            ->pluck('code')
            ->unique()
            ->values()
            ->all();
    }

    public function menuIds(): array
    {
        if ($this->isSuperAdmin()) {
            return AdminMenu::query()->pluck('id')->all();
        }

        return $this->loadMissing('roles.menus')
            ->roles
            ->flatMap(fn (AdminRole $role) => $role->menus)
            ->pluck('id')
            ->unique()
            ->values()
            ->all();
    }

    public function accessibleMenus(): Collection
    {
        $query = AdminMenu::query()
            ->enabled()
            ->orderBy('parent_id')
            ->orderBy('sort')
            ->orderBy('id');

        if ($this->isSuperAdmin()) {
            return $query->get();
        }

        $menuIds = $this->menuIds();

        return $menuIds === []
            ? new Collection()
            : $query->whereIn('id', $menuIds)->get();
    }

    public function hasAnyPermission(array|string $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permissions = is_array($permissions) ? $permissions : [$permissions];

        return collect($this->permissionCodes())->intersect($permissions)->isNotEmpty();
    }

    public function canAccessApi(string $method, string $path): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $identity = strtoupper($method) . ' ' . trim($path, '/');

        return $this->loadMissing('roles.permissions.apis')
            ->roles
            ->flatMap(fn (AdminRole $role) => $role->permissions)
            ->flatMap(fn (AdminPermission $permission) => $permission->apis)
            ->map(fn (AdminApi $api) => strtoupper($api->method) . ' ' . trim($api->path, '/'))
            ->contains($identity);
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [
            'is_super' => $this->isSuperAdmin(),
            'username' => $this->username,
        ];
    }
}
