<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/health-token', function() {
    Log::debug('health check');
    return response()->json([
        'message' => 'OK',
    ], 200);
});

Route::get('/', 'App\Http\Controllers\MainController@homeView')->name('home');
Route::get('/about', 'App\Http\Controllers\MainController@aboutView');
Route::get('/login', 'App\Http\Controllers\AuthController@getLoginView');
Route::get('/game/{game}', 'App\Http\Controllers\GamePageController@showGamePage');