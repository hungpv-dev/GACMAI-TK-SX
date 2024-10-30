<?php

namespace AsfyCode\Utils;

class Route
{
    private static $typeRoute = ['api', 'web'];
    public static $routes = [];
    protected static $middleware = [];
    protected static $currentMiddleware = [];
    protected static $prefix = '';
    protected static $currentPrefix = [];
    private static $callback = [];
    public static $names = [];
    private static $currentRoute = [];
    public static $groupNames = [];
    public static function middleware($middleware)
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        if (array_intersect($middlewares, self::$typeRoute)) {
            self::$middleware = $middlewares;
            self::$currentMiddleware = self::$middleware;
        } else {
            self::$middleware = array_merge(self::$middleware, $middlewares);
            self::$currentMiddleware[] = self::$middleware;
        }
        self::$callback[] = __FUNCTION__;
        return new static;
    }
    public static function resource($path, $callback)
    {
        $withNames = in_array('names', self::$callback);

        self::prefix($path)->group(function () use ($callback, $withNames) {
            $routes = [
                ['/{id}', 'show', 'get'],
                ['/{id}', 'update', 'put'],
                ['/{id}', 'destroy', 'delete'],
                ['/{id}/edit', 'edit', 'get'],
                ['/create', 'create', 'get'],
                ['/', 'index', 'get'],
                ['/', 'store', 'post'],
            ];

            foreach ($routes as [$uri, $action, $method]) {
                $route = self::$method($uri, [$callback, $action]);
                if ($withNames) {
                    $route->name($action);
                }
            }
        });

        return new static;
    }
    public static function apiResource($path, $callback)
    {
        $withNames = in_array('names', self::$callback);

        self::prefix($path)->group(function () use ($callback, $withNames) {
            $routes = [
                ['/{id}', 'show', 'get'],
                ['/{id}', 'update', 'put'],
                ['/{id}', 'destroy', 'delete'],
                ['/', 'index', 'get'],
                ['/', 'store', 'post'],
            ];

            foreach ($routes as [$uri, $action, $method]) {
                $route = self::$method($uri, [$callback, $action]);
                if ($withNames) {
                    $route->name($action);
                }
            }
        });

        return new static;
    }
    public static function prefix($prefix)
    {
        $prefix = ltrim($prefix, '/');
        if (in_array($prefix, self::$typeRoute)) {
            self::$prefix = $prefix;
            self::$currentPrefix = [];
        } else {
            self::$currentPrefix[] = self::$prefix;
        }
        self::$prefix = rtrim(end(self::$currentPrefix) . '/' . trim($prefix, '/'), '/');
        self::$callback[] = __FUNCTION__;
        return new static;
    }
    public static function group($callback)
    {
        $dataFunction = self::$callback;
        self::$callback = [];
        $callback();
        if (in_array('prefix', $dataFunction)) {
            self::$prefix = array_pop(self::$currentPrefix);
        }
        if (in_array('middleware', $dataFunction)) {
            array_pop(self::$currentMiddleware);
            self::$middleware = end(self::$currentMiddleware);
        }
        if (in_array('names', $dataFunction)) {
            array_pop(self::$groupNames);
        }
        return new static;
    }
    private static function addRoute($method, $path, $callback)
    {
        $router = [
            'method' => $method,
            'path' => rtrim(self::$prefix . '/' . trim($path, '/')),
            'callback' => $callback,
            'middleware' => self::$middleware,
        ];
        self::$routes[] = $router;
        self::$currentRoute = $router;
    }
    public static function get($path, $callback)
    {
        self::addRoute('GET', $path, $callback);
        return new static;
    }
    public static function view($path, $pathView)
    {
        self::addRoute('GET', $path, fn() => view($pathView));
        return new static;
    }
    public static function post($path, $callback)
    {
        self::addRoute('POST', $path, $callback);
        return new static;
    }
    public static function put($path, $callback)
    {
        self::addRoute('PUT', $path, $callback);
        return new static;
    }
    public static function delete($path, $callback)
    {
        self::addRoute('DELETE', $path, $callback);
        return new static;
    }
    public static function name($name)
    {
        if (!empty(self::$groupNames)) {
            $nameGr = (string) implode('.', self::$groupNames) . '.' . $name;
        } else {
            $nameGr = (string) $name;
        }
        $path = self::$currentRoute['path'] == '/' ? self::$currentRoute['path'] : rtrim(self::$currentRoute['path'],'/');
        self::$names[$nameGr] = [
            'path' => $path,
            'method' => self::$currentRoute['method'],
        ];
        return new static;
    }
    public static function names($name)
    {
        self::$groupNames[] = $name;
        self::$callback[] = __FUNCTION__;
        return new static;
    }
    private static function convertPattern($expression)
    {
        // Biến đổi các kí tự {} thành ([a-zA-Z0-9-]+) -> để vị trí đó có thể để bất từ kí tự nào
        // ([a-zA-Z0-9-]+) : Chấp nhận các kí tự a-z, A-Z, 0-9, -
        return preg_replace('/\{[a-zA-Z]+\}/', '([a-zA-Z0-9-]+)', $expression);
    }
    public static function dispatch($request)
    {
        $path = $request->path();
        $method = $request->method();

        $method_match_found = true;
        $route_match_found = true;
        $router = [];
        foreach (self::$routes as $route) {
            // Đảm bảo đường dẫn bắt đầu bằng '/'
            if (substr($route['path'], 0, 1) !== '/') {
                $route['path'] = '/' . $route['path'];
            }
            $route['path'] = $route['path'] !== '/' ? rtrim($route['path'], '/') : '/';
            // Chuyển đổi đường dẫn thành biểu thức chính quy
            $pattern = '/^' . str_replace('/', '\/', self::convertPattern($route['path'])) . '$/';

            // So khớp đường dẫn với biểu thức chính quy
            if (preg_match($pattern, $path, $matches)) {
                $data = [];
                $route_match_found = false;
                if ((string)$route['method'] === $method) {
                    $method_match_found = false;
                    $data = array_slice($matches, 1);
                    // Ánh xạ các tham số động
                    if (preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $path, $keys)) {
                        $keys = $keys[1];
                        if (count($keys) === count($data)) {
                            $data = array_combine($keys, $data);
                        } else {
                            // Số lượng tham số không khớp, có thể xử lý lỗi hoặc bỏ qua
                            $data = [];
                        }
                    }
                    $route['data'] = $data;
                    $router = $route;
                }
            }
        }
        if ($route_match_found) {
            abort(404);
            die();
        }

        if ($method_match_found) {
            abort(405);
            die();
        }
        return $router;
    }
}
