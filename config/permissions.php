<?php

use App\Models\RoleType;

return [

    // this is list of all available permissions in system
    'all' => [
        'roles.index',
    ],

    // this is assignment of permissions to user roles
    'roles' => [
        RoleType::ADMIN => [
            '*', // all permissions (don't add anything into this array)
        ],

        RoleType::DEALER => [

        ],

        RoleType::DEVELOPER => [

        ],

        RoleType::CLIENT => [

        ],
    ],
];
