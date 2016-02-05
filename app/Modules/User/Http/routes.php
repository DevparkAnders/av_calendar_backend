<?php

Route::group(['middleware' => 'api'], function () {
    Route::group(['middleware' => ['throttle:5,1', 'guest']], function () {
        // log in
        Route::post('auth', 'AuthController@login')->name('auth.store');
        // password reset
        Route::post('password/reset', 'PasswordController@sendResetLinkEmail')
            ->name('password.reset.post');
        Route::put('password/reset', 'PasswordController@reset')
            ->name('password.reset.put');
    });

    Route::group(['middleware' => ['throttle:60,1', 'auth']], function () {
        // log out
        Route::delete('auth', 'AuthController@logout')->name('auth.delete');

        Route::group(['middleware' => ['refresh.token', 'authorize']],
            function () {
                // roles
                Route::get('roles', 'RoleController@index')
                    ->name('roles.index');
                // users
                Route::get('users', 'UserController@index')
                    ->name('users.index');
                Route::post('users', 'UserController@store')
                    ->name('users.store');
            });
    });
});
