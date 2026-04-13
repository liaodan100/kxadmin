<?php

namespace KxAdmin\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use KxAdmin\Models\AdminMenu;
use KxAdmin\Models\AdminUser;
use KxAdmin\Support\MenuTreeBuilder;
use KxAdmin\Validate\AdminMenuStoreValidate;
use KxAdmin\Validate\AdminMenuUpdateValidate;

class MenuController extends AdminController
{
    public function index(Request $request): JsonResponse
    {
        $menus = AdminMenu::query()
            ->orderBy('sort')
            ->orderBy('id')
            ->get()
            ->map(fn (AdminMenu $menu) => $this->payload($menu))
            ->all();

        return $this->success($menus);
    }

    public function tree(): JsonResponse
    {
        $menus = AdminMenu::query()->orderBy('parent_id')->orderBy('sort')->orderBy('id')->get();

        return $this->success(MenuTreeBuilder::build($menus, 0, fn (AdminMenu $menu) => $this->payload($menu)));
    }

    public function routes(Request $request): JsonResponse
    {
        /** @var AdminUser $user */
        $user = $request->user();
        $menus = $user->accessibleMenus();

        return $this->success(MenuTreeBuilder::build($menus, 0, fn (AdminMenu $menu) => $menu->toRouteArray()));
    }

    public function show(AdminMenu $menu): JsonResponse
    {
        return $this->success($this->payload($menu));
    }

    public function store(AdminMenuStoreValidate $request): JsonResponse
    {
        $payload = $request->validated();
        $payload['parent_id'] = (int) ($payload['parent_id'] ?? 0);
        $menu = AdminMenu::query()->create($payload);

        return $this->success($this->payload($menu), '创建成功');
    }

    public function update(AdminMenuUpdateValidate $request, AdminMenu $menu): JsonResponse
    {
        $payload = $request->validated();

        if (array_key_exists('parent_id', $payload)) {
            $payload['parent_id'] = (int) ($payload['parent_id'] ?? 0);
        }

        $menu->fill($payload)->save();

        return $this->success($this->payload($menu), '更新成功');
    }

    public function destroy(AdminMenu $menu): JsonResponse
    {
        if (AdminMenu::query()->where('parent_id', $menu->id)->exists()) {
            return $this->error([], '请先删除子菜单', 422, 422);
        }

        $menu->roles()->detach();
        $menu->delete();

        return $this->success([], '删除成功');
    }

    protected function payload(AdminMenu $menu): array
    {
        return [
            'id' => $menu->id,
            'parent_id' => $menu->parent_id,
            'type' => $menu->type,
            'path' => $menu->path,
            'name' => $menu->name,
            'component' => $menu->component,
            'route_name' => $menu->route_name,
            'redirect' => $menu->redirect,
            'title' => $menu->title,
            'icon' => $menu->icon,
            'sort' => $menu->sort,
            'keep_alive' => $menu->keep_alive,
            'hidden' => $menu->hidden,
            'status' => $menu->status,
            'meta' => $menu->meta ?? [],
        ];
    }
}
