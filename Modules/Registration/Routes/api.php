<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware(['is_authorized', 'employee', 'is_not_registered', 'cors'])->group(function () {
    Route::post('registration', 'RegistrationController@store')->name('registration.store');
});
