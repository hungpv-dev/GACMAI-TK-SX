<?php

namespace AsfyCode\Utils;

use Paginator;
use Response;
use Session;

class Request
{
    public function input($key = NULL, $default = NULL)
    {
        $all = $this->all();
        return isset($all[$key]) ? $all[$key] : $default;
    }

    public function __get($name) {
        return $this->input($name);
    }

    public function user()
    {
        return $_SESSION['authentication'] ?? NULL;
    }

    public function all()
    {
        $data = [];
        $dataGet = $_GET;
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (strpos($contentType, 'application/json') !== false) {
            $data = json_decode(file_get_contents('php://input'), true);
            if (!is_array($data)) {
                $data = [];
            }
        } elseif (strpos($contentType, 'multipart/form-data') !== false) {
            $data = array_merge($_POST, $_FILES);
        } else {
            $data = $_POST;
        }

        $all = array_merge($dataGet, $data);
        foreach($all as $key => $item){
            if(is_string($item)){
                $all[$key] = htmlspecialchars(trim($item));
            }else{
                $all[$key] = $item;
            }
        }
        return $all;
    }

    public function has($key)
    {
        $allData = $this->all();
        return isset($allData[$key]);
    }
    public function query($key = null, $default = null)
    {
        if ($key === null) {
            return $_GET;
        }

        return $_GET[$key] ?? $default;
    }
    public function merge($key = null, $value = null)
    {
        $_GET[$key] = $value;
    }
    public function file($key = null)
    {
        if ($key === null) {
            return $_FILES;
        }

        return $_FILES[$key] ?? null;
    }
    public function method()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if($method == 'POST'){
            if($this->has('_method')){
                $method = strtoupper($this->input('_method'));
            }
        }
        return $method;
    }
    public function fullUrl($params = true)
    {
        $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $scheme = $isHttps ? 'https' : 'http';
        $url = $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $params ? $url : strtok($url, '?');
    }
    public function uri()
    {
        return $_SERVER['REQUEST_URI'];
    }
    public function path()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }
    function session($flush = false)
    {
        return new Session($flush);
    }
    public function ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }
    public static function getRealIPAddress()
    {
        return $_SERVER['HTTP_CLIENT_IP'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
    }

    public function back()
    {
        return back();
    }
    public function validate($rules, $message = [], $attribute = [])
    {
        return new Validator($this->all(), $rules, $message, $attribute);
    }

    public function response()
    {
        return new Response();
    }
    public function host()
    {
        return $_SERVER['HTTP_HOST'] ?? '';
    }

    public function paginate($data,$limit = 50){
        return (new Paginator($data,$limit))->build();
    }

    public static function capture()
    {
        return new self();
    }
}
