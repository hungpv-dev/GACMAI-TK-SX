<?php
namespace App\Http\Controllers\WEB\Xuong;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Factory;
use App\Models\Group;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Province;
use App\Models\TransactionType;
use App\Models\User;
use AsfyCode\Utils\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller{
    public function index(){
        $provinces = Province::all();
        $users = User::all();
        $customersQuery = Customer::query();
        $customersSearch = (clone $customersQuery)->get();
        $statusadd = OrderStatus::whereIn('id',[10])->orderBy('sort','desc')->get(); 
        $customers = Customer::all();
        $statusOrder = OrderStatus::where('type_data',3)->get();
        $factories = Factory::all();
        $categories = Category::all();
        $type = TransactionType::where('type',2)->get();
        return view("xuong.orders.index",compact('type','factories','statusadd','categories','provinces','statusOrder','customersSearch','users','customers'));
    }

    
    public function baohanh(){
        $provinces = Province::all();
        $users = User::all();
        $status = OrderStatus::whereIn('id',[12,13])->orderBy('sort','desc')->get(); 
        return view("xuong.orders.baohanh",compact('provinces','users','status','customers'));
    }

    
    public function show($id){
        try{
            $order = Order::findOrFail($id);
            $account = BankAccount::all();
            return view('xuong.orders.show',compact('order','account'));
        }catch(ModelNotFoundException $e){
            abort(404,'Không tìm thấy khách hàng!');
        }
    }
}
