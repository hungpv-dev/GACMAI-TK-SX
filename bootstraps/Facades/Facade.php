<?php 

namespace AsfyCode\Facades;

abstract class Facade
{
    // Trả về tên của dịch vụ trong container
    protected static function getFacadeAccessor()
    {
        throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
    }

    // Gọi phương thức của dịch vụ thông qua container
    public static function __callStatic($method, $args)
    {
        $instance = app()->make(static::getFacadeAccessor());
        return call_user_func_array([$instance, $method], $args);
    }
}
