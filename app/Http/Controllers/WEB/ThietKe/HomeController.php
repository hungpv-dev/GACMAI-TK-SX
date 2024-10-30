<?php
namespace App\Http\Controllers\WEB\ThietKe;
use App\Http\Controllers\Controller;
use App\Models\Order;
use AsfyCode\Utils\Request;

class HomeController extends Controller{
    public function index(){
        $orders = Order::where('status_id',9)->get();
        return view('thietke.home',compact('orders'));
    }
}
