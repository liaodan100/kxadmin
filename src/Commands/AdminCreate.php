<?php
namespace KxAdmin\Commands;

use Illuminate\Console\Command;
class AdminCreate extends Command
{
    protected $signature = 'admin:create {account} {password} {name}';

    protected $description = 'Create a new admin user';

    public function handle()
    {
        $account = $this->argument('account');
        $password = $this->argument('password');
        $name = $this->argument('name');
        $model = resolve(config('admin.admin_model'));
        if ($model::where('account', $account)->exists()) {
            $this->error("Account [{$account}] already exists!");
            return;
        }

        $model::create([
            'account' => $account,
            'password' => bcrypt($password),
            'name' => $name
        ]);
    }
}
