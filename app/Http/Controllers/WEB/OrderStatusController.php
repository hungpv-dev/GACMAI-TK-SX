<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use AsfyCode\Utils\Request;

class OrderStatusController extends Controller{
    public function index(){
        $status = OrderStatus::where('type_data',1)->orderBy('sort','asc')->get();
        return view("orders.status.index",compact('status'));
    }
}
