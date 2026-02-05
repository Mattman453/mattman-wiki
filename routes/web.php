<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['throttle:global'])->group(function () {
    // Static Pages
    Route::get('/', 'App\Http\Controllers\MainController@homeView')->name('game.home');
    Route::get('/about', 'App\Http\Controllers\MainController@aboutView');
    
    // Authentication
    Route::prefix('/login')->group(function () {
        Route::get('/', 'App\Http\Controllers\AuthController@getLoginView')->name('auth.login');
        Route::post('/', 'App\Http\Controllers\AuthController@login');
    });
    Route::post('/register', 'App\Http\Controllers\AuthController@register');
    Route::post('/logout', 'App\Http\Controllers\AuthController@logout');
    Route::prefix('/verify')->group(function () {
        Route::get('/email/{id}/{hash}', 'App\Http\Controllers\AuthController@verifyEmail')->middleware('signed')->name('verification.verify');
        Route::get('/', 'App\Http\Controllers\AuthController@getVerifyEmailView')->middleware('auth')->name('verification.notice');
    });
    Route::post('/send-verification', 'App\Http\Controllers\AuthController@sendEmailVerification')->middleware('auth')->name('verification.send');
    
    // Standard Pages
    Route::prefix('/game')->group(function () {
        Route::post('/new_page', 'App\Http\Controllers\PageController@createPage')->middleware(['verified']);
        Route::post('/edit/{game}/{subtitle?}/{page?}', 'App\Http\Controllers\PageController@updatePage')->middleware(['auth', 'verified']);
        Route::get('/{game}/{subtitle?}/{page?}', 'App\Http\Controllers\PageController@showStandardPage');
    });
    
    // Generic Post Requests
    Route::post('/update-lifetime', 'App\Http\Controllers\MainController@updateLifetime');

    // No Page Found
    Route::fallback(function () {
        $requestUri = request()->path();
        if (str_ends_with($requestUri, '.php')) {
            $newUri = substr($requestUri, 0, -4);
            return redirect($newUri);
        }
        return redirect(route('game.home'));
    });
});
