<?php

use App\Http\Controllers\API\{
    CategoryController,
    CostAllocationController,
    GroupController,
    LogController,
    OrderController,
    UserController,
    SupplierInvoicesController,
    CustomerController,
    CustomerNotifyController,
    DebtController,
    FinanceController,
    HomeController,
    OrderExpenseController,
    VNController,
    OrderFileController,
    SupplierController,
    SaleChannelController,
    OrderUpdateLogController,
    PayRollDetailController,
    BankAccountController,
    FactoryController,
    TransactionController,
    UnitController,
};
use App\Http\Controllers\API\ThietKe\DesignController;
use App\Http\Controllers\OrderLogController;
use App\Http\Controllers\API\Xuong\OrderController as XuongOrderController;
use App\Http\Controllers\API\Xuong\OrderLogController as XuongOrderLogController;
use AsfyCode\Utils\Route;

Route::get('/home',[HomeController::class,'index']);
Route::names('users')->apiResource('users',UserController::class);
Route::get('/users/total-price',[UserController::class,'totalPrice']);
Route::post('/login-code',[UserController::class,'loginCode']);
Route::apiResource('bank-account', BankAccountController::class);
Route::apiResource('transactions', TransactionController::class);
Route::names('groups')->apiResource('groups',GroupController::class);
Route::names('factories')->apiResource('factories',FactoryController::class);
Route::names('units')->apiResource('units',UnitController::class);
Route::names('logs')->apiResource('logs',LogController::class);
Route::names('debt')->apiResource('debt',DebtController::class);
Route::names('sale-channel')->apiResource('sale-channel',SaleChannelController::class);
Route::names('customers')->apiResource('customers',CustomerController::class);
Route::get('customers-expired',[CustomerController::class,'expired']);
Route::apiResource('suppliers', SupplierController::class);
Route::apiResource('customer-notification',CustomerNotifyController::class);
Route::post('customers-status',[CustomerController::class,'updateStatus']);
Route::names('categories')->apiResource('categories',CategoryController::class);
Route::names('orders')->apiResource('orders',OrderController::class);
Route::names('order-logs')->apiResource('order-logs',XuongOrderLogController::class);
Route::get('orders-xuong',[XuongOrderController::class,'index']);
Route::post('orders-xuong',[XuongOrderController::class,'store']);
Route::put('orders-xuong/{id}',[XuongOrderController::class,'update']);
Route::post('orders-status',[OrderController::class,'updateStatus']);
Route::post('design-status',[DesignController::class,'status']);
Route::post('xuong-status',[DesignController::class,'statusXuong']);
Route::names('order-files')->apiResource('order-files',OrderFileController::class);
Route::names('order-update-logs')->apiResource('order-update-logs',OrderUpdateLogController::class);
Route::apiResource('order-expenses',OrderExpenseController::class);
Route::apiResource('cost-allocation', CostAllocationController::class);
Route::prefix('vn')->group(function(){
    Route::get('provinces',[VNController::class,'provinces']);
    Route::get('districts/{id}',[VNController::class,'districts']);
    Route::get('wards/{id}',[VNController::class,'wards']);
});
Route::prefix('supplier-invoices')->group(function () {
    Route::get('/', [SupplierInvoicesController::class, 'index']);
    Route::post('/add-cong-no/{id}', [SupplierInvoicesController::class, 'addCongNo']);
    Route::post('/thanhtoan-cong-no/{id}', [SupplierInvoicesController::class, 'thanhToanCongNo']);
});
Route::get('finance', [FinanceController::class, 'index']);
Route::prefix('payroll-detail')->group(function () {
    Route::get('/price', [PayRollDetailController::class, 'getPrice']);
});