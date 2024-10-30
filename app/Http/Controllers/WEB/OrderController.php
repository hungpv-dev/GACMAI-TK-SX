<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Customer;
use App\Models\Factory;
use App\Models\Group;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Province;
use App\Models\User;
use App\Repositories\UserRepository;
use AsfyCode\Utils\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller{
    public function index(UserRepository $userRepository){
        $provinces = Province::all();
        $users = User::all();
        $account = BankAccount::all();
        $groups = Group::all();
        $customersQuery = Customer::query();
        $customersSearch = (clone $customersQuery)->get();
        $customers = (clone $customersQuery)->where('status_id',status('order_customer'))->get();
        $statusOrder = OrderStatus::where('type_data',1)->get();
        $factories = Factory::all();
        $usertk = $users->where('role_id',6);
        return view("orders.index",compact('factories','usertk','provinces','groups','statusOrder','customersSearch','users','customers','account'));
    }

    public function show($id){
        try{
            $order = Order::findOrFail($id);
            $account = BankAccount::all();
            return view('orders.show',compact('order','account'));
        }catch(ModelNotFoundException $e){
            abort(404,'Không tìm thấy khách hàng!');
        }
    }
}
