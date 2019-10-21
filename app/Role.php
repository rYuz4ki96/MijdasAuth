<?php

namespace App\Role;

use App\User;

/***
 * Class UserRole
 * @package App\Role
 */
class UserRole {

    const admin = 'admin';
    const coordinator = 'coordinator';
    const tutor = 'tutor';
    const student = 'student';

    /**
     * @var array
     */
    protected static $roleHierarchy = [
        self::admin => ['*'],
        self::coordinator => [
            self::tutor,
            self::student
        ],
        self::tutor => [
            self::student
        ],
        self::student => []
    ];

    /**
     * @param string $role
     * @return array
     */
    public static function getAllowedRoles(string $role)
    {
        if (isset(self::$roleHierarchy[$role])) {
            return self::$roleHierarchy[$role];
        }

        return [];
    }

    /***
     * @return array
     */
    public static function getRoleList()
    {
        return [
            static::admin =>'Admin',
            static::coordinator => 'Coordinator',
            static::tutor => 'Tutor',
            static::student => 'Student',
        ];
    }

}

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
        if ($user->hasRole(UserRole::admin)) {
            return true;
        }
        else if($user->hasRole(UserRole::coordinator)) {
            $allowedRoles = UserRole::getAllowedRoles(UserRole::coordinator);

            if (in_array($role, $allowedRoles)) {
                return true;
            }
        }
        else if($user->hasRole(UserRole::tutor)) {
            $allowedRoles = UserRole::getAllowedRoles(UserRole::tutor);

            if (in_array($role, $allowedRoles)) {
                return true;
            }
        }

        return $user->hasRole($role);
    }
}
