<?php

namespace KxAdmin\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use KxAdmin\Models\AdminPermission;
use KxAdmin\Validate\AdminPermissionStoreValidate;
use KxAdmin\Validate\AdminPermissionUpdateValidate;

class PermissionController extends AdminController
{
    public function index(Request $request): JsonResponse
    {
        $query = AdminPermission::query()
            ->with('apis:id,name,method,path')
            ->when($request->filled('keyword'), function ($query) use ($request): void {
                $keyword = trim((string) $request->input('keyword'));
                $query->where(function ($inner) use ($keyword): void {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('code', 'like', "%{$keyword}%")
                        ->orWhere('group', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', (bool) $request->input('status')))
            ->orderByDesc('id');

        return $this->paginate($request, $query);
    }

    public function show(AdminPermission $permission): JsonResponse
    {
        $permission->load('apis:id,name,method,path');

        return $this->success($this->payload($permission));
    }

    public function store(AdminPermissionStoreValidate $request): JsonResponse
    {
        $payload = $request->validated();
        $apiIds = $this->toIdArray($payload['api_ids'] ?? []);
        unset($payload['api_ids']);

        $permission = AdminPermission::query()->create($payload);
        $permission->apis()->sync($apiIds);
        $permission->load('apis:id,name,method,path');

        return $this->success($this->payload($permission), '创建成功');
    }

    public function update(AdminPermissionUpdateValidate $request, AdminPermission $permission): JsonResponse
    {
        $payload = $request->validated();
        $apiIds = array_key_exists('api_ids', $payload) ? $this->toIdArray($payload['api_ids'] ?? []) : null;
        unset($payload['api_ids']);

        $permission->fill($payload)->save();

        if ($apiIds !== null) {
            $permission->apis()->sync($apiIds);
        }

        $permission->load('apis:id,name,method,path');

        return $this->success($this->payload($permission), '更新成功');
    }

    public function destroy(AdminPermission $permission): JsonResponse
    {
        $permission->roles()->detach();
        $permission->apis()->detach();
        $permission->delete();

        return $this->success([], '删除成功');
    }

    protected function payload(AdminPermission $permission): array
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'code' => $permission->code,
            'group' => $permission->group,
            'description' => $permission->description,
            'status' => $permission->status,
            'apis' => $permission->apis->map(fn ($api) => [
                'id' => $api->id,
                'name' => $api->name,
                'method' => $api->method,
                'path' => $api->path,
            ])->values()->all(),
            'api_ids' => $permission->apis->pluck('id')->values()->all(),
        ];
    }
}
