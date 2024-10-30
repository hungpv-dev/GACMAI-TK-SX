<?php

namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\UserRepository;
use Illuminate\Database\Capsule\Manager;

class HomeController extends Controller
{
    public function index()
    {
        $dt = app()->make('App\Repositories\UserRepository')->customerExpired();
        $status = app()->make('App\Repositories\UserRepository')->customerSchedule();
        if($status > 0){
            $dt[] = $status;
        }
        return view('home',compact('dt'));
    }
    public function thietke(){
        echo 'Đây là module thiêt kế';
    }
    public function xuong(){
        echo 'Đây là module xương';
    }
}
