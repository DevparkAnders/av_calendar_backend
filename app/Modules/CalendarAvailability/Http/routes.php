<?php
// @todo add authorization here
Route::group(['middleware' => ['api', 'auth']], function () {
    Route::get('users/{user}/availabilities/{day}', 'CalendarAvailabilityController@show');
    Route::post('users/{user}/availabilities/{day}', 'CalendarAvailabilityController@store');
    Route::get('users/availabilities/', 'CalendarAvailabilityController@index');
//    Route::get('users/availabilities/')
//    Route::resource('calendaravailabilities', 'CalendarAvailabilityController');
});
