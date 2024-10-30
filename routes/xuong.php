<?php

use AsfyCode\Utils\Route;
use App\Http\Controllers\WEB\{
    HomeController,
    LoginController,
};

Route::get('/login',[LoginController::class,'showFormLogin']);
Route::post('/login',[LoginController::class,'login'])->name('login');
Route::get('/login/callback',[LoginController::class,'googleCallback']);
Route::get('/logout',[LoginController::class,'logout'])->name('logout');

Route::middleware('auth.xuong')->group(function(){
    Route::get('/',function(){
        echo 'đây là module xưởng';
    });
});




    


