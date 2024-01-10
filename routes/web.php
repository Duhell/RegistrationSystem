<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'data'=> \App\Models\User::all()
    ]);
});

Route::post('/register',function(){

});
