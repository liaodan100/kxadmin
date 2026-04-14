<?php

use Illuminate\Support\Facades\Route;
use KxAdmin\Controllers\ApiController;
use KxAdmin\Controllers\DashboardController;
use KxAdmin\Controllers\FileUploadController;
use KxAdmin\Controllers\LoginController;
use KxAdmin\Controllers\MenuController;
use KxAdmin\Controllers\PermissionController;
use KxAdmin\Controllers\RoleController;
use KxAdmin\Controllers\UsersController;

Route::prefix(config('admin.route.prefix', 'admin'))
    ->middleware(config('admin.route.middleware', ['api']))
    ->as('admin.')
    ->group(function (): void {
        Route::post('login/password', [LoginController::class, 'loginWithPassword'])->name('auth.login');

        Route::middleware(['admin.auth'])->group(function (): void {
            Route::post('login/refresh', [LoginController::class, 'refresh'])->name('auth.refresh');
            Route::post('login/logout', [LoginController::class, 'logout'])->name('auth.logout');
            Route::get('users/info', [LoginController::class, 'getLoginUserinfo'])->name('users.info');
            Route::put('users/password', [LoginController::class, 'updatePassword'])->name('users.password');
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('menu/routes', [MenuController::class, 'routes'])->name('menus.routes');
            Route::post('upload', [FileUploadController::class, 'file'])->name('upload.file');
            Route::post('upload/image', [FileUploadController::class, 'image'])->name('upload.image');

            Route::get('users', [UsersController::class, 'index'])->middleware('admin.permission:system.user.view')->name('users.index');
            Route::get('users/{user}', [UsersController::class, 'show'])->middleware('admin.permission:system.user.view')->name('users.show');
            Route::post('users', [UsersController::class, 'store'])->middleware('admin.permission:system.user.create')->name('users.store');
            Route::put('users/{user}', [UsersController::class, 'update'])->middleware('admin.permission:system.user.update')->name('users.update');
            Route::delete('users/{user}', [UsersController::class, 'destroy'])->middleware('admin.permission:system.user.delete')->name('users.destroy');

            Route::get('roles', [RoleController::class, 'index'])->middleware('admin.permission:system.role.view')->name('roles.index');
            Route::get('roles/{role}', [RoleController::class, 'show'])->middleware('admin.permission:system.role.view')->name('roles.show');
            Route::post('roles', [RoleController::class, 'store'])->middleware('admin.permission:system.role.create')->name('roles.store');
            Route::put('roles/{role}', [RoleController::class, 'update'])->middleware('admin.permission:system.role.update')->name('roles.update');
            Route::delete('roles/{role}', [RoleController::class, 'destroy'])->middleware('admin.permission:system.role.delete')->name('roles.destroy');

            Route::get('permissions', [PermissionController::class, 'index'])->middleware('admin.permission:system.permission.view')->name('permissions.index');
            Route::get('permissions/{permission}', [PermissionController::class, 'show'])->middleware('admin.permission:system.permission.view')->name('permissions.show');
            Route::post('permissions', [PermissionController::class, 'store'])->middleware('admin.permission:system.permission.create')->name('permissions.store');
            Route::put('permissions/{permission}', [PermissionController::class, 'update'])->middleware('admin.permission:system.permission.update')->name('permissions.update');
            Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->middleware('admin.permission:system.permission.delete')->name('permissions.destroy');

            Route::get('apis', [ApiController::class, 'index'])->middleware('admin.permission:system.api.view')->name('apis.index');
            Route::get('apis/{api}', [ApiController::class, 'show'])->middleware('admin.permission:system.api.view')->name('apis.show');
            Route::post('apis', [ApiController::class, 'store'])->middleware('admin.permission:system.api.create')->name('apis.store');
            Route::put('apis/{api}', [ApiController::class, 'update'])->middleware('admin.permission:system.api.update')->name('apis.update');
            Route::delete('apis/{api}', [ApiController::class, 'destroy'])->middleware('admin.permission:system.api.delete')->name('apis.destroy');

            Route::get('menus', [MenuController::class, 'index'])->middleware('admin.permission:system.menu.view')->name('menus.index');
            Route::get('menus/tree', [MenuController::class, 'tree'])->middleware('admin.permission:system.menu.view')->name('menus.tree');
            Route::get('menus/{menu}', [MenuController::class, 'show'])->middleware('admin.permission:system.menu.view')->name('menus.show');
            Route::post('menus', [MenuController::class, 'store'])->middleware('admin.permission:system.menu.create')->name('menus.store');
            Route::put('menus/{menu}', [MenuController::class, 'update'])->middleware('admin.permission:system.menu.update')->name('menus.update');
            Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->middleware('admin.permission:system.menu.delete')->name('menus.destroy');
        });
    });
