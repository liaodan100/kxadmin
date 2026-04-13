<?php

namespace KxAdmin\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use KxAdmin\Models\AdminApi;
use KxAdmin\Validate\AdminApiStoreValidate;
use KxAdmin\Validate\AdminApiUpdateValidate;

class ApiController extends AdminController
{
    public function index(Request $request): JsonResponse
    {
        $query = AdminApi::query()
            ->when($request->filled('keyword'), function ($query) use ($request): void {
                $keyword = trim((string) $request->input('keyword'));
                $query->where(function ($inner) use ($keyword): void {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('method', 'like', "%{$keyword}%")
                        ->orWhere('path', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', (bool) $request->input('status')))
            ->orderByDesc('id');

        return $this->paginate($request, $query);
    }

    public function show(AdminApi $api): JsonResponse
    {
        return $this->success($api);
    }

    public function store(AdminApiStoreValidate $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['path'] = AdminApi::normalizePath($payload['path']);
        $payload['method'] = strtoupper($payload['method']);

        $api = AdminApi::query()->create($payload);

        return $this->success($api, '创建成功');
    }

    public function update(AdminApiUpdateValidate $request, AdminApi $api): JsonResponse
    {
        $payload = $request->validated();

        if (array_key_exists('path', $payload)) {
            $payload['path'] = AdminApi::normalizePath($payload['path']);
        }

        if (array_key_exists('method', $payload)) {
            $payload['method'] = strtoupper($payload['method']);
        }

        $api->fill($payload)->save();

        return $this->success($api, '更新成功');
    }

    public function destroy(AdminApi $api): JsonResponse
    {
        $api->permissions()->detach();
        $api->delete();

        return $this->success([], '删除成功');
    }
}
