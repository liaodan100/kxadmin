<?php

namespace KxAdmin\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use KxAdmin\Models\AdminRole;
use KxAdmin\Validate\AdminRoleStoreValidate;
use KxAdmin\Validate\AdminRoleUpdateValidate;

class RoleController extends AdminController
{
    public function index(Request $request): JsonResponse
    {
        $query = AdminRole::query()
            ->with(['permissions:id,name,code', 'menus:id,title'])
            ->when($request->filled('keyword'), function ($query) use ($request): void {
                $keyword = trim((string) $request->input('keyword'));
                $query->where(function ($inner) use ($keyword): void {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('code', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', (bool) $request->input('status')))
            ->orderByDesc('id');

        return $this->paginate($request, $query);
    }

    public function show(AdminRole $role): JsonResponse
    {
        $role->load(['permissions:id,name,code', 'menus:id,title']);

        return $this->success($this->payload($role));
    }

    public function store(AdminRoleStoreValidate $request): JsonResponse
    {
        $payload = $request->validated();
        $permissionIds = $this->toIdArray($payload['permission_ids'] ?? []);
        $menuIds = $this->toIdArray($payload['menu_ids'] ?? []);
        unset($payload['permission_ids'], $payload['menu_ids']);

        $role = AdminRole::query()->create($payload);
        $role->permissions()->sync($permissionIds);
        $role->menus()->sync($menuIds);
        $role->load(['permissions:id,name,code', 'menus:id,title']);

        return $this->success($this->payload($role), '创建成功');
    }

    public function update(AdminRoleUpdateValidate $request, AdminRole $role): JsonResponse
    {
        $payload = $request->validated();
        $permissionIds = array_key_exists('permission_ids', $payload) ? $this->toIdArray($payload['permission_ids'] ?? []) : null;
        $menuIds = array_key_exists('menu_ids', $payload) ? $this->toIdArray($payload['menu_ids'] ?? []) : null;
        unset($payload['permission_ids'], $payload['menu_ids']);

        $role->fill($payload)->save();

        if ($permissionIds !== null) {
            $role->permissions()->sync($permissionIds);
        }

        if ($menuIds !== null) {
            $role->menus()->sync($menuIds);
        }

        $role->load(['permissions:id,name,code', 'menus:id,title']);

        return $this->success($this->payload($role), '更新成功');
    }

    public function destroy(AdminRole $role): JsonResponse
    {
        $role->permissions()->detach();
        $role->menus()->detach();
        $role->users()->detach();
        $role->delete();

        return $this->success([], '删除成功');
    }

    protected function payload(AdminRole $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'code' => $role->code,
            'description' => $role->description,
            'status' => $role->status,
            'permissions' => $role->permissions->map(fn ($permission) => [
                'id' => $permission->id,
                'name' => $permission->name,
                'code' => $permission->code,
            ])->values()->all(),
            'permission_ids' => $role->permissions->pluck('id')->values()->all(),
            'menus' => $role->menus->map(fn ($menu) => [
                'id' => $menu->id,
                'title' => $menu->title,
            ])->values()->all(),
            'menu_ids' => $role->menus->pluck('id')->values()->all(),
        ];
    }
}
