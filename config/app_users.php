<?php

/**
 * Those users will be created if they don't exist in application. Main admin
 * user is defined in .env file
 */
return [
    [
        'email' => 'marcin.n@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'a.fenzel@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::ADMIN,
    ],
    [
        'email' => 'andrzej.z@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'bartek.k@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'd.slebioda@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::ADMIN,
    ],
    [
        'email' => 'j.idkowski@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'jakub.c@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'jaroslaw.f@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'jaroslaw.t@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'lukasz.k@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'mateusz.u@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'p.slebioda@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'patryk.t@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
    [
        'email' => 'przemyslaw.t@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
    ],
];
