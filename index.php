<?php
use AsfyCode\Utils\Request;
use App\Http\Kernel;
use AsfyCode\Container\Container;
use App\Providers\AppServiceProvider;
use App\Providers\RouteServiceProvider;

define('BASE_PATH', __DIR__);

session_start();

require_once 'vendor/autoload.php';
require_once 'config/app.php';

// Load biến môi trường .env
Dotenv\Dotenv::createImmutable(BASE_PATH)->load();

// Khởi tạo container
$container = new Container();

// Đăng ký các route 
$routeServiceProvider = new RouteServiceProvider();
$routeServiceProvider->boot();
// Đăng ký dịch vụ cho website
$appServiceProvider = new AppServiceProvider();
$appServiceProvider->register($container);

// Khởi tạo Kernel
$kernel = $container->make(Kernel::class);
// Khởi tạo Request
$request = $container->make(Request::class);

// Sử lý request
$response = $kernel->handle($request);
// Trả về response
$response->send();

// Xử lý công việc cần làm sau phản hồi
$kernel->terminate($request, $response);
