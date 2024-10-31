<?php

namespace App\Providers;

use AsfyCode\Utils\Request;
use AsfyCode\Utils\Route;

class RouteServiceProvider
{
    private $routes = [
        'thietke.gacmai.vn' => 'web',
        'xuong.gacmai.vn' => 'xuong',
    ];
    private $request;
    public function boot()
    {
        $this->request = new Request();
        $this->registerRoutes();
    }

    protected function registerRoutes()
    {
        // Đăng ký routes cho web
        Route::middleware('web')->group(function(){
            $host = $this->request->host();
            $path = $this->redirectRoute($host);
            require_once $path;
        });

        // Đăng ký routes cho API
        Route::prefix('api')->names('api')->middleware(['api','auth'])->group(function(){
            require_once BASE_PATH . '/routes/api.php';
        });
    }

    public function redirectRoute($host){
        $routes = $this->routes;
        for($i = 0; $i < count($routes); $i++){
            $keys = array_keys($routes);
            $key = $keys[$i];
            $view = $routes[$key];
            if($key == $host){
                return BASE_PATH . '/routes/'.$view.'.php';
            }
        }
    }
}
