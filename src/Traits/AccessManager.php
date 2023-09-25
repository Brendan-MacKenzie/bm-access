<?php

namespace BrendanMacKenzie\BMAccess\Traits;

use Exception;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

trait AccessManager
{
    public function toArray()
    {
        $authUser = Auth::user();

        $this->checkModelPermissions($authUser);

        if (function_exists('additionalToArrayImplementations')) {
            $this->additionalToArrayImplementations();
        }

        return parent::toArray();
    }

    public function checkModelPermissions($authUser = null)
    {
        if (!isset($this->protectedFields)) {
            return;
        }

        if (!$authUser) {
            return;
        }

        // Loop through each key
        foreach ($this->protectedFields as $field => $rule) {
            if (!in_array($field, $this->getHidden())) {
                continue;
            }

            switch (gettype($rule)) {
                case 'array':
                    foreach ($this->protectedFields[$field] as $subRule) {
                        $setVisible = $this->testFieldRule($authUser, $field, $subRule);
                        if ($setVisible) {
                            break;
                        }
                    }
                    break;

                case 'string':
                    $setVisible = $this->testFieldRule($authUser, $field, $rule);
                    break;

                default:
                    break;
            }

            if (isset($setVisible) && $setVisible) {
                $this->makeVisible($field);
            }
        }
    }

    private function testFieldRule($authUser, $field, $rule)
    {
        if ($rule === 'function') {
            $functionName = Str::camel($field).'FieldTest';

            try {
                return $this->$functionName();
            } catch (Exception $e) {
                return false;
            }
        }

        if (str_starts_with($rule, 'self')) {
            return $this->testSelf($authUser, $rule);
        }

        if (str_contains($rule, ':')) {
            [$role, $permissions] = explode(':', $rule);
            if ($this->isRole($role) ? $this->testRole($authUser, $role) : false) {
                return $this->testPermissions($authUser, $permissions);
            }
        } else {
            return $this->isRole($rule) ? $this->testRole($authUser, $rule) : $this->testPermissions($authUser, $rule);
        }

        return false;
    }

    private function testSelf($authUser, $rule)
    {
        $userIdField = 'id';

        $roleParts = explode(':', $rule);
        $role = $roleParts[0];
        $permissions = count($roleParts) > 1 ? $roleParts[1] : null;

        if (str_contains($role, '.')) {
            [$role, $userIdField] = explode('.', $role);
        }

        if ($this->$userIdField === $authUser->id) {
            if ($permissions) {
                return $this->testPermissions($authUser, $permissions);
            }

            return true;
        }

        return false;
    }

    private function isRole($role)
    {
        $roles = Role::all()->pluck('name')->all();

        return in_array($role, $roles);
    }

    private function testRole($authUser, $role)
    {
        return $authUser->hasRole($role);
    }

    private function testPermissions($authUser, $permissions)
    {
        if ($permissions === '') {
            return true;
        }

        $string = preg_replace('/([^|&]+)/', '$authUser->can(\'$1\')', $permissions);

        return eval('return '.$string.';');
    }
}
