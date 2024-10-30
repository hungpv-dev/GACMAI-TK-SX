<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\District;
use App\Models\OrderStatus;
use App\Models\OrderUpdateLog;
use App\Models\Province;
use App\Models\Ward;
use AsfyCode\Utils\Request;

class OrderUpdateLogController extends Controller{
    public function index(Request $request){
        $orderUpdateLogs = OrderUpdateLog::query();
        if($request->order_id){
            $orderUpdateLogs->where('order_id',$request->order_id);
        }
        $orderUpdateLogs->orderBy('created_at','desc');
        $orderUpdateLogs->with([
            'user:id,name',
        ]);
        $response = $request->paginate($orderUpdateLogs,$request->input('limit',50));
        $response->data->each(function($item){
            $from = $item->from;
            $to = $item->to;
            switch($item->type){
                case 'customer_id':{
                    $item->from = Customer::find($from)->name ?? null;
                    $item->to = Customer::find($to)->name ?? null;
                    break;
                }
                case 'province_id':{
                    $item->from = Province::find($from)->name ?? null;
                    $item->to = Province::find($to)->name ?? null;
                    break;
                }
                case 'district_id':{
                    $item->from = District::find($from)->name ?? null;
                    $item->to = District::find($to)->name ?? null;
                    break;
                }
                case 'ward_id':{
                    $item->from = Ward::find($from)->name ?? null;
                    $item->to = Ward::find($to)->name ?? null;
                    break;
                }
                case 'status_id':{
                    $item->from = OrderStatus::where('type_data',1)->find($from)->name ?? null;
                    $item->to = OrderStatus::where('type_data',1)->find($to)->name ?? null;
                    break;
                }
                case 'du_kien':{
                    $item->from = number_format($from).' ₫';
                    $item->to = number_format($to).' ₫';
                    break;
                }
                case 'finish_at':
                case 'du_kien_time':{
                    $item->from = dateFormat($from);
                    $item->to = dateFormat($to);
                    break;
                }
                default:{
                    $item->from = $from;
                    $item->to = $to;
                    break;
                }
            }
            $item->type = status('orderLogType')[$item->type];
        });
        return $this->sendResponse($response);
    }
}
