<?php namespace App\Http;

use App\Http\Middleware\AuthApiMiddleware;
use AsfyCode\Container\Container;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\AuthXuong;
use App\Http\Middleware\CheckSessionUser;
use AsfyCode\Middleware\HandleAfter;
use AsfyCode\Middleware\HandleBefore;
use AsfyCode\Traits\HttpRequest;


class Kernel
{
    use HttpRequest;
    
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public $middlewares = [
        // CheckSessionUser::class, // Bỏ
        HandleBefore::class
    ];

    protected $routeMiddleware = [
        'web' => [
            // Các middleware cho web (cần phát triển thêm)
        ],
        'api' => [
            // Các middleware cho API (cần phát triển thêm)
        ],
    ];
    protected $middlewareAliases = [
        'auth' => AuthApiMiddleware::class,
        'auth.thietke' => AuthMiddleware::class,
        'auth.xuong' => AuthXuong::class
    ];
    public $middlewareAfter = [
        HandleAfter::class
    ];

}
