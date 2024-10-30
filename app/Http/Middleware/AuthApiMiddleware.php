<?php

namespace App\Http\Middleware;

use App\Http\Controllers\WEB\LoginController;
use App\Models\User;
use App\Traits\GoogleClient;
use AsfyCode\Middleware\Middleware;
use AsfyCode\Utils\Request as Request;
use Exception;

class AuthApiMiddleware extends Middleware
{
    use GoogleClient;
    private $login;
    public function __construct()
    {
        $this->login = new LoginController();
    }

    public function handle(Request $request)
    {
        try {
            $refresh_token = $_COOKIE['secure_refresh_token'] ?? null;
            $access_token = $_COOKIE['secure_access_token'] ?? null;
            if (isset($_SESSION['authentication']) && isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $user = User::find($user_id);
                if ($user) {
                    if ($user->status == 1) {
                        $this->login->setSession($user);
                        $user->last_active = now();
                        $user->save();
                        return;
                    } 
                } 
            } elseif ($refresh_token || $access_token) {
                $this->login->handleAccessToken();
                return;
            } 
        } catch (Exception $e) {
            $this->login->logout();
        }
        if ($request->ajax()) {
            return response()->json([
                'messages' => 'Vui lòng đăng nhập',
            ], 401);
        } else {
            return redirect('/login');
        }
    }
}