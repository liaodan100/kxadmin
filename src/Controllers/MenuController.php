<?php

namespace KxAdmin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use KxAdmin\Response\ApiResponse;

class MenuController extends AdminController
{
    use ApiResponse;
    /**
     * 获取菜单
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->success([
            [
                'id' => 1,
                'name' => 'Dashboard',
                'path' => '/dashboard',
                'component' => '/index/index',
                'meta' => [
                    'title' => 'menus.dashboard.title',
                    'icon' => 'ri:pie-chart-line',
                ],
                'children' => [
                    [
                        'id' => 2,
                        'name' => 'Console',
                        'path' => 'console',
                        'component' => '/dashboard/console/index',
                        'meta' => [
                            'title' => '工作台',
                            'icon' => 'ri:bar-chart-box-line',
                            'keepAlive' => true,
                            'fixedTab' => false
                        ]
                    ],
                ],
            ],
            [
                'id' => 2,
                'name' => 'Product',
                'path' => '/products',
                'component' => '/index/index',
                'meta' => [
                    'title' => '产品中心',
                    'icon' => 'ri:file-text-line',
                ],
                'children' => [
                    [
                        'id' => 31,
                        'name' => 'Categories',
                        'path' => 'categories',
                        'component' => '/products/categories/index',
                        'meta' => [
                            'title' => '产品分类',
                            'icon' => 'ri:home-smile-2-line',
                            'keepAlive' => false,
                            'fixedTab' => true
                        ]
                    ],
                    [
                        'id' => 52,
                        'name' => 'Specification',
                        'path' => 'specification',
                        'component' => '/products/specification/index',
                        'meta' => [
                            'title' => '产品规格',
                            'icon' => 'ri:home-smile-2-line',
                            'keepAlive' => false,
                            'fixedTab' => true
                        ]
                    ],
                    [
                        'id' => 41,
                        'name' => 'Goods',
                        'path' => 'goods',
                        'component' => '/products/goods/index',
                        'meta' => [
                            'title' => '产品管理',
                            'icon' => 'ri:home-smile-2-line',
                            'keepAlive' => false,
                            'fixedTab' => true
                        ]
                    ],
                    [
                        'id' => 42,
                        'name' => 'GoodsPublish',
                        'path' => 'goods-publish',
                        'component' => '/products/goods/publish',
                        'meta' => [
                            'title' => '发布产品',
                            'icon' => 'ri:home-smile-2-line',
                            'keepAlive' => false,
                            'fixedTab' => true,
                            'isHide' => true
                        ]
                    ]
                ]
            ],
            [
                'id' => 8,
                'name' => 'Quotation',
                'path' => '/quotations',
                'component' => '/index/index',
                'meta' => [
                    'title' => '报价单管理',
                    'icon' => 'ri:file-list-3-line',
                ],
                'children' => [
                    [
                        'id' => 81,
                        'name' => 'QuotationList',
                        'path' => 'list',
                        'component' => '/quotations/list/index',
                        'meta' => [
                            'title' => '报价单列表',
                            'icon' => 'ri:file-list-line',
                            'keepAlive' => true
                        ]
                    ],
                    [
                        'id' => 82,
                        'name' => 'QuotationCreate',
                        'path' => 'create',
                        'component' => '/quotations/create/index',
                        'meta' => [
                            'title' => '创建报价单',
                            'icon' => 'ri:file-add-line',
                            'isHideTab' => true
                        ]
                    ],
                    [
                        'id' => 83,
                        'name' => 'CustomerManage',
                        'path' => 'customers',
                        'component' => '/quotations/customers/index',
                        'meta' => [
                            'title' => '客户管理',
                            'icon' => 'ri:user-heart-line',
                            'keepAlive' => true
                        ]
                    ]
                ]
            ],
            [
                'id' => 3,
                'name' => 'System',
                'path' => '/system',
                'component' => '/index/index',
                'meta' => [
                    'title' => 'menus.system.title',
                    'icon' => 'ri:user-3-line',
                ],
                'children' => [
                    [
                        'id' => 4,
                        'name' => 'User',
                        'path' => 'user',
                        'component' => '/system/user',
                        'meta' => [
                            'title' => 'menus.system.user',
                            'icon' => 'ri:user-line',
                            'keepAlive' => true,
                            'roles' => ['R_SUPER', 'R_ADMIN']
                        ]
                    ],
                    [
                        'id' => 5,
                        'name' => 'Role',
                        'path' => 'role',
                        'component' => '/system/role',
                        'meta' => [
                            'title' => 'menus.system.role',
                            'icon' => 'ri:user-settings-line',
                            'keepAlive' => true,
                            'roles' => ['R_SUPER', 'R_ADMIN']
                        ]
                    ],
                    [
                        'id' => 7,
                        'name' => 'Menu',
                        'path' => 'menu',
                        'component' => '/system/menu',
                        'meta' => [
                            'title' => 'menus.system.menu',
                            'icon' => 'ri:menu-line',
                            'keepAlive' => true,
                            'roles' => ['R_SUPER', 'R_ADMIN']
                        ]
                    ],
                    [
                        'id' => 6,
                        'name' => 'userCenter',
                        'path' => 'user-center',
                        'component' => '/system/user-center',
                        'meta' => [
                            'title' => 'menus.system.userCenter',
                            'icon' => 'ri:user-line',
                            'keepAlive' => true,
                            'roles' => ['R_SUPER', 'R_ADMIN']
                        ]
                    ],
                ]
            ]
        ]);
    }
}
