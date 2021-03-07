<?php

namespace BrendanMacKenzie\BMAccess\Tests\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use BrendanMacKenzie\BMAccess\Traits\AccessManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use BrendanMacKenzie\BMAccess\Tests\Factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, AccessManager, HasFactory;

    public function guardName()
    {
        return config('auth.defaults.guard');
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function fieldTwoFieldTest()
    {
        return $this->field_two === 'Value two';
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'field_one',
        'field_two',
    ];
}
