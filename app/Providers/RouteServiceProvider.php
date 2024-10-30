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
        
        // Đăng ký routes cho API
        Route::prefix('api')->names('api')->middleware(['api','auth'])->group(function(){
            require_once BASE_PATH . '/routes/api.php';
        });

        // Đăng ký routes cho web
        Route::middleware('web')->group(function(){
            $host = $this->request->host();
            $this->redirectRoute($host);
        });
    }

    public function redirectRoute($host){
        $routes = $this->routes;
        foreach($routes as $key => $view){
            if($key == $host){
                require_once BASE_PATH . '/routes/'.$view.'.php';
            }
        }
    }
}
