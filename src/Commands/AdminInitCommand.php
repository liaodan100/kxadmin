<?php

namespace KxAdmin\Commands;

use Illuminate\Console\Command;
use KxAdmin\Support\AdminInitializer;

class AdminInitCommand extends Command
{
    protected $signature = 'admin:init
                            {--username=admin : Initial admin username}
                            {--password=123456 : Initial admin password}
                            {--name=超级管理员 : Initial admin display name}
                            {--force : Reset built-in seed data before init}';

    protected $description = '后台初始化数据';

    public function handle(AdminInitializer $initializer): int
    {
        $initializer->initialize(
            (string) $this->option('username'),
            (string) $this->option('password'),
            (string) $this->option('name'),
            (bool) $this->option('force')
        );

        $this->info('Admin data initialized.');

        return self::SUCCESS;
    }
}
