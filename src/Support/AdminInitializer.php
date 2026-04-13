<?php

namespace KxAdmin\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use KxAdmin\Models\AdminApi;
use KxAdmin\Models\AdminMenu;
use KxAdmin\Models\AdminPermission;
use KxAdmin\Models\AdminRole;
use KxAdmin\Models\AdminUser;

class AdminInitializer
{
    public function initialize(string $username, string $password, string $name, bool $force = false): void
    {
        DB::transaction(function () use ($username, $password, $name, $force): void {
            if ($force) {
                $this->resetSeededData();
            }

            $menus = $this->syncMenus();
            $apis = $this->syncApis();
            $permissions = $this->syncPermissions($apis);
            $role = $this->syncSuperRole($menus, $permissions);
            $this->syncAdminUser($role, $username, $password, $name);
        });
    }

    public function initializeMenus(bool $force = false): void
    {
        DB::transaction(function () use ($force): void {
            if ($force) {
                AdminMenu::query()->whereIn('name', $this->seededMenuNames())->delete();
            }

            $this->syncMenus();
        });
    }

    protected function resetSeededData(): void
    {
        AdminRole::query()->where('code', (string) config('admin.super_role_code', 'R_SUPER'))->delete();
        AdminMenu::query()->whereIn('name', $this->seededMenuNames())->delete();
        AdminPermission::query()->whereIn('code', $this->seededPermissionCodes())->delete();

        $apiKeys = $this->seededApiKeys();
        AdminApi::query()->get()->each(function (AdminApi $api) use ($apiKeys): void {
            if (in_array($this->apiKey($api->method, $api->path), $apiKeys, true)) {
                $api->delete();
            }
        });
    }

    protected function syncMenus(): array
    {
        $records = [];

        foreach (AdminBuiltinSeedData::menus() as $index => $menu) {
            $this->storeMenu($menu, 0, $index, $records);
        }

        return $records;
    }

    protected function storeMenu(array $menu, int $parentId, int $index, array &$records): void
    {
        $children = $menu['children'] ?? [];
        $meta = $menu['meta'] ?? [];
        unset($menu['children'], $menu['meta']);

        $record = AdminMenu::query()->withTrashed()->updateOrCreate(
            [
                'parent_id' => $parentId,
                'name' => $menu['name'],
            ],
            [
                'parent_id' => $parentId,
                'type' => $menu['type'] ?? ($parentId === 0 ? AdminMenu::TYPE_CATALOG : AdminMenu::TYPE_MENU),
                'path' => $menu['path'] ?? null,
                'name' => $menu['name'],
                'component' => $menu['component'] ?? null,
                'route_name' => $menu['route_name'] ?? null,
                'redirect' => $menu['redirect'] ?? null,
                'title' => $menu['title'] ?? (string) ($meta['title'] ?? $menu['name']),
                'icon' => $menu['icon'] ?? ($meta['icon'] ?? null),
                'sort' => (int) ($menu['sort'] ?? (($index + 1) * 10)),
                'keep_alive' => (bool) ($menu['keep_alive'] ?? ($meta['keepAlive'] ?? false)),
                'hidden' => (bool) ($menu['hidden'] ?? ($meta['isHide'] ?? false)),
                'status' => (bool) ($menu['status'] ?? true),
                'meta' => $meta,
            ]
        );

        if ($record->trashed()) {
            $record->restore();
        }

        $records[$menu['name']] = $record;

        foreach ($children as $childIndex => $child) {
            $this->storeMenu($child, (int) $record->id, $childIndex, $records);
        }
    }

    protected function syncApis(): array
    {
        $records = [];

        foreach (AdminBuiltinSeedData::apis() as $definition) {
            $path = AdminApi::normalizePath($definition['path']);
            $method = strtoupper($definition['method']);

            $record = AdminApi::query()->withTrashed()->updateOrCreate(
                [
                    'method' => $method,
                    'path' => $path,
                ],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'] ?? null,
                    'status' => true,
                ]
            );

            if ($record->trashed()) {
                $record->restore();
            }

            $records[$this->apiKey($method, $path)] = $record;
        }

        return $records;
    }

    protected function syncPermissions(array $apis): array
    {
        $records = [];
        $apiDefinitions = collect(AdminBuiltinSeedData::apis());

        foreach (AdminBuiltinSeedData::permissions() as $definition) {
            $record = AdminPermission::query()->withTrashed()->updateOrCreate(
                ['code' => $definition['code']],
                [
                    'name' => $definition['name'],
                    'group' => $definition['group'] ?? null,
                    'description' => $definition['description'] ?? null,
                    'status' => true,
                ]
            );

            if ($record->trashed()) {
                $record->restore();
            }

            $apiIds = $apiDefinitions
                ->filter(fn (array $api): bool => ($api['permission_code'] ?? null) === $definition['code'])
                ->map(function (array $api) use ($apis): ?int {
                    $key = $this->apiKey($api['method'], $api['path']);

                    return isset($apis[$key]) ? (int) $apis[$key]->id : null;
                })
                ->filter()
                ->values()
                ->all();

            $record->apis()->sync($apiIds);
            $records[$definition['code']] = $record;
        }

        return $records;
    }

    protected function syncSuperRole(array $menus, array $permissions): AdminRole
    {
        $role = AdminRole::query()->withTrashed()->updateOrCreate(
            ['code' => (string) config('admin.super_role_code', 'R_SUPER')],
            [
                'name' => 'Super Admin',
                'description' => 'Built-in super admin role created by admin:init',
                'status' => true,
            ]
        );

        if ($role->trashed()) {
            $role->restore();
        }

        $role->menus()->sync(collect($menus)->pluck('id')->map(fn ($id): int => (int) $id)->values()->all());
        $role->permissions()->sync(collect($permissions)->pluck('id')->map(fn ($id): int => (int) $id)->values()->all());

        return $role;
    }

    protected function syncAdminUser(AdminRole $role, string $username, string $password, string $name): AdminUser
    {
        $user = AdminUser::query()->withTrashed()->updateOrCreate(
            ['username' => $username],
            [
                'password' => Hash::make($password),
                'name' => $name,
                'status' => true,
                'is_super' => true,
            ]
        );

        if ($user->trashed()) {
            $user->restore();
        }

        $user->roles()->syncWithoutDetaching([(int) $role->id]);

        return $user;
    }

    protected function seededMenuNames(): array
    {
        return collect($this->flattenMenus(AdminBuiltinSeedData::menus()))
            ->pluck('name')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function seededPermissionCodes(): array
    {
        return collect(AdminBuiltinSeedData::permissions())
            ->pluck('code')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function seededApiKeys(): array
    {
        return collect(AdminBuiltinSeedData::apis())
            ->map(fn (array $api): string => $this->apiKey($api['method'], $api['path']))
            ->values()
            ->all();
    }

    protected function flattenMenus(array $menus): array
    {
        $items = [];

        foreach ($menus as $menu) {
            $items[] = $menu;

            if (!empty($menu['children'])) {
                $items = array_merge($items, $this->flattenMenus($menu['children']));
            }
        }

        return $items;
    }

    protected function apiKey(string $method, string $path): string
    {
        return strtoupper($method) . ' ' . AdminApi::normalizePath($path);
    }
}
