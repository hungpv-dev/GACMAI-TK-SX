<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\CustomerStatus;
use App\Models\Group;
use AsfyCode\Utils\Request;

class CustomerNotifyController extends Controller{
    public function index(){
        $groups = Group::all();
        $status = CustomerStatus::all();
        return view('customers.notificaion.index',compact('groups','status'));
    }
}
