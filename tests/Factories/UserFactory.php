<?php

namespace  BrendanMacKenzie\BMAccess\Tests\Factories;

use BrendanMacKenzie\BMAccess\Tests\Models\User as ModelsUser;
use Orchestra\Testbench\Factories\UserFactory as TestbenchUserFactory;

class UserFactory extends TestbenchUserFactory
{
    protected $model = ModelsUser::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => 1,
            'field_one' => 'Value one',
            'field_two' => 'Value two',
            'created_by' => 2,
        ];
    }
}
