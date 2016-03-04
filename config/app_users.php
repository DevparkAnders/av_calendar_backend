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
        'first_name' => 'Marcin',
        'last_name' => 'Nabiałek',
    ],
    [
        'email' => 'a.fenzel@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::ADMIN,
        'first_name' => 'Andrzej',
        'last_name' => 'Fenzel',
    ],
    [
        'email' => 'andrzej.z@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'Andrzej',
        'last_name' => 'Żmudziński',
    ],
    [
        'email' => 'bartek.k@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'Bartek',
        'last_name' => 'K',
    ],
    [
        'email' => 'd.slebioda@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::ADMIN,
        'first_name' => 'Dominik',
        'last_name' => 'Ślebioda',
    ],
    [
        'email' => 'j.idkowski@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'J',
        'last_name' => 'Idkowski',
    ],
    [
        'email' => 'jakub.c@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'Jakub',
        'last_name' => 'C',
    ],
    [
        'email' => 'jaroslaw.f@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'Jarosław',
        'last_name' => 'Furmanek',
    ],
    [
        'email' => 'jaroslaw.t@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'Jarosław',
        'last_name' => 'Tkaczyk',
    ],
    [
        'email' => 'lukasz.k@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'Łukasz',
        'last_name' => 'K',
    ],
    [
        'email' => 'mateusz.u@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'Mateusz',
        'last_name' => 'U',
    ],
    [
        'email' => 'p.slebioda@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'P',
        'last_name' => 'Ślebioda',
    ],
    [
        'email' => 'patryk.t@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'Patryk',
        'last_name' => 'Trochowski',
    ],
    [
        'email' => 'przemyslaw.t@devpark.pl',
        'password' => str_random(),
        'role' => App\Models\RoleType::DEVELOPER,
        'first_name' => 'Przemysław',
        'last_name' => 'Tkaczyk',
    ],
];
