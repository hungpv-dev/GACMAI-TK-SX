<?php 
namespace AsfyCode\Traits;
use AsfyCode\Utils\Route;
use ReflectionMethod;
use Response;
trait HttpRequest
{
    private $request;
    public function handle($request)
    {
        $this->request= $request;
        
        require_once BASE_PATH.'/config/database.php';

        // Xử lý middleware chung trước
        foreach ($this->middlewares as $middleware) {
            (new $middleware())->handle($request);
        }
        // Xử lý route
        $route = Route::dispatch($request);
        if(is_array($route)){
            $response = $this->routeNavigation($route);
        }else{
            $response = $route;
        }

        if ($response instanceof Response) {
            return $response;
        } else {
            return new Response();
        }
    }

    private function routeNavigation($route){
        $middlewares = $route['middleware'];
        $this->handleListMiddleware($middlewares);
        $response = null;
        $callback = $route['callback'];
        $data = $route['data'];
        if (is_array($callback)) {
            $controller = $callback[0];
            $action = $callback[1];
            if (class_exists($controller) && method_exists($controller, $action)) {
                $reflectionMethod = new ReflectionMethod($controller, $action);
                $parameters = $reflectionMethod->getParameters();
                $dependencies = [];
                foreach ($parameters as $parameter) {
                    $paramClass = $parameter->getClass();
                    if ($paramClass) {
                        $dependencies[] = $this->container->make($paramClass->name);
                    } else {
                        $dependencies[] = array_shift($data);
                    }
                }
                $response = call_user_func_array([new $controller, $action], $dependencies);
            } else {
                throw new \Exception("Lớp $controller hoặc phương thức $action không tồn tại.");
            }
        } elseif (is_callable($callback)) {
            $response = call_user_func_array($callback, $data);
        }
        return $response;
    }
    public function terminate($request, $response)
    {
        foreach($this->middlewareAfter as $midd){
            (new $midd)->handle($request);
        }
    }
    private function handleListMiddleware($middlewares){
        if(is_array($middlewares) && !empty($middlewares)){
            foreach($middlewares as $midd){
                $this->handleMiddleware($midd);
            }
        }else if(is_string($middlewares)){
            $this->handleMiddleware($middlewares);
        }
    }
    private function handleMiddleware($midd){
        $keyRouteMiddle = array_keys($this->routeMiddleware);
        if(in_array($midd,$keyRouteMiddle)){
            $middlewares = $this->routeMiddleware[$midd];
            if(!empty($middlewares)){
                foreach($middlewares as $item){
                    $middleware = new $item;
                    $middleware->handle($this->request);
                }
            }
        }else if(isset($this->middlewareAliases[$midd])){
            $middleware = new $this->middlewareAliases[$midd];
            $middleware->handle($this->request);
        }
    }
}