<?php

namespace KxAdmin\Controllers;

use KxAdmin\Contracts\DashboardProvider;
use KxAdmin\Support\DefaultDashboardProvider;

class DashboardController extends AdminController
{
    public function index()
    {
        $providerClass = config('admin.dashboard.provider', DefaultDashboardProvider::class);

        if (!is_string($providerClass) || !class_exists($providerClass)) {
            $providerClass = DefaultDashboardProvider::class;
        }

        /** @var DashboardProvider $provider */
        $provider = app($providerClass);

        return $this->success($provider->getData());
    }
}
