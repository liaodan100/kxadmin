<?php

namespace KxAdmin\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use KxAdmin\Models\AdminApi;
use KxAdmin\Models\AdminUser;
use KxAdmin\Response\ApiResponse;

class PermissionMiddleware
{
    use ApiResponse;

    public function handle($request, Closure $next, string $permissions = '')
    {
        /** @var AdminUser|null $user */
        $user = $request->user() ?? Auth::guard('admin')->user();

        if (!$user instanceof AdminUser) {
            return $this->error([], '未登录或登录已失效', 401, 401);
        }

        if (!$user->isSuperAdmin()) {
            $permissionList = collect(explode('|', $permissions))
                ->map(fn ($permission) => trim($permission))
                ->filter()
                ->values()
                ->all();

            if ($permissionList !== [] && !$user->hasAnyPermission($permissionList)) {
                return $this->error([], '无权限访问当前资源', 403, 403);
            }

            $paths = collect([
                AdminApi::normalizePath((string) optional($request->route())->uri()),
                AdminApi::normalizePath($request->path()),
            ])->filter()->unique()->values();

            $apiExists = AdminApi::query()
                ->enabled()
                ->where('method', strtoupper($request->method()))
                ->whereIn('path', $paths->all())
                ->exists();

            if ($apiExists) {
                $canAccess = $paths->contains(fn ($path) => $user->canAccessApi($request->method(), $path));

                if (!$canAccess) {
                    return $this->error([], '未分配接口权限', 403, 403);
                }
            }
        }

        return $next($request);
    }
}
