<?php

namespace KxAdmin\Support;

use KxAdmin\Models\AdminMenu;

class AdminBuiltinSeedData
{
    public static function menus(): array
    {
        $superRoleCode = (string) config('admin.super_role_code', 'R_SUPER');

        return [
            [
                'path' => '/dashboard',
                'name' => 'Dashboard',
                'component' => '/index/index',
                'type' => AdminMenu::TYPE_CATALOG,
                'meta' => [
                    'title' => '仪表盘',
                    'icon' => 'ri:pie-chart-line',
                ],
                'sort' => 1,
                'children' => [
                    [
                        'path' => 'console',
                        'name' => 'Console',
                        'component' => '/dashboard/console/index',
                        'type' => AdminMenu::TYPE_MENU,
                        'sort' => 10,
                        'meta' => [
                            'title' => '控制台',
                            'icon' => 'ri:home-smile-2-line'
                        ],
                    ],
                ],
            ],
            [
                'path' => '/system',
                'name' => 'System',
                'component' => '/index/index',
                'type' => AdminMenu::TYPE_CATALOG,
                'meta' => [
                    'title' => '系统管理',
                    'icon' => 'ri:settings-3-line',
                ],
                'sort' => 999,
                'children' => [
                    [
                        'path' => 'user',
                        'name' => 'User',
                        'component' => '/system/user',
                        'type' => AdminMenu::TYPE_MENU,
                        'sort' => 10,
                        'meta' => [
                            'title' => '用户管理',
                            'keepAlive' => true,
                        ],
                    ],
                    [
                        'path' => 'role',
                        'name' => 'Role',
                        'component' => '/system/role',
                        'type' => AdminMenu::TYPE_MENU,
                        'sort' => 20,
                        'meta' => [
                            'title' => '角色管理',
                            'keepAlive' => true,
                        ],
                    ],
                    [
                        'path' => 'user-center',
                        'name' => 'UserCenter',
                        'component' => '/system/user-center',
                        'type' => AdminMenu::TYPE_MENU,
                        'sort' => 30,
                        'meta' => [
                            'title' => '个人中心',
                            'isHide' => true,
                            'keepAlive' => true,
                            'isHideTab' => true,
                        ],
                    ],
                    [
                        'path' => 'menu',
                        'name' => 'Menus',
                        'component' => '/system/menu',
                        'type' => AdminMenu::TYPE_MENU,
                        'sort' => 40,
                        'meta' => [
                            'title' => '菜单管理',
                            'keepAlive' => true,
                            'roles' => [$superRoleCode],
                            'authList' => [
                                ['title' => '新增', 'authMark' => 'add'],
                                ['title' => '编辑', 'authMark' => 'edit'],
                                ['title' => '删除', 'authMark' => 'delete'],
                            ],
                        ],
                    ],
                    [
                        'path' => 'apis',
                        'name' => 'Apis',
                        'component' => '/system/api',
                        'type' => AdminMenu::TYPE_MENU,
                        'sort' => 50,
                        'meta' => [
                            'title' => 'API管理',
                            'keepAlive' => true,
                            'roles' => [$superRoleCode],
                            'authList' => [
                                ['title' => '新增', 'authMark' => 'add'],
                                ['title' => '编辑', 'authMark' => 'edit'],
                                ['title' => '删除', 'authMark' => 'delete'],
                            ],
                        ],
                    ],
                    [
                        'path' => 'permission',
                        'name' => 'Permission',
                        'component' => '/system/permission',
                        'type' => AdminMenu::TYPE_MENU,
                        'sort' => 60,
                        'meta' => [
                            'title' => '权限管理',
                            'keepAlive' => true,
                            'roles' => [$superRoleCode],
                            'authList' => [
                                ['title' => '新增', 'authMark' => 'add'],
                                ['title' => '编辑', 'authMark' => 'edit'],
                                ['title' => '删除', 'authMark' => 'delete'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    public static function apis(): array
    {
        return [
            ['name' => '账号密码登录', 'method' => 'POST', 'path' => 'admin/login/password', 'description' => '管理员账号密码登录'],
            ['name' => '刷新令牌', 'method' => 'POST', 'path' => 'admin/login/refresh', 'description' => '刷新 JWT 令牌'],
            ['name' => '退出登录', 'method' => 'POST', 'path' => 'admin/login/logout', 'description' => '退出当前登录'],
            ['name' => '当前用户信息', 'method' => 'GET', 'path' => 'admin/users/info', 'description' => '获取当前登录管理员信息'],
            ['name' => '仪表盘数据', 'method' => 'GET', 'path' => 'admin/dashboard', 'description' => '获取后台首页统计数据'],
            ['name' => '当前用户路由菜单', 'method' => 'GET', 'path' => 'admin/menu/routes', 'description' => '获取当前用户可访问菜单'],
            ['name' => '通用文件上传', 'method' => 'POST', 'path' => 'admin/upload', 'description' => '后台通用文件上传'],
            ['name' => '图片上传', 'method' => 'POST', 'path' => 'admin/upload/image', 'description' => '后台图片上传'],

            ['name' => '用户列表', 'method' => 'GET', 'path' => 'admin/users', 'description' => '获取用户列表', 'permission_code' => 'system.user.view'],
            ['name' => '用户详情', 'method' => 'GET', 'path' => 'admin/users/{user}', 'description' => '获取用户详情', 'permission_code' => 'system.user.view'],
            ['name' => '新增用户', 'method' => 'POST', 'path' => 'admin/users', 'description' => '创建用户', 'permission_code' => 'system.user.create'],
            ['name' => '更新用户', 'method' => 'PUT', 'path' => 'admin/users/{user}', 'description' => '更新用户', 'permission_code' => 'system.user.update'],
            ['name' => '删除用户', 'method' => 'DELETE', 'path' => 'admin/users/{user}', 'description' => '删除用户', 'permission_code' => 'system.user.delete'],

            ['name' => '角色列表', 'method' => 'GET', 'path' => 'admin/roles', 'description' => '获取角色列表', 'permission_code' => 'system.role.view'],
            ['name' => '角色详情', 'method' => 'GET', 'path' => 'admin/roles/{role}', 'description' => '获取角色详情', 'permission_code' => 'system.role.view'],
            ['name' => '新增角色', 'method' => 'POST', 'path' => 'admin/roles', 'description' => '创建角色', 'permission_code' => 'system.role.create'],
            ['name' => '更新角色', 'method' => 'PUT', 'path' => 'admin/roles/{role}', 'description' => '更新角色', 'permission_code' => 'system.role.update'],
            ['name' => '删除角色', 'method' => 'DELETE', 'path' => 'admin/roles/{role}', 'description' => '删除角色', 'permission_code' => 'system.role.delete'],

            ['name' => '权限列表', 'method' => 'GET', 'path' => 'admin/permissions', 'description' => '获取权限列表', 'permission_code' => 'system.permission.view'],
            ['name' => '权限详情', 'method' => 'GET', 'path' => 'admin/permissions/{permission}', 'description' => '获取权限详情', 'permission_code' => 'system.permission.view'],
            ['name' => '新增权限', 'method' => 'POST', 'path' => 'admin/permissions', 'description' => '创建权限', 'permission_code' => 'system.permission.create'],
            ['name' => '更新权限', 'method' => 'PUT', 'path' => 'admin/permissions/{permission}', 'description' => '更新权限', 'permission_code' => 'system.permission.update'],
            ['name' => '删除权限', 'method' => 'DELETE', 'path' => 'admin/permissions/{permission}', 'description' => '删除权限', 'permission_code' => 'system.permission.delete'],

            ['name' => 'API列表', 'method' => 'GET', 'path' => 'admin/apis', 'description' => '获取API列表', 'permission_code' => 'system.api.view'],
            ['name' => 'API详情', 'method' => 'GET', 'path' => 'admin/apis/{api}', 'description' => '获取API详情', 'permission_code' => 'system.api.view'],
            ['name' => '新增API', 'method' => 'POST', 'path' => 'admin/apis', 'description' => '创建API', 'permission_code' => 'system.api.create'],
            ['name' => '更新API', 'method' => 'PUT', 'path' => 'admin/apis/{api}', 'description' => '更新API', 'permission_code' => 'system.api.update'],
            ['name' => '删除API', 'method' => 'DELETE', 'path' => 'admin/apis/{api}', 'description' => '删除API', 'permission_code' => 'system.api.delete'],

            ['name' => '菜单列表', 'method' => 'GET', 'path' => 'admin/menus', 'description' => '获取菜单列表', 'permission_code' => 'system.menu.view'],
            ['name' => '菜单树', 'method' => 'GET', 'path' => 'admin/menus/tree', 'description' => '获取菜单树', 'permission_code' => 'system.menu.view'],
            ['name' => '菜单详情', 'method' => 'GET', 'path' => 'admin/menus/{menu}', 'description' => '获取菜单详情', 'permission_code' => 'system.menu.view'],
            ['name' => '新增菜单', 'method' => 'POST', 'path' => 'admin/menus', 'description' => '创建菜单', 'permission_code' => 'system.menu.create'],
            ['name' => '更新菜单', 'method' => 'PUT', 'path' => 'admin/menus/{menu}', 'description' => '更新菜单', 'permission_code' => 'system.menu.update'],
            ['name' => '删除菜单', 'method' => 'DELETE', 'path' => 'admin/menus/{menu}', 'description' => '删除菜单', 'permission_code' => 'system.menu.delete'],
        ];
    }

    public static function permissions(): array
    {
        return [
            ['name' => '查看用户', 'code' => 'system.user.view', 'group' => 'system.user', 'description' => '查看用户列表和详情'],
            ['name' => '新增用户', 'code' => 'system.user.create', 'group' => 'system.user', 'description' => '创建用户'],
            ['name' => '编辑用户', 'code' => 'system.user.update', 'group' => 'system.user', 'description' => '更新用户'],
            ['name' => '删除用户', 'code' => 'system.user.delete', 'group' => 'system.user', 'description' => '删除用户'],

            ['name' => '查看角色', 'code' => 'system.role.view', 'group' => 'system.role', 'description' => '查看角色列表和详情'],
            ['name' => '新增角色', 'code' => 'system.role.create', 'group' => 'system.role', 'description' => '创建角色'],
            ['name' => '编辑角色', 'code' => 'system.role.update', 'group' => 'system.role', 'description' => '更新角色'],
            ['name' => '删除角色', 'code' => 'system.role.delete', 'group' => 'system.role', 'description' => '删除角色'],

            ['name' => '查看权限', 'code' => 'system.permission.view', 'group' => 'system.permission', 'description' => '查看权限列表和详情'],
            ['name' => '新增权限', 'code' => 'system.permission.create', 'group' => 'system.permission', 'description' => '创建权限'],
            ['name' => '编辑权限', 'code' => 'system.permission.update', 'group' => 'system.permission', 'description' => '更新权限'],
            ['name' => '删除权限', 'code' => 'system.permission.delete', 'group' => 'system.permission', 'description' => '删除权限'],

            ['name' => '查看API', 'code' => 'system.api.view', 'group' => 'system.api', 'description' => '查看API列表和详情'],
            ['name' => '新增API', 'code' => 'system.api.create', 'group' => 'system.api', 'description' => '创建API'],
            ['name' => '编辑API', 'code' => 'system.api.update', 'group' => 'system.api', 'description' => '更新API'],
            ['name' => '删除API', 'code' => 'system.api.delete', 'group' => 'system.api', 'description' => '删除API'],

            ['name' => '查看菜单', 'code' => 'system.menu.view', 'group' => 'system.menu', 'description' => '查看菜单列表和详情'],
            ['name' => '新增菜单', 'code' => 'system.menu.create', 'group' => 'system.menu', 'description' => '创建菜单'],
            ['name' => '编辑菜单', 'code' => 'system.menu.update', 'group' => 'system.menu', 'description' => '更新菜单'],
            ['name' => '删除菜单', 'code' => 'system.menu.delete', 'group' => 'system.menu', 'description' => '删除菜单'],
        ];
    }
}
