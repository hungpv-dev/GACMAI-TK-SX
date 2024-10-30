<?php 
namespace AsfyCode\Container;

class Container
{
    protected $bindings = [];
    protected $singletons = [];
    public function bind($abstract, $concrete)
    {
        $this->bindings[$abstract] = $concrete;
    }
    public function singleton($abstract, $concrete)
    {
        $this->singletons[$abstract] = function () use ($concrete) {
            // Đảm bảo biến chỉ được khởi tạo 1 lần duy nhất và lưu giá trị của nó
            static $instance;

            // Nếu chưa có $instance được tạo ra thì tạo 1 $instance mới
            if ($instance === null) {
                $instance = $concrete();
            }

            return $instance;
        };
    }
    public function make($abstract)
    {
        // Kiểm tra xem có trong singleton không
        if (isset($this->singletons[$abstract])) {
            return $this->singletons[$abstract]();
        }

        // Trả về instance mới từ binding
        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]();
        }

        return null;
    }
}