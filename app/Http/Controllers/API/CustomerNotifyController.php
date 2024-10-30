<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\CustomerNotify;
use AsfyCode\Utils\Request;

class CustomerNotifyController extends Controller{
    public function index(Request $request){
        $customerNotify = CustomerNotify::query();
        if($request->has('group_id')){
            $customerNotify->where('group_id','=',$request->group_id);
        }
        if($request->has('status_id')){
            $customerNotify->where('status_id','=',$request->status_id);
        }
        $customerNotify->orderBy('group_id','asc');
        $customerNotify->orderBy('status_id','asc');
        $customerNotify->with([
            'group',
            'status',
        ]);
        return $this->sendResponse($request->paginate($customerNotify));
    }
    public function store(Request $request){
        try{
            $data = CustomerNotify::updateOrCreate(
                [
                    'group_id' => $request->input('group_id'),
                    'status_id' => $request->input('status_id'), 
                ],
                [
                    'time_notify' => $request->input('time'), 
                ]
            );
        }catch(\Exception $e){
            return $this->sendResponse([
                'message' => 'Đã xảy ra lỗi, thử lại sau',
            ],500);
        }
        return $this->sendResponse([
            'message' => 'Cập nhật thời gian thông báo thành công',
            'id' => $data->id
        ],201);
    }

    public function destroy($id){
        CustomerNotify::where('id',$id)->delete();
        return $this->sendResponse([],204);
    }
}
