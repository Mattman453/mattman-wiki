<?php

use Illuminate\Support\Facades\Route;

Route::get('/health-token', function() {
    return response()->json([
        'message' => 'OK',
    ], 200);
});

Route::get('/', 'App\Http\Controllers\MainController@homeView')->name('game.home');
Route::get('/about', 'App\Http\Controllers\MainController@aboutView');
Route::get('/login', 'App\Http\Controllers\AuthController@getLoginView')->name('auth.login');
Route::post('/login', 'App\Http\Controllers\AuthController@login');
Route::post('/logout', 'App\Http\Controllers\AuthController@logout');
Route::get('/register', 'App\Http\Controllers\AuthController@getRegisterView');
Route::post('/register', 'App\Http\Controllers\AuthController@register');
Route::get('/verify/email/{id}/{hash}', 'App\Http\Controllers\AuthController@verifyEmail')->middleware('signed')->name('verification.verify');
Route::get('/verify', 'App\Http\Controllers\AuthController@getVerifyEmailView')->middleware('auth')->name('verification.notice');
Route::post('/send-verification', 'App\Http\Controllers\AuthController@sendEmailVerification')->middleware('auth')->name('verification.send');
Route::get('/game/{game}/{subtitle?}/{page?}', 'App\Http\Controllers\GamePageController@showStandardPage');
