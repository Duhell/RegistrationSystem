<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\AccountController::class,"getAccount"]);
Route::post('/register',[\App\Http\Controllers\AccountController::class,"createAccount"]);

Route::get('/csrf-token', function() {
    return csrf_token();
});
