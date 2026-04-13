<?php

namespace KxAdmin;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use KxAdmin\Commands\AdminInitCommand;
use KxAdmin\Middleware\Authenticate;
use KxAdmin\Middleware\PermissionMiddleware;

class AdminServiceProvider extends ServiceProvider
{
    protected array $commands = [
        AdminInitCommand::class,
    ];

    protected array $routeMiddleware = [
        'admin.auth' => Authenticate::class,
        'admin.permission' => PermissionMiddleware::class,
    ];

    protected array $middlewareGroups = [
        'admin' => ['admin.auth'],
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/admin.php', 'admin');
        require_once __DIR__ . '/helpers.php';
        $this->loadAdminAuthConfig();
    }

    public function boot(): void
    {
        $this->registerRouteMiddleware();
        $this->commands($this->commands);
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->publishes([
            __DIR__ . '/../config/admin.php' => config_path('admin.php'),
        ], 'kxadmin-config');
    }

    protected function registerRouteMiddleware(): void
    {
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        foreach ($this->middlewareGroups as $key => $middleware) {
            app('router')->middlewareGroup($key, $middleware);
        }
    }

    protected function loadAdminAuthConfig(): void
    {
        config(Arr::dot(config('admin.auth', []), 'auth.'));
    }
}
