<?php

use AsfyCode\Utils\Route;
use App\Http\Controllers\WEB\{
    LoginController,
};
use App\Http\Controllers\WEB\ThietKe\{
    DesignController,
    OrderController,
    HomeController
};

Route::get('/login',[LoginController::class,'showFormLogin']);
Route::post('/login',[LoginController::class,'login'])->name('login');
Route::get('/login/callback',[LoginController::class,'googleCallback']);
Route::get('/logout',[LoginController::class,'logout'])->name('logout');

Route::middleware('auth.thietke')->group(function(){
    Route::get('/',[HomeController::class,'index']);
    Route::get('/design-status',[DesignController::class,'status']);
    Route::prefix('orders')->names('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
    });
});
