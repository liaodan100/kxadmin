<?php

namespace KxAdmin\Controllers;

use App\Http\Requests\Admin\UpdatePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use KxAdmin\Models\AdminUser;
use KxAdmin\Validate\LoginValidate;

class LoginController extends AdminController
{
    public function loginWithPassword(LoginValidate $request): JsonResponse
    {
        $credentials = $request->validated();
        $guard = Auth::guard('admin');

        $token = $guard->attempt([
            'username' => $credentials['username'],
            'password' => $credentials['password'],
            'status' => true,
        ]);

        if (!$token) {
            return $this->error([], '用户名或密码错误', 400, 400);
        }

        /** @var AdminUser $user */
        $user = $guard->user();
        $user->forceFill(['last_login_at' => now()])->save();

        return $this->success($this->tokenPayload($guard, $token, $user), '登录成功');
    }

    public function refresh(): JsonResponse
    {
        $guard = Auth::guard('admin');
        $token = $guard->refresh();

        /** @var AdminUser $user */
        $user = $guard->user();

        return $this->success($this->tokenPayload($guard, $token, $user), '刷新成功');
    }

    public function logout(): JsonResponse
    {
        Auth::guard('admin')->logout();

        return $this->success([], '退出成功');
    }

    public function getLoginUserinfo(Request $request): JsonResponse
    {
        /** @var AdminUser $user */
        $user = $request->user() ?? Auth::guard('admin')->user();

        return $this->success($this->userPayload($user));
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        /** @var AdminUser $user */
        $user = $request->user() ?? Auth::guard('admin')->user();

        if (!$user || !Hash::check((string) $request->input('password'), (string) $user->password)) {
            return $this->error([], '当前密码错误', 422, 422);
        }

        $user->forceFill([
            'password' => Hash::make((string) $request->input('new_password')),
        ])->save();

        return $this->success([], '密码修改成功');
    }

    protected function tokenPayload($guard, string $token, AdminUser $user): array
    {
        $ttl = method_exists($guard, 'factory') ? $guard->factory()->getTTL() * 60 : null;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttl,
            'user' => $this->userPayload($user),
        ];
    }

    protected function userPayload(AdminUser $user): array
    {
        $user->loadMissing('roles.permissions.apis');

        return [
            'id' => $user->id,
            'username' => $user->username,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'status' => $user->status,
            'is_super' => $user->is_super,
            'roles' => $user->roles->map(fn ($role) => [
                'id' => $role->id,
                'name' => $role->name,
                'code' => $role->code,
            ])->values()->all(),
            'role_ids' => $user->roles->pluck('id')->values()->all(),
            'permission_codes' => $user->permissionCodes(),
        ];
    }
}
