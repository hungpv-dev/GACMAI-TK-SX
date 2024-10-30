<?php
namespace App\Http\Controllers\WEB\ThietKe;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Factory;
use App\Models\Group;
use App\Models\OrderStatus;
use App\Models\Province;
use App\Models\User;
use AsfyCode\Utils\Request;

class OrderController extends Controller{
    public function index(){
        $provinces = Province::all();
        $users = User::all();
        $account = BankAccount::all();
        $groups = Group::all();
        $customersQuery = Customer::query();
        $customersSearch = (clone $customersQuery)->get();
        $customers = (clone $customersQuery)->where('status_id',status('order_customer'))->get();
        $statusOrder = OrderStatus::where('type_data',2)->get();
        $factories = Factory::all();
        $categories = Category::all();
        $usertk = $users->where('role_id',6);
        return view("thietke.orders.index",compact('factories','categories','usertk','provinces','groups','statusOrder','customersSearch','users','customers','account'));
    }
    
}
