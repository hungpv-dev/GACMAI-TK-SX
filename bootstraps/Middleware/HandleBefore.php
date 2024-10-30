<?php

namespace AsfyCode\Middleware;

use AsfyCode\Traits\MiddleBefore;
use AsfyCode\Utils\Request as Request;

class HandleBefore extends Middleware
{
    use MiddleBefore;

    public function handle(Request $request)
    {

        // Check csrf token
        $this->csrfToken($request);

        // Lưu lại trang trước để sử dụng hàm back();
        $this->previousUrl($request);
    }
}
