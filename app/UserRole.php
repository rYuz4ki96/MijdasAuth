<?php

namespace App\Role;

/***
 * Class UserRole
 * @package App\Role
 */
class UserRole {

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_COORDINATOR = 'ROLE_COORDINATOR';
    const ROLE_TUTOR = 'ROLE_TUTOR';
    const ROLE_STUDENT = 'ROLE_STUDENT';

    /**
     * @var array
     */
    protected static $roleHierarchy = [
        self::ROLE_ADMIN => ['*'],
        self::ROLE_COORDINATOR => [
            self::ROLE_TUTOR
        ],
        self::ROLE_TUTOR => [
            self::ROLE_STUDENT
        ],
        self::ROLE_STUDENT => []
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
            static::ROLE_ADMIN =>'Admin',
            static::ROLE_COORDINATOR => 'Coordinator',
            static::ROLE_TUTOR => 'Tutor',
            static::ROLE_STUDENT => 'Student',
        ];
    }

}
