<?php

use App\Models\RoleType;

return [

    // this is list of all available permissions in system
    'all' => [
        'roles.index',
        'users.index',
        'users.store',
    ],

    // this is assignment of permissions to user roles
    'roles' => [
        RoleType::ADMIN => [
            '*', // all permissions (don't add anything into this array)
        ],

        RoleType::DEALER => [
            'users.index',
        ],

        RoleType::DEVELOPER => [
            'users.index',
        ],

        RoleType::CLIENT => [

        ],
    ],
];
