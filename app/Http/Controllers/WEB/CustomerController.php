<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Repositories\UserRepository;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CustomerStatus;
use App\Models\District;
use App\Models\Factory;
use App\Models\User;
use App\Models\Group;
use App\Models\Province;
use App\Models\SaleChannel;
use App\Models\Ward;
use AsfyCode\Utils\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerController extends Controller{
    public function index(){
        $users = User::all();
        $status = CustomerStatus::orderBy('sort','asc')->get();
        $categories = Category::all();  
        $sale_channel = SaleChannel::all();
        $groups = Group::all();
        return view("customers.index",compact("users",'sale_channel','groups',"categories",'status','provinces'));
    }
    
    public function show($id){
        try{
            $customer = Customer::findOrFail($id);
            $account = BankAccount::all();
            $factories = Factory::all();
            $usertk = User::where('role_id',6)->get();
            return view('customers.show',compact('customer','factories','usertk','account'));
        }catch(ModelNotFoundException $e){
            abort(404,'Không tìm thấy khách hàng!');
        }
    }

    public function priceThuDuoc(){
        $customers = Customer::all();
        $bank_accounts = BankAccount::all();
        return view('customers.total_price_thu_duoc',compact('customers','bank_accounts'));
    }
    public function expired(){
        $users = User::get();
        $status = CustomerStatus::orderBy('sort','asc')->get();
        $categories = Category::all();  
        $sale_channel = SaleChannel::all();
        $groups = Group::all();
        return view('customers.expired',compact('status','categories','users','sale_channel','groups'));
    }
}
