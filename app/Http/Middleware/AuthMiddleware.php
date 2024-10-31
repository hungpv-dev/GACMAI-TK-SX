<?php

namespace App\Http\Middleware;

use App\Http\Controllers\WEB\LoginController;
use App\Models\User;
use App\Traits\GoogleClient;
use AsfyCode\Middleware\Middleware;
use AsfyCode\Utils\Request as Request;
use Exception;

class AuthMiddleware extends Middleware
{
    use GoogleClient;
    private $login;
    private $roles = [1,6];
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
                    if ($user->status != 1) {
                        session(true)->set('error', 'Tài khoản không có quyền truy cập! 1');
                        $this->login->logout();
                    } else {
                        if ($this->checkRoles($user->id,$this->roles)) {
                            $this->login->setSession($user);
                            $user->last_active = now();
                            $user->save();
                        } else {
                            session(true)->set('error', 'Tài khoản không có quyền truy cập! 2');
                            $this->login->logout();
                        }
                    }
                } else {
                    session(true)->set('error', 'Tài khoản không tồn tại trong hệ thống!');
                    $this->login->logout();
                }
            } elseif ($refresh_token || $access_token) {
                $this->login->handleAccessToken();
            }else{
                session(true)->set('error', 'Vui lòng đăng nhập!');
                $this->login->logout();
            }
        } catch (Exception $e) {
            $this->login->logout();
            session(true)->set('error', 'Phiên đăng nhập đã hết hạn!');
        }
    }
}