<?php

use App\Admin\DashboardProvider;
use KxAdmin\Models\AdminUser;

return [
    'route' => [
        'prefix' => env('ADMIN_ROUTE_PREFIX', 'admin'),
        'middleware' => ['api'],
    ],
    'auth' => [
        'guard' => 'admin',
        'guards' => [
            'admin' => [
                'driver' => 'jwt',
                'provider' => 'admin_users',
            ],
        ],
        'providers' => [
            'admin_users' => [
                'driver' => 'eloquent',
                'model' => AdminUser::class,
            ],
        ],
    ],
    'path' => app_path('Admin'),
    'admin_model' => AdminUser::class,
    'tables' => [
        'users' => 'admin_users',
        'roles' => 'admin_roles',
        'permissions' => 'admin_permissions',
        'apis' => 'admin_apis',
        'menus' => 'admin_menus',
        'role_users' => 'admin_role_user',
        'role_permissions' => 'admin_permission_role',
        'permission_apis' => 'admin_api_permission',
        'role_menus' => 'admin_menu_role',
    ],
    'dashboard' => [
        'provider' => DashboardProvider::class,
    ],
    'upload' => [
        'disk' => env('ADMIN_UPLOAD_DISK', 'public'),
        'directory' => env('ADMIN_UPLOAD_DIRECTORY', 'admin/uploads/files'),
        'image_directory' => env('ADMIN_UPLOAD_IMAGE_DIRECTORY', 'admin/uploads/images'),
        'visibility' => env('ADMIN_UPLOAD_VISIBILITY', 'public'),
        'max_size' => (int) env('ADMIN_UPLOAD_MAX_SIZE', 10240),
        'image_max_size' => (int) env('ADMIN_UPLOAD_IMAGE_MAX_SIZE', 5120),
        'allowed_disks' => ['public', 'local', 's3'],
    ],
    'super_role_code' => env('ADMIN_SUPER_ROLE_CODE', 'R_SUPER'),
];
