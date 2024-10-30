<?php

if (!class_exists('Session')) {
    class Session
    {
        public $flush = false;

        public function __construct($flush = false)
        {
            $this->flush = $flush;
        }

        public function set($key = null, $value = null)
        {
            if ($key === null) {
                return $this->flush ? $_SESSION['flush'] ?? [] : $_SESSION;
            }

            if ($value === null) {
                if ($this->flush) {
                    return $_SESSION['flush'][$key] ?? null;
                } else {
                    return $_SESSION[$key] ?? null;
                }
            }

            if ($this->flush) {
                $_SESSION['flush'][$key] = $value;
            } else {
                $_SESSION[$key] = $value;
            }

            return new static();
        }

        public function has($key)
        {
            if ($this->flush) {
                return isset($_SESSION['flush'][$key]);
            } else {
                return isset($_SESSION[$key]);
            }
        }
        public function get($key)
        {
            if ($this->flush) {
                return $_SESSION['flush'][$key] ?? NULL;
            } else {
                return $_SESSION[$key] ?? NULL;
            }
        }

        public function remove($key)
        {
            if ($this->flush) {
                unset($_SESSION['flush'][$key]);
                return;
            }
            unset($_SESSION[$key]);
        }
        public static function clearFlush()
        {
            unset($_SESSION['flush']);
            return new static();
        }

        public static function __callStatic($name, $arguments)
        {
            if (method_exists(static::class, $name)) {
                return forward_static_call_array([static::class, $name], $arguments);
            }

            throw new \BadMethodCallException("Method $name does not exist.");
        }
    }
}

if (!class_exists('Paginator')) {
    class Paginator
    {

        public $data;
        public $limit;
        public function __construct($data, $limit = 50)
        {
            $this->data = $data;
            $this->limit = $limit;
        }

        public function build()
        {
            $request = new \AsfyCode\Utils\Request();
            $page = $request->input('page', 1);
            $offset = ($page - 1) * $this->limit;
            $count = (clone $this->data)->count();
            $to = $offset + $this->limit;
            $to = min($to, $count);
            $from = $offset + 1;
            if ($request->has('show_all')) {
                $data = $this->data;
                $to = $count;
                $from = 1;
            } else {
                $data = $this->data->limit($this->limit)->offset($offset);
            }

            $query = $request->query();
            $query['page'] = $page;
            $response = [
                'from' => $from,
                'to' => $to,
                'total' => $count,
                'totalPages' => ceil($count / $this->limit),
                'currentPage' => $page,
                'url' => $request->fullUrl(false),
                'currentUrl' => $request->fullUrl(),
                'params' => '?' . http_build_query($query),
                'limit' => $this->limit,
                'all' => $request->has('show_all'),
                'data' => $data->get()
            ];

            foreach ($response as $key => $item) {
                $this->$key = $item;
            }

            return $this;
        }

        public function setAttribute($key, $value = null)
        {
            if (is_array($key)) {
                foreach ($key as $k => $value) {
                    $this->addProperty($k, $value);
                }
            } else {
                $this->addProperty($key, $value);
            }
            return $this;
        }

        public function addProperty($key, $value)
        {
            if (is_string($key)) {
                if (property_exists($this, $key)) {
                    throw new \InvalidArgumentException("Property '$key' exist");
                }
                $this->$key = $value;
            } else {
                throw new \InvalidArgumentException('Name must be string');
            }
        }
    }
}

if (!class_exists('Response')) {
    class Response
    {
        public $content;
        public $header;
        private $headers = [];
        public function json($data, $status = 200)
        {
            http_response_code($status);
            $this->header = "Content-Type: application/json";
            $this->content = json_encode($data);
            return $this;
        }

        public function html($content, $status = 200)
        {
            http_response_code($status);
            $this->header = "Content-Type: text/html";
            $this->content = $content;
            return $this;
        }

        public function plaintext($content, $status = 200)
        {
            http_response_code($status);
            $this->header = "Content-Type: text/plain";
            $this->content = $content;
            return $this;
        }

        public function header($key, $value = '')
        {
            if (is_array($key)) {
                foreach ($key as $k => $v) {
                    $this->headers[$k] = $v;
                }
            } else {
                $this->headers[$key] = $value;
            }
            return $this;
        }


        public function send()
        {
            if(!is_null($this->header)){
                header($this->header);
                foreach ($this->headers as $key => $value) {
                    header("{$key}: {$value}");
                }
                echo $this->content;
            }
        }
    }
}
