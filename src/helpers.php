<?php

if (!function_exists('admin_path')) {
    function admin_path($path = ''): string
    {
        return ucfirst(config('admin.path')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}
