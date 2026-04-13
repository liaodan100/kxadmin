<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $tables = config('admin.tables', []);

        $usersTable = $tables['users'] ?? 'admin_users';
        if (!Schema::hasTable($usersTable)) {
            Schema::create($usersTable, function (Blueprint $table): void {
                $table->id();
                $table->string('username', 50)->unique();
                $table->string('password');
                $table->string('name', 50);
                $table->string('avatar')->nullable();
                $table->string('email', 100)->nullable();
                $table->string('mobile', 20)->nullable();
                $table->boolean('status')->default(true);
                $table->boolean('is_super')->default(false);
                $table->timestamp('last_login_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        $rolesTable = $tables['roles'] ?? 'admin_roles';
        if (!Schema::hasTable($rolesTable)) {
            Schema::create($rolesTable, function (Blueprint $table): void {
                $table->id();
                $table->string('name', 50);
                $table->string('code', 50)->unique();
                $table->string('description')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        $permissionsTable = $tables['permissions'] ?? 'admin_permissions';
        if (!Schema::hasTable($permissionsTable)) {
            Schema::create($permissionsTable, function (Blueprint $table): void {
                $table->id();
                $table->string('name', 50);
                $table->string('code', 100)->unique();
                $table->string('group', 50)->nullable();
                $table->string('description')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        $apisTable = $tables['apis'] ?? 'admin_apis';
        if (!Schema::hasTable($apisTable)) {
            Schema::create($apisTable, function (Blueprint $table): void {
                $table->id();
                $table->string('name', 100);
                $table->string('method', 10);
                $table->string('path');
                $table->string('description')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->softDeletes();
                $table->unique(['method', 'path']);
            });
        }

        $menusTable = $tables['menus'] ?? 'admin_menus';
        if (!Schema::hasTable($menusTable)) {
            Schema::create($menusTable, function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('parent_id')->default(0)->index();
                $table->string('type', 20)->default('menu');
                $table->string('path')->nullable();
                $table->string('name', 50);
                $table->string('component')->nullable();
                $table->string('route_name', 100)->nullable();
                $table->string('redirect')->nullable();
                $table->string('title', 100);
                $table->string('icon', 100)->nullable();
                $table->unsignedInteger('sort')->default(0);
                $table->boolean('keep_alive')->default(false);
                $table->boolean('hidden')->default(false);
                $table->boolean('status')->default(true);
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        $roleUsersTable = $tables['role_users'] ?? 'admin_role_user';
        if (!Schema::hasTable($roleUsersTable)) {
            Schema::create($roleUsersTable, function (Blueprint $table): void {
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('user_id');
                $table->primary(['role_id', 'user_id']);
            });
        }

        $rolePermissionsTable = $tables['role_permissions'] ?? 'admin_permission_role';
        if (!Schema::hasTable($rolePermissionsTable)) {
            Schema::create($rolePermissionsTable, function (Blueprint $table): void {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');
                $table->primary(['permission_id', 'role_id']);
            });
        }

        $permissionApisTable = $tables['permission_apis'] ?? 'admin_api_permission';
        if (!Schema::hasTable($permissionApisTable)) {
            Schema::create($permissionApisTable, function (Blueprint $table): void {
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('api_id');
                $table->primary(['permission_id', 'api_id']);
            });
        }

        $roleMenusTable = $tables['role_menus'] ?? 'admin_menu_role';
        if (!Schema::hasTable($roleMenusTable)) {
            Schema::create($roleMenusTable, function (Blueprint $table): void {
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('menu_id');
                $table->primary(['role_id', 'menu_id']);
            });
        }
    }

    public function down(): void
    {
        $tables = config('admin.tables', []);

        Schema::dropIfExists($tables['role_menus'] ?? 'admin_menu_role');
        Schema::dropIfExists($tables['permission_apis'] ?? 'admin_api_permission');
        Schema::dropIfExists($tables['role_permissions'] ?? 'admin_permission_role');
        Schema::dropIfExists($tables['role_users'] ?? 'admin_role_user');
        Schema::dropIfExists($tables['menus'] ?? 'admin_menus');
        Schema::dropIfExists($tables['apis'] ?? 'admin_apis');
        Schema::dropIfExists($tables['permissions'] ?? 'admin_permissions');
        Schema::dropIfExists($tables['roles'] ?? 'admin_roles');
        Schema::dropIfExists($tables['users'] ?? 'admin_users');
    }
};
