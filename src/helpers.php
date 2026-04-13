<?php

if (!function_exists('admin_path')) {
    function admin_path(string $path = ''): string
    {
        $basePath = rtrim(config('admin.path', app_path('Admin')), DIRECTORY_SEPARATOR);

        return $path === ''
            ? $basePath
            : $basePath . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR);
    }
}
