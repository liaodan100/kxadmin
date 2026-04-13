<?php

namespace KxAdmin\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use KxAdmin\Models\AdminUser;
use KxAdmin\Validate\AdminUserStoreValidate;
use KxAdmin\Validate\AdminUserUpdateValidate;

class UsersController extends AdminController
{
    public function index(Request $request): JsonResponse
    {
        $query = AdminUser::query()
            ->with('roles:id,name,code')
            ->when($request->filled('keyword'), function ($query) use ($request): void {
                $keyword = trim((string) $request->input('keyword'));
                $query->where(function ($inner) use ($keyword): void {
                    $inner->where('username', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%")
                        ->orWhere('email', 'like', "%{$keyword}%")
                        ->orWhere('mobile', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', (bool) $request->input('status')))
            ->orderByDesc('id');

        return $this->paginate($request, $query);
    }

    public function show(AdminUser $user): JsonResponse
    {
        $user->load('roles:id,name,code');

        return $this->success($this->payload($user));
    }

    public function store(AdminUserStoreValidate $request): JsonResponse
    {
        $payload = $request->validated();
        $roleIds = $this->toIdArray($payload['role_ids'] ?? []);
        unset($payload['role_ids']);
        $payload['password'] = Hash::make($payload['password']);

        $user = AdminUser::query()->create($payload);
        $user->syncRoles($roleIds);
        $user->load('roles:id,name,code');

        return $this->success($this->payload($user), '创建成功');
    }

    public function update(AdminUserUpdateValidate $request, AdminUser $user): JsonResponse
    {
        $payload = $request->validated();
        $roleIds = array_key_exists('role_ids', $payload) ? $this->toIdArray($payload['role_ids'] ?? []) : null;
        unset($payload['role_ids']);

        if (array_key_exists('password', $payload)) {
            if ($payload['password'] === null || $payload['password'] === '') {
                unset($payload['password']);
            } else {
                $payload['password'] = Hash::make($payload['password']);
            }
        }

        $user->fill($payload)->save();

        if ($roleIds !== null) {
            $user->syncRoles($roleIds);
        }

        $user->load('roles:id,name,code');

        return $this->success($this->payload($user), '更新成功');
    }

    public function destroy(AdminUser $user): JsonResponse
    {
        $currentUser = Auth::guard('admin')->user();

        if ($currentUser && (int) $currentUser->getAuthIdentifier() === (int) $user->id) {
            return $this->error([], '不能删除当前登录用户', 422, 422);
        }

        $user->roles()->detach();
        $user->delete();

        return $this->success([], '删除成功');
    }

    protected function payload(AdminUser $user): array
    {
        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'status' => $user->status,
            'is_super' => $user->is_super,
            'last_login_at' => $user->last_login_at,
            'roles' => $user->roles->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'code' => $role->code,
            ])->values()->all(),
            'role_ids' => $user->roles->pluck('id')->values()->all(),
        ];
    }
}
