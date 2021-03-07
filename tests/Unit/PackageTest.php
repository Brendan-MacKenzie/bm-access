<?php

namespace BrendanMacKenzie\BMAccess\Tests\Unit;

use BrendanMacKenzie\BMAccess\Tests\TestCase;
use BrendanMacKenzie\BMAccess\Tests\Models\User;

class PackageTest extends TestCase
{
    /** @test */
    public function no_protected_fields()
    {
        $user = User::factory()->create();

        $user->checkModelPermissions($user);

        $this->assertEquals(false, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function auth_is_self()
    {
        $user = User::factory()->create();

        $user->protectedFields = [
            'field_one' => 'self',
            'field_two' => [
                'self',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([], $user->getHidden());
    }

    /** @test */
    public function auth_is_non_self()
    {
        $user = User::factory()->create();
        $userTwo = $user->replicate();
        $userTwo->id = 2;

        $user->protectedFields = [
            'field_one' => 'self',
            'field_two' => [
                'self',
            ],
        ];

        $user->checkModelPermissions($userTwo);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function auth_is_non_self_but_self_by_field()
    {
        $user = User::factory()->create();
        $userTwo = $user->replicate();
        $userTwo->id = 2;

        $user->protectedFields = [
            'field_one' => 'self.created_by',
            'field_two' => [
                'self.created_by',
            ],
        ];

        $user->checkModelPermissions($userTwo);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([], $user->getHidden());
    }

    /** @test */
    public function auth_has_right_role()
    {
        $user = User::factory()->create();

        $user->assignRole('user');

        $user->protectedFields = [
            'field_one' => 'user',
            'field_two' => [
                'user',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([], $user->getHidden());
    }

    /** @test */
    public function auth_has_wrong_role()
    {
        $user = User::factory()->create();

        $user->assignRole('admin');

        $user->protectedFields = [
            'field_one' => 'user',
            'field_two' => [
                'user',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function auth_has_right_permission()
    {
        $user = User::factory()->create();

        $user->givePermissionTo('see field one');
        $user->givePermissionTo('see field two');

        $user->protectedFields = [
            'field_one' => 'see field one',
            'field_two' => [
                'see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([], $user->getHidden());
    }

    /** @test */
    public function auth_has_wrong_permission()
    {
        $user = User::factory()->create();

        $user->givePermissionTo('see field three');
        $user->givePermissionTo('see field four');

        $user->protectedFields = [
            'field_one' => 'see field one',
            'field_two' => [
                'see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function auth_has_right_role_and_wrong_permission()
    {
        $user = User::factory()->create();

        $user->assignRole('user');
        $user->givePermissionTo('see field three');
        $user->givePermissionTo('see field four');

        $user->protectedFields = [
            'field_one' => 'user:see field one',
            'field_two' => [
                'user:see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function auth_has_right_role_and_right_permission()
    {
        $user = User::factory()->create();

        $user->assignRole('user');
        $user->givePermissionTo('see field one');
        $user->givePermissionTo('see field two');

        $user->protectedFields = [
            'field_one' => 'user:see field one',
            'field_two' => [
                'user:see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([], $user->getHidden());
    }

    /** @test */
    public function auth_has_wrong_role_and_right_permission()
    {
        $user = User::factory()->create();

        $user->assignRole('user');
        $user->givePermissionTo('see field one');
        $user->givePermissionTo('see field two');

        $user->protectedFields = [
            'field_one' => 'admin:see field one',
            'field_two' => [
                'admin:see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function auth_has_wrong_role_and_wrong_permission()
    {
        $user = User::factory()->create();

        $user->assignRole('user');
        $user->givePermissionTo('see field three');
        $user->givePermissionTo('see field four');

        $user->protectedFields = [
            'field_one' => 'admin:see field one',
            'field_two' => [
                'admin:see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function auth_has_both_right_permissions_for_and_check()
    {
        $user = User::factory()->create();

        $user->givePermissionTo('see field one');
        $user->givePermissionTo('see field two');

        $user->protectedFields = [
            'field_one' => 'see field one&&see field two',
            'field_two' => [
                'see field one&&see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([], $user->getHidden());
    }

    /** @test */
    public function auth_has_only_one_right_permissions_for_and_check()
    {
        $user = User::factory()->create();

        $user->givePermissionTo('see field one');

        $user->protectedFields = [
            'field_one' => 'see field one&&see field two',
            'field_two' => [
                'see field one&&see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function auth_has_wrong_permissions_for_and_check()
    {
        $user = User::factory()->create();

        $user->protectedFields = [
            'field_one' => 'see field one&&see field two',
            'field_two' => [
                'see field one&&see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function auth_has_both_right_permissions_for_or_check()
    {
        $user = User::factory()->create();

        $user->givePermissionTo('see field one');
        $user->givePermissionTo('see field two');

        $user->protectedFields = [
            'field_one' => 'see field one||see field two',
            'field_two' => [
                'see field one||see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([], $user->getHidden());
    }

    /** @test */
    public function auth_has_only_one_right_permissions_for_or_check()
    {
        $user = User::factory()->create();

        $user->givePermissionTo('see field one');

        $user->protectedFields = [
            'field_one' => 'see field one||see field two',
            'field_two' => [
                'see field one||see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([], $user->getHidden());
    }

    /** @test */
    public function auth_has_wrong_permissions_for_or_check()
    {
        $user = User::factory()->create();

        $user->protectedFields = [
            'field_one' => 'see field one||see field two',
            'field_two' => [
                'see field one||see field two',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function auth_has_right_permissions_for_combined_and_or_check()
    {
        $user = User::factory()->create();

        $user->givePermissionTo('see field one');
        $user->givePermissionTo('see field two');
        $user->givePermissionTo('see all fields');

        $user->protectedFields = [
            'field_one' => '(see field one&&see field two)||see all fields',
            'field_two' => [
                '(see field one&&see field two)||see all fields',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([], $user->getHidden());
    }

    /** @test */
    public function auth_has_only_one_permission_for_combined_and_or_check()
    {
        $user = User::factory()->create();

        $user->givePermissionTo('see all fields');

        $user->protectedFields = [
            'field_one' => '(see field one&&see field two)||see all fields',
            'field_two' => [
                '(see field one&&see field two)||see all fields',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([], $user->getHidden());
    }

    /** @test */
    public function auth_has_wrong_permissions_for_combined_and_or_check()
    {
        $user = User::factory()->create();

        $user->givePermissionTo('see field one');

        $user->protectedFields = [
            'field_one' => '(see field one&&see field two)||see all fields',
            'field_two' => [
                '(see field one&&see field two)||see all fields',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
            'field_two',
        ], $user->getHidden());
    }

    /** @test */
    public function rule_is_function()
    {
        $user = User::factory()->create();

        $user->protectedFields = [
            'field_one' => 'function',
            'field_two' => [
                'function',
            ],
        ];

        $user->checkModelPermissions($user);

        $this->assertEquals(true, isset($user->protectedFields));

        $this->assertEquals([
            'field_one',
        ], $user->getHidden());
    }
}
