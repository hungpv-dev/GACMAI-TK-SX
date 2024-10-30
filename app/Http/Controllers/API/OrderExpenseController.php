<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\OrderExpenses;
use AsfyCode\Utils\Request;

class OrderExpenseController extends Controller{
    public function index(Request $request){
        $response = OrderExpenses::query();
        if($request->has('order_id')){
            $response->where('order_id',$request->order_id);
        }
        $response->with(['type','user']);
        return $this->sendResponse($request->paginate($response));
    }
    public function store(Request $request){
        $data = $request->all();
        $data['created_at'] = now();
        $data['user_id'] = user()->id;
        OrderExpenses::create($data);
        user_logs('Thêm chi phí cho đơn hàng');
        return $this->sendResponse([
            'id' => $data['order_id'],
            'message' => 'Thêm chi phí thành công!',
        ],201);
    }
}
