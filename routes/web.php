<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::post('/login', function () {
    return redirect('/home');
});

Route::get('/home', function () {
    return view('welcome');
});
