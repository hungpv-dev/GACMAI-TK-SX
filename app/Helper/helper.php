<?php

use App\Http\Controllers\TypeDBController;

if (!function_exists('view')) {
    function view($view, $data = [])
    {
        $blade = new Blade();
        $html = $blade->render($view, $data);
        return response()->html($html);
    }
}
if (!function_exists('app')) {
    function app()
    {
        global $container;
        return $container;
    }
}
if (!function_exists('user')) {
    function user()
    {
        return session()->get('authentication');
    }
}
if (!function_exists('csrf_token')) {
    function csrf_token()
    {

        if (session()->has("csrf_token")) {
            $csrfToken = session()->get("csrf_token");
        } else {
            $csrfToken = bin2hex(random_bytes(32));
        }

        session()->set("csrf_token", $csrfToken);

        return $csrfToken;
    }
}
if (!function_exists('route')) {
    function route($route, $params = [])
    {
        $routes = AsfyCode\Utils\Route::$names;
        if (!isset($routes[$route]['path'])) {
            echo 'Đường dẫn không tồn tại!';
            return;
        }
        $path = $routes[$route]['path'];
        $generatedRoute = replacePlaceholders($path, $params);
        return $generatedRoute;
    }
    function replacePlaceholders($route, $parameters = [])
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }
        $index = 0;
        return preg_replace_callback('/\{[a-zA-Z]+\}/', function () use (&$index, $parameters) {
            // Lấy giá trị từ mảng $parameters theo số thứ tự
            return isset($parameters[$index]) ? $parameters[$index++] : '';
        }, $route);
    }
}
if (!function_exists('http_status_code')) {
    function http_message_code($code)
    {
        $statusCodes = [
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Không tìm thấy trang!',
            405 => 'Phương thức không hỗ trợ',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            419 => 'Page expired | CSRF token invalide',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        ];
        return $statusCodes[$code] ?? 'Trạng thái khong xác định';
    }
}
if (!function_exists('abort')) {
    function abort($status, $message = '')
    {
        try {
            // Đặt mã trạng thái HTTP
            http_response_code($status);
            $message = !empty($message) ? $message : http_message_code($status);
            // Check status trang để chuyển hướng
            $status = http_response_code();
            $request = new \AsfyCode\Utils\Request();
            if ($request->ajax()) {
                echo response()->json([
                    'message' => $message
                ], $status)->content;
            } else {
                echo view("errors.$status", compact('message'))->content;
            }
        } catch (RuntimeException $e) {
            logError($e);
            echo view("errors.default", compact('status', 'message'))->content;
        }
    }
}
if (!function_exists('session')) {
    function session($flush = false)
    {
        return new Session($flush);
    }
}
if (!function_exists('response')) {
    function response()
    {
        return new Response();
    }
}
if (!function_exists('dd')) {
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            echo '<pre>';
            \Symfony\Component\VarDumper\VarDumper::dump($var);
            echo '</pre>';
        }
        die();
    }
}
if (!function_exists('old')) {
    function old($key)
    {
        return $_SESSION['flush']['form']['value'][$key] ?? false;
    }
}
if (!function_exists('back')) {
    function back($url = 'false')
    {
        if (session()->has('previous_url')) {
            $key = count(session()->get('previous_url')) - 1;
        } else {
            $key = count(session()->get('previous_url'));
        }
        foreach (session()->get('previous_url') as $i => $value) {
            if ($i == $key) {
                $previous_url = $value['path'];
            }
        }
        session()->set('previous_url', array_values(session()->get('previous_url')));
        if ($url) {
            return $previous_url;
        }
        return redirect($previous_url);
    }
}
if (!function_exists('redirect')) {
    function redirect($url, $statusCode = 302)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        http_response_code($statusCode);
        header("Location: " . $url);
        exit();
    }
}
if (!function_exists('compact')) {
    function compact(...$variables)
    {
        try {
            $result = [];
            foreach ($variables as $varName) {
                if (isset($GLOBALS[$varName])) {
                    $result[$varName] = $GLOBALS[$varName];
                }
            }
            return $result;
        } catch (Exception $e) {
            logError($e);
            abort(500);
        }
    }
}
if (!function_exists('asset')) {
    function asset($path)
    {
        return $_ENV['BASE_URL'] . '/public/assets/' . $path;
    }
}
if (!function_exists('public_path')) {
    function public_path($path)
    {
        return $_ENV['BASE_URL'] . '/public/' . $path;
    }
}
if (!function_exists('root')) {
    function root($path)
    {
        global $BASE_PATH;
        $root = $BASE_PATH;
        return $root . '/' . $path;
    }
}
if (!function_exists('js')) {
    function js($path)
    {
        return $_ENV['BASE_URL'] . '/resources/js/' . $path . '.js';
    }
}
if (!function_exists('css')) {
    function css($path)
    {
        return $_ENV['BASE_URL'] . '/resources/css/' . $path;
    }
}
if (!function_exists('uploads')) {
    function uploads($path)
    {
        return $_ENV['BASE_URL'] . '/public/uploads/' . $path;
    }
}
if (!function_exists('getRootUrl')) {
    function getRootUrl()
    {
        $http = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? "https://" : "http://";
        return $http . $_SERVER["HTTP_HOST"];
    }
}
if (!function_exists('logError')) {
    function logError(Exception $e)
    {
        $traceInfo = getBacktraceInfo();

        $errorMessage = sprintf(
            "Error: %s\nIn file: %s\nOn line: %d",
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );

        if ($traceInfo['class'] || $traceInfo['function']) {
            $errorMessage .= "\nContext: ";
            if ($traceInfo['class']) {
                $errorMessage .= "class " . $traceInfo['class'] . "::";
            }
            if ($traceInfo['function']) {
                $errorMessage .= "function " . $traceInfo['function'];
            }
        }

        if ($traceInfo['file'] && $traceInfo['line']) {
            $errorMessage .= sprintf("\nCaught in file: %s\nOn line: %d", $traceInfo['file'], $traceInfo['line']);
        }

        if (!empty($traceInfo['context'])) {
            $errorMessage .= "\nContext Variables: " . implode(', ', $traceInfo['context']);
        }
        $errorMessage = $errorMessage . "\n " . str_repeat('★', 35) . " ERROR LOG " . str_repeat('★', 35) . "\n";
        error_log($errorMessage);
    }

    function getBacktraceInfo()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $traceInfo = isset($backtrace[1]) ? $backtrace[1] : [];

        $class = $traceInfo['class'] ?? null;
        $function = $traceInfo['function'] ?? null;
        $file = $traceInfo['file'] ?? null;
        $line = $traceInfo['line'] ?? null;

        $context = [];
        foreach ($backtrace as $trace) {
            if (!empty($trace['args'])) {
                foreach ($trace['args'] as $arg) {
                    $context[] = var_export($arg, true);
                }
            }
        }

        return [
            'class' => $class,
            'function' => $function,
            'file' => $file,
            'line' => $line,
            'context' => $context,
        ];
    }
}
if (!function_exists('getProtocol')) {
    function getProtocol()
    {
        if (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ||
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        ) {
            return 'https://';
        }
        return 'http://';
    }
}
if (!function_exists('dateFormat')) {
    function dateFormat($time = null, $format = 'd-m-Y')
    {
        if ($time == null || $time == '' || $time == '0000-00-00' || $time == '0000-00-00 00:00:00') {
            return $time;
        }
        return date($format, strtotime($time));
    }
}
if (!function_exists('formatDate')) {
    function formatDate($time = null, $format = 'd-m-Y')
    {
        return dateFormat($time, 'Y-m-d H:i:s');
    }
}
if (!function_exists('getTimeAgo')) {
    function getTimeAgo($time, $type = '')
    {
        if ($time == '' || $time == '0000-00-00' || $time == '0000-00-00 00:00:00') {
            return 'Chưa hoạt động';
        }
        if (preg_match('/[-:]/', $time)) {
            $time = strtotime($time);
        }
        $time_diff = time() - $time;
        $rs = '';
        $num = '';
        $unit = '';
        if ($time_diff <= 5) {
            return 'Vừa xong';
        } elseif ($time_diff < 60) {
            $num = $time_diff;
            $unit = 's';
        } elseif ($time_diff < 3600) {
            $num = floor($time_diff / 60);
            $unit = 'm';
        } elseif ($time_diff < 3600 * 24) {
            $num = floor($time_diff / 3600);
            $unit = 'h';
        } elseif ($time_diff < 30 * 3600 * 24) {
            $num = floor($time_diff / (3600 * 24));
            $unit = 'd';
        } elseif ($time_diff < 365 * 3600 * 24) {
            $num = floor($time_diff / (30 * 3600 * 24));
            $unit = 'mo';
        } else {
            $num = floor($time_diff / (365 * 3600 * 24));
            $unit = 'y';
        }
        if ($type == '') {
            $rs = $num . $unit;
        } else {
            if ($unit == 's') {
                $unit = 'giây';
            } elseif ($unit == 'm') {
                $unit = 'phút';
            } elseif ($unit == 'h') {
                $unit = 'giờ';
            } elseif ($unit == 'd') {
                $unit = 'ngày';
            } elseif ($unit == 'mo') {
                $unit = 'tháng';
            } elseif ($unit == 'y') {
                $unit = 'năm';
            }
            $rs = $num . ' ' . $unit . ' trước';
        }
        return $rs;
    }
}
if (!function_exists('getDayAgo')) {
    function getDayAgo($time)
    {
        if ($time == '0000-00-00 00:00:00' || $time == '') {
            return 0;
        }
        if (strpos($time, ':') !== false) {
            $time = strtotime($time);
        }
        $time_range = time() - $time;
        return (int)floor($time_range / (3600 * 24));
    }
}
if (!function_exists('getDateStart2End')) {
    function getDateStart2End($dateRequest, $format = 'Y-m-d'): array
    {
        $start_date = '';
        $end_date = '';
        try {
            if (preg_match('# đến #ui', $dateRequest)) {
                $date = explode(' đến ', $dateRequest);
                $start_date = (new DateTime($date[0]))->format($format);
                $end_date = (new DateTime($date[1]))->format($format);
            } elseif (preg_match('# to #ui', $dateRequest)) {
                $date = explode(' to ', $dateRequest);
                $start_date = (new DateTime($date[0]))->format($format);
                $end_date = (new DateTime($date[1]))->format($format);
            }else if(preg_match('#:\s*(.*)#', $dateRequest, $matches)){
                $dateRange = $matches[1];
                if (preg_match('# - #', $dateRange)) {
                    $dates = explode(' - ', $dateRange);
                    $start_date = formatDateString($dates[0],$format);
                    $end_date = formatDateString($dates[1],$format);
                } else {
                    $start_date = formatDateString($dateRange,$format);
                    $end_date = $start_date; // Ngày kết thúc giống ngày bắt đầu
                }
            }else {
                if (preg_match('# - #', $dateRequest)) {
                    $dates = explode(' - ', $dateRequest);
                    $start_date = formatDateString($dates[0],$format);
                    $end_date = formatDateString($dates[1],$format);
                } else {
                    try{
                        $start_date = formatDateString($dateRequest,$format);
                        $end_date = $start_date;
                    }catch(Exception $e){
                        $date = DateTime::createFromFormat('d-m-Y', $dateRequest);
                        $formattedDate = $date->format('Y-m-d');
                        $start_date = $formattedDate;
                        $end_date = $formattedDate;
                    }
                }
            }
            return [
                'start' => $start_date,
                'end' => $end_date . ' 23:59:59'
            ];
        } catch (Exception $e) {
            return [];
        }
    }
}
if (!function_exists('formatDateString')) {
    function formatDateString($dateString, $format)
    {
        $date = DateTime::createFromFormat('d \T\h\á\n\g m, Y', $dateString);
        if ($date === false) {
            throw new Exception("Không thể tạo đối tượng DateTime từ chuỗi: $dateString");
        }
        $output = $date->format($format);
        return $output;
    }
}
if (!function_exists('slug')) {
    function slug($string)
    {
        // Chuyển đổi ký tự tiếng Việt có dấu thành không dấu
        $vietnameseMap = [
            'à' => 'a',
            'á' => 'a',
            'ả' => 'a',
            'ã' => 'a',
            'ạ' => 'a',
            'ă' => 'a',
            'ằ' => 'a',
            'ắ' => 'a',
            'ẳ' => 'a',
            'ẵ' => 'a',
            'ặ' => 'a',
            'â' => 'a',
            'ầ' => 'a',
            'ấ' => 'a',
            'ẩ' => 'a',
            'ẫ' => 'a',
            'ậ' => 'a',
            'è' => 'e',
            'é' => 'e',
            'ẻ' => 'e',
            'ẽ' => 'e',
            'ẹ' => 'e',
            'ê' => 'e',
            'ề' => 'e',
            'ế' => 'e',
            'ể' => 'e',
            'ễ' => 'e',
            'ệ' => 'e',
            'ì' => 'i',
            'í' => 'i',
            'ỉ' => 'i',
            'ĩ' => 'i',
            'ị' => 'i',
            'ò' => 'o',
            'ó' => 'o',
            'ỏ' => 'o',
            'õ' => 'o',
            'ọ' => 'o',
            'ô' => 'o',
            'ồ' => 'o',
            'ố' => 'o',
            'ổ' => 'o',
            'ỗ' => 'o',
            'ộ' => 'o',
            'ơ' => 'o',
            'ờ' => 'o',
            'ớ' => 'o',
            'ở' => 'o',
            'ỡ' => 'o',
            'ợ' => 'o',
            'ù' => 'u',
            'ú' => 'u',
            'ủ' => 'u',
            'ũ' => 'u',
            'ụ' => 'u',
            'ư' => 'u',
            'ừ' => 'u',
            'ứ' => 'u',
            'ử' => 'u',
            'ữ' => 'u',
            'ự' => 'u',
            'ỳ' => 'y',
            'ý' => 'y',
            'ỷ' => 'y',
            'ỹ' => 'y',
            'ỵ' => 'y',
            'đ' => 'd',
            'À' => 'A',
            'Á' => 'A',
            'Ả' => 'A',
            'Ã' => 'A',
            'Ạ' => 'A',
            'Ă' => 'A',
            'Ằ' => 'A',
            'Ắ' => 'A',
            'Ẳ' => 'A',
            'Ẵ' => 'A',
            'Ặ' => 'A',
            'Â' => 'A',
            'Ầ' => 'A',
            'Ấ' => 'A',
            'Ẩ' => 'A',
            'Ẫ' => 'A',
            'Ậ' => 'A',
            'È' => 'E',
            'É' => 'E',
            'Ẻ' => 'E',
            'Ẽ' => 'E',
            'Ẹ' => 'E',
            'Ê' => 'E',
            'Ề' => 'E',
            'Ế' => 'E',
            'Ể' => 'E',
            'Ễ' => 'E',
            'Ệ' => 'E',
            'Ì' => 'I',
            'Í' => 'I',
            'Ỉ' => 'I',
            'Ĩ' => 'I',
            'Ị' => 'I',
            'Ò' => 'O',
            'Ó' => 'O',
            'Ỏ' => 'O',
            'Õ' => 'O',
            'Ọ' => 'O',
            'Ô' => 'O',
            'Ồ' => 'O',
            'Ố' => 'O',
            'Ổ' => 'O',
            'Ỗ' => 'O',
            'Ộ' => 'O',
            'Ơ' => 'O',
            'Ờ' => 'O',
            'Ớ' => 'O',
            'Ở' => 'O',
            'Ỡ' => 'O',
            'Ợ' => 'O',
            'Ù' => 'U',
            'Ú' => 'U',
            'Ủ' => 'U',
            'Ũ' => 'U',
            'Ụ' => 'U',
            'Ư' => 'U',
            'Ừ' => 'U',
            'Ứ' => 'U',
            'Ử' => 'U',
            'Ữ' => 'U',
            'Ự' => 'U',
            'Ỳ' => 'Y',
            'Ý' => 'Y',
            'Ỷ' => 'Y',
            'Ỹ' => 'Y',
            'Ỵ' => 'Y',
            'Đ' => 'D',
        ];

        $string = htmlspecialchars($string);
        // Thay thế ký tự tiếng Việt
        $string = strtr($string, $vietnameseMap);

        // Loại bỏ ký tự không hợp lệ và chuyển về chữ thường
        $string = strtolower(preg_replace('/[^a-zA-Z0-9\s-]/', '', $string));

        // Thay thế khoảng trắng và dấu gạch ngang liền kề bằng một dấu gạch ngang duy nhất
        $string = preg_replace('/[\s-]+/', '-', $string);

        // Loại bỏ dấu gạch ngang ở đầu và cuối chuỗi
        $string = trim($string, '-');

        return $string;
    }
}
if (!function_exists('now')) {
    function now($format = 'Y-m-d H:i:s', $modify = '')
    {
        $dateTime = new DateTime('now', new DateTimeZone('Asia/Ho_Chi_Minh'));
        if ($modify) {
            $dateTime->modify($modify);
        }
        return $dateTime->format($format);
    }
}

if (!function_exists('getDateQuery')) {
    function getDateQuery($date)
    {
        return array_values(getDateStart2End($date));
    }
}

if (!function_exists('status')) {
    function status($name)
    {
        $typeDB = new TypeDBController();
        return $typeDB->$name;
    }
}
if (!function_exists('currentMonth')) {
    function currentMonth()
    {
        return 'Tháng này: 01 Tháng '.now('m').', '.now('Y').' - '.now('d').' Tháng '.now('m').', '.now('Y');
    }
}

if (!function_exists('getPreviousDateQuery')) {
    function getPreviousDateQuery($dateSearch)
    {
        $startDate = strtotime($dateSearch[0]);
        $endDate = strtotime($dateSearch[1]);

        // Tính toán khoảng thời gian
        $duration = ($endDate - $startDate) / (60 * 60 * 24); // Số ngày

        // Lấy khoảng thời gian trước cùng kỳ
        $previousStartDate = $startDate - ($duration * 60 * 60 * 24);
        $previousEndDate = $endDate - ($duration * 60 * 60 * 24) - 1;

        return [date('Y-m-d', $previousStartDate), date('Y-m-d 23:59:59', $previousEndDate)];
    }
}

if (!function_exists('user_logs')) {
    function user_logs($content)
    {
        App\Models\UserActiveLog::create([
            'user_id' => user()->id,
            'content' => $content,
            'created_at' => now(),
        ]);
    }
}


if (!function_exists('formatText')) {
    function formatText($type, $text) {
        $classes = [
            'info' => 'fw-bold text-info',
            'success' => 'fw-bold text-success',
            'primary' => 'fw-bold text-primary',
            'warning' => 'fw-bold text-warning',
            'danger' => 'fw-bold',
            '' => 'fw-bold',
        ];

        $style = $type === 'danger' ? 'style="color:red"' : '';
        return "<span class='{$classes[$type]}' {$style}>{$text}</span>";
    }
}
if (!function_exists('textPrice')) {
    function textPrice($price,$type = '') {
        return formatText($type,number_format($price).' ₫');
    }
}