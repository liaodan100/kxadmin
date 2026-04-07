<?php

namespace KxAdmin;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use KxAdmin\Commands\AdminCreate;
use KxAdmin\Middleware\Authenticate;

class AdminServiceProvider extends ServiceProvider
{
    protected array $command = [
        AdminCreate::class,
    ];

    protected array $routeMiddleware = [
        'admin.auth' => Authenticate::class,
    ];

    protected $middlewareGroups = [
        'admin' => [
            'admin.auth'
        ]
    ];

    public function register(): void
    {
        // 加载Auth配置
        $this->loadAdminAuthConfig();
        // 中间件注册
        $this->registerRouteMiddleware();
        // 注册命令行
        $this->commands($this->command);
        // 注册路由文件
        if (file_exists($routers = admin_path('routers.php'))) {
            $this->loadRoutesFrom($routers);
        }
    }

    protected function registerRouteMiddleware(): void
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }

    protected function loadAdminAuthConfig()
    {
        config(Arr::dot(config('admin.auth', []), 'auth.'));
    }
}
