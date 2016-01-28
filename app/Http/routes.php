<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware' => 'api'], function () {
    Route::group(['middleware' => ['guest', 'throttle:5,1']], function () {
        Route::post('auth', 'AuthController@login');
    });

    Route::group(['middleware' => 'auth'], function () {
        Route::delete('auth', 'AuthController@logout');

        Route::group(['middleware' => 'refresh.token'], function () {
            Route::get('/', function () {
                // @todo this is test route only - will be removed later
                return response()->api(['item' => auth()->user()]);
            });
        });
    });
});
