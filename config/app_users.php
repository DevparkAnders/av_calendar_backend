<?php

/**
 * Those users will be created if they don't exist in application. Main admin user
 * is defined in .env file 
 */
return [
    [
        'email' => 'marcin.n@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
];
