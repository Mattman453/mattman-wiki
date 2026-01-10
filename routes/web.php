<?php

use Illuminate\Support\Facades\Route;

Route::get('/health-token', function() {
    return response()->json([
        'message' => 'OK',
    ], 200);
});

Route::get('/', 'App\Http\Controllers\MainController@homeView')->name('game.home');
Route::get('/about', 'App\Http\Controllers\MainController@aboutView');
Route::get('/login', 'App\Http\Controllers\AuthController@getLoginView');
Route::get('/register', 'App\Http\Controllers\AuthController@getRegisterView');
Route::get('/game/{game}', 'App\Http\Controllers\GamePageController@showGamePage');
