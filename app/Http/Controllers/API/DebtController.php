<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Repositories\UserRepository;
use AsfyCode\Utils\Request;
use Illuminate\Database\Capsule\Manager;

class DebtController extends Controller{
    public function index(Request $request){
        $customers = Customer::from('customers as c')
        ->join('orders as o','o.customer_id','=','c.id')
        ->join('users as u','u.id','=','c.user_id')
        ->select(
            'c.name as customer_name',
            'c.id as customer_id',
            'u.name as user_name',
            'o.id as order_id',
            Manager::raw('SUM(o.du_kien) - SUM(o.thuc_thu) as total_amount')
        )
        ->groupBy('c.id');

        $customers->havingRaw('SUM(o.du_kien) - SUM(o.thuc_thu) != ?', [0]);

        $customers->orderBy(Manager::raw('SUM(o.du_kien) - SUM(o.thuc_thu)'),'desc');

        if($request->has('name')){
            $customers->where('c.name','like','%'.$request->name.'%');
        }
        if($request->has('user_id')){
            $customers->where('u.id','=',$request->user_id);
        }

        $total_amount = (clone $customers)->get()->sum('total_amount');
        $response = $request->paginate($customers)
        ->setAttribute(compact('total_amount'));
        return $this->sendResponse($response);
    }
}
