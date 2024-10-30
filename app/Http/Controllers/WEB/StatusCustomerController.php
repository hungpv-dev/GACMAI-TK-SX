<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\CustomerStatus;
use AsfyCode\Utils\Request;

class StatusCustomerController extends Controller{
    public function index(){
        $status = CustomerStatus::orderBy("sort","asc")->get();
        return view("customers.status.index",compact("status"));
    }
}
