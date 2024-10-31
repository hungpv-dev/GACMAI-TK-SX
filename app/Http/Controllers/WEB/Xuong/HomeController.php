<?php
namespace App\Http\Controllers\WEB\Xuong;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use AsfyCode\Utils\Request;

class HomeController extends Controller{
    public function index(){
        $orders = Order::where('status_id',10)->get();
        $ordersBaohanh = Order::where('status_id',13)->count();
        return view('xuong.home',compact('orders','ordersBaohanh'));
    }
    

    public function status(){
        $status = OrderStatus::where('type_data',3)->orderBy('sort','asc')->get();
        return view("xuong.status.index",compact('status'));
    }
}
