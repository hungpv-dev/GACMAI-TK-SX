<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Group;
use App\Models\TypeLog;
use App\Models\User;
use AsfyCode\Utils\Request;

class LogController extends Controller{
    public function index(){
        $users = User::all();
        $customers = Customer::all();
        return view("logs.index",compact('users','customers'));
    }

    public function tienthuduoc(){
        $users = User::all();
        $groups = Group::all();
        return view('logs.tienthuduoc',compact('users','groups'));
    }
}
