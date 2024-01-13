<?php

use Illuminate\Support\Facades\Route;

Route::get('/csrf-token', function() {
    return csrf_token();
});

Route::controller(\App\Http\Controllers\AccountController::class)->group(function(){
    Route::get('/{id?}', "getAccount");
    Route::post('/register',"createAccount");
});



