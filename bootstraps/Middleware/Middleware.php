<?php

namespace AsfyCode\Middleware;

use AsfyCode\Utils\Request;

abstract class Middleware
{
    abstract public function handle(Request $request);
}
