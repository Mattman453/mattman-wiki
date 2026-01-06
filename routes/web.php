<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\MainController@homeView');
Route::get('/about', 'App\Http\Controllers\MainController@aboutView');