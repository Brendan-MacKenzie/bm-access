<?php

namespace BrendanMacKenzie\BMAccess\Tests;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Config;
use Spatie\Permission\Models\Permission;
use BrendanMacKenzie\BMAccess\BMAccessServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $permissionConfig = include __DIR__.'/../vendor/spatie/laravel-permission/config/permission.php';
        Config::set('permission', $permissionConfig);

        Role::create([
            'name' => 'user',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Role::create([
            'name' => 'admin',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::create([
            'name' => 'see field one',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::create([
            'name' => 'see field two',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::create([
            'name' => 'see field three',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::create([
            'name' => 'see field four',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        Permission::create([
            'name' => 'see all fields',
            'guard_name' => config('auth.defaults.guard'),
        ]);

        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->registerPermissions();
    }

    protected function getPackageProviders($app)
    {
        return [
            BMAccessServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__.'/Migrations/create_users_table.php.stub';
        include_once __DIR__.'/Migrations/create_permission_tables.php.stub';

        (new \CreateUsersTable)->up();
        (new \CreatePermissionTables)->up();
    }
}
