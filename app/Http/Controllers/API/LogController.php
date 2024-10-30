<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLog;
use App\Repositories\UserRepository;
use AsfyCode\Utils\Request;

class LogController extends Controller{
    public function index(Request $request){
        $logs = CustomerLog::query();

        if($request->has("content")){
            $logs->where('content','like','%'.$request->content.'%');
        }

        if($request->has("user_id")){
            $logs->where('user_id',$request->user_id);
        }

        if($request->has("customer_id")){
            $logs->where('customer_id',$request->customer_id);
        }

        $logs->with([
            'user',
            'customer',
            'category',
            'from_status',
            'to_status',
        ]);
        
        $limit = $request->input('limit',50);
        $logs->orderBy('created_at','desc');
        return $this->sendResponse($request->paginate($logs,$limit));
    }

    public function store(Request $request){
        $validate = $request->validate(
            [
                'customer_id' => 'required',
            ],
            [],
            [
            'customer_id'=> 'Khách hàng',
        ]);
        if($validate->fails()){
            return $this->sendResponse($validate->errors(),422);
        }
        $data = $request->all();
        $customer = Customer::find($request->input('customer_id'));
        $customer->updated_log_at = now();
        $customer->save();
        $data['user_id'] = user()->id;
        $data['nhu_cau'] = $customer->category_id;
        $data['from_status'] = $customer->status_id;
        $data['to_status'] = $customer->status_id;
        $data['created_at'] = now();
        CustomerLog::create($data);
        return $this->sendResponse([
            'message' => 'Cập nhật trạng thái thành công!'
        ],201);
    }
}
