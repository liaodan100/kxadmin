<?php

namespace KxAdmin\Support;

use KxAdmin\Contracts\DashboardProvider;
use KxAdmin\Models\AdminApi;
use KxAdmin\Models\AdminMenu;
use KxAdmin\Models\AdminPermission;
use KxAdmin\Models\AdminRole;
use KxAdmin\Models\AdminUser;

class DefaultDashboardProvider implements DashboardProvider
{
    public function getData(): array
    {
        return [
            'users' => AdminUser::query()->count(),
            'roles' => AdminRole::query()->count(),
            'permissions' => AdminPermission::query()->count(),
            'apis' => AdminApi::query()->count(),
            'menus' => AdminMenu::query()->count(),
        ];
    }
}
