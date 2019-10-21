<?php

namespace App\Role;

use App\User;

/**
 * Class RoleChecker
 * @package App\Role
 */
class RoleChecker
{
    /**
     * @param User $user
     * @param string $role
     * @return bool
     */
    public function check(User $user, string $role)
    {
        // Admin has everything
        if ($user->hasRole(UserRole::ROLE_ADMIN)) {
            return true;
        }
        else if($user->hasRole(UserRole::ROLE_COORDINATOR)) {
            $allowedRoles = UserRole::getAllowedRoles(UserRole::ROLE_COORDINATOR);

            if (in_array($role, $allowedRoles)) {
                return true;
            }
        }
        else if($user->hasRole(UserRole::ROLE_TUTOR)) {
            $allowedRoles = UserRole::getAllowedRoles(UserRole::ROLE_TUTOR);

            if (in_array($role, $allowedRoles)) {
                return true;
            }
        }

        return $user->hasRole($role);
    }
}
