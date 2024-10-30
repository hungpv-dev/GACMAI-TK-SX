<?php
namespace App\Http\Controllers\WEB\ThietKe;
use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use AsfyCode\Utils\Request;

class DesignController extends Controller{
    public function status(){
        $status = OrderStatus::where('type_data',2)->orderBy('sort','asc')->get();
        return view("thietke.status.index",compact('status'));
    }
}
