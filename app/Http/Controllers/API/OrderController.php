<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderLogController;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLogs;
use App\Models\OrderStatus;
use App\Models\OrderUpdateLog;
use App\Repositories\UserRepository;
use AsfyCode\Utils\Request;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

class OrderController extends Controller
{
    # [GET] /  =>  Danh sách dữ liệu 
    public function index(Request $request)
    {
        $orders = Order::query();

        $dates = currentMonth();
        if(!$request->has('nosearchdate')){
            $dates = currentMonth();
            if ($request->has('dates')) {
                $dates = $request->dates;
            } else {
                $request->merge('dates', $dates);
            }
            $dateQuery = getDateQuery($dates);
            $orders->whereBetween('created_at', $dateQuery);
            if ($request->has('finish_at')) {
                $dateSearch = getDateQuery($request->finish_at);
                $orders->whereBetween('finish_at', $dateSearch);
            } else {
                $orders->whereBetween('created_at', $dateQuery);
            }
        }
        if ($request->has('city_id')) {
            $orders->where('city_id', $request->city_id);
        }
        if ($request->has('customer_id')) {
            $orders->where('customer_id', $request->customer_id);
        }
        if ($request->has('status')) {
            $orders->where('status_id', $request->status);
        }
        if ($request->has('nostatus')) {
            $orders->where('status_id','!=', $request->nostatus);
        }
        if ($request->has('user_id')) {
            $orders->where('user_id', $request->user_id);
        }
        if ($request->has('group_id')) {
            $orders->whereHas('user', function($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
        }


        $orders->with([
            'customer:id,name',
            'user:id,name,group_id',
            'user.group',
            'current_status',
            'status',
        ]);
        $data_status = (clone $orders)
        ->rightJoin('order_status', 'order_status.id', '=', 'orders.status_id')
        ->groupBy('orders.status_id')
        ->select(Manager::raw('COUNT(orders.id) as count_order'), 'order_status.*')->get();

        $orders->orderBy('created_at', 'desc');
        $response = $request->paginate($orders)->setAttribute(compact('data_status'));
        $response->data->map(function ($item) {
            $item->append('areas');
            $item->append('price_pending');
        });
        return $this->sendResponse($response);
    }

    # [POST] /create  =>  Thực thi thêm dữ liệu 
    public function store(Request $request)
    {
        $validate = $request->validate(
            [
                'customer_id' => 'required',
                'du_kien' => 'required',
                'province_id' => 'required',
                'district_id' => 'required',
                'status_id' => 'required',
            ],
            [],
            [
                'customer_id' => 'Khách hàng',
                'du_kien' => 'Dự kiến',
                'province_id' => 'Tỉnh thành',
                'district_id' => 'Quận huyện',
                'status_id' => 'Trạng thái',
            ]
        );
        $errors = $validate->errors();
        if ($validate->fails()) {
            return $this->sendResponse($errors, 422);
        }
        // if (!Customer::where('id', $request->customer_id)->where('status_id', status('order_customer'))->exists()) {
        //     $errors['customer_id'][] = 'Khách hàng chưa xác nhận!';
        //     return $this->sendResponse($errors, 422);
        // }
        $customer = Customer::findOrFail($request->customer_id);
        $customer->status_id = status('order_customer');
        $customer->save();
        try{
            Manager::beginTransaction();
            $dataOrder = [
                'customer_id' => $request->customer_id,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'ward_id' => $request->ward_id,
                'address' => $request->address,
                'du_kien' => $request->du_kien,
                'du_kien_time' => formatDate($request->du_kien_time),
                'thuc_thu' => 0,
                'note' => $request->input('note'),
                'status_id' => $request->status_id,
                'user_id' => user()->id,
            ];
            if($request->status_id == status('order_success')){
                $dataOrder['finish_at'] = now();
                $customer = Customer::findOrFail($request->customer_id);
                $customer->update([
                    'status_id' => status('customer_success')
                ]);
            }
            if($request->status_id == 9){
                $dataOrder['rela'] = $request->input('user_tk',NULL);
                $dataOrder['current_status'] = 16;
            }else if($request->status_id == 10){
                $dataOrder['rela'] = $request->input('factory_id',NULL);
                $dataOrder['current_status'] = 19;
            }
            $order = Order::create($dataOrder);
            if($request->input('status_id') == status('order_back')){
                $order->finish_at = now();
                $order->save();
            }
            if($request->has('thuc_thu') && $request->thuc_thu != 0){
                if($request->has('bank_account_id')){
                    $dataOdl = [
                        'order_id' => $order->id,
                        'amount' => $request->thuc_thu,
                        'bank_account_id' => $request->bank_account_id,
                        'user_id' => user()->id,
                        'note' => $request->input('note_giao_dich',''),
                    ];
                    if($request->thuc_thu >= 0){
                        $dataOdl['status'] = 2;
                    }else{
                        $dataOdl['status'] = 1;
                    }
                    $ol = OrderLogs::create($dataOdl);
                    $orderAfter = new OrderLogController;
                    $orderAfter->afterStore($ol);
                }
            }
            Manager::commit();
        }catch(\Exception $e){
            Manager::rollBack();
            return $this->sendResponse([
                'message' => 'Thêm đơn hàng thất bại!'
            ], 400);
        }
        return $this->sendResponse([
            'id' => $order->id,
            'message' => 'Thêm đơn hàng thành công!'
        ], 201);
    }

    # [GET] /{id}  =>  Xem thông tin một bản ghi 
    public function show($id)
    {
        try {
            $order = Order::with('customer')->findOrFail($id);
            return $this->sendResponse($order);
        } catch (ModelNotFoundException $e) {
            return $this->sendResponse([
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }
    }

    # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
    public function update($id, Request $request)
    {
        $validate = $request->validate(
            [
                'customer_id' => 'required',
                'du_kien' => 'required',
                'province_id' => 'required',
                'district_id' => 'required',
                'status_id' => 'required',
            ],
            [],
            [
                'customer_id' => 'Khách hàng',
                'du_kien' => 'Dự kiến',
                'province_id' => 'Tỉnh thành',
                'district_id' => 'Quận huyện',
                'status_id' => 'Trạng thái',
            ]
        );
        $errors = $validate->errors();
        if ($validate->fails()) {
            return $this->sendResponse($errors, 422);
        }
        try {
            Manager::beginTransaction();
            $data = $request->all();
            $order = Order::findOrFail($id);
            if($request->input('status_id') == status('order_back')){
                $order->finish_at = now();
                $order->save();
            }
            unset($data['bank_account_id']);
            unset($data['thu_them']);
            unset($data['note_giao_dich']);
            $data['du_kien_time'] = formatDate($request->du_kien_time);
            $orderArray = $order->getAttributes();
            if($request->status_id == 9){
                $data['rela'] = $request->input('user_tk',NULL);
                $data['current_status'] = 16;
            }else if($request->status_id == 10){
                $data['rela'] = $request->input('factory_id',NULL);
                $data['current_status'] = 19;
            }
            unset($data['user_tk']);
            unset($data['factory_id']);
            $order->update($data);
            if ($orderArray != $order->getAttributes()) {
                foreach ($order->getAttributes() as $key => $value) {
                    if ($orderArray[$key] != $value) {
                        if (isset(status('orderLogType')[$key])) {
                            OrderUpdateLog::create([
                                'order_id' => $order->id,
                                'type' => $key,
                                'from' => $orderArray[$key],
                                'to' => $value,
                                'user_id' => user()->id,
                                'created_at' => now(),
                            ]);
                        }
                    }
                }
            }
            if ($request->has('thu_them') && $request->thu_them != 0) {
                if ($request->has('bank_account_id')) {
                    $dataOdl = [
                        'order_id' => $order->id,
                        'amount' => $request->thu_them,
                        'bank_account_id' => $request->bank_account_id,
                        'user_id' => user()->id,
                        'note' => $request->input('note_giao_dich', ''),
                    ];
                    if ($request->thu_them >= 0) {
                        $dataOdl['status'] = 2;
                    } else {
                        $dataOdl['status'] = 1;
                    }
                    $ol = OrderLogs::create($dataOdl);
                    $orderAfter = new OrderLogController;
                    $orderAfter->afterStore($ol);
                }
            }else{
                $orderAfter = new OrderLogController;
                $orderAfter->checkOrder($order);
            }
            Manager::commit();
        } catch (ModelNotFoundException $e) {
            return $this->sendResponse([
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        } catch (\Exception $e) {
            return $this->sendResponse([
                'error' => $e->getMessage(),
                'message' => 'Đã xảy ra lỗi, vui lòng thử lại sau',
            ], 500);
        }

        return $this->sendResponse([
            'id' => $order->id,
            'message' => 'Cập nhật đơn hàng thành công'
        ]);
    }

    public function updateStatus(Request $request)
    {
        $listIds = [];
        foreach ($request->all() as $value) {
            if ($value['id']) {
                $status = OrderStatus::where('type_data',1)->find($value['id']);
            } else {
                $status = new OrderStatus();
            }
            $status->name = $value['name'];
            if ($value['type'] && $value['type'] != 'null') {
                $status->type = $value['type'];
            }
            $status->sort = $value['sort'];
            $status->color = $value['color'];
            $status->bg = $value['bg'];
            $status->save();
            $listIds[] = $status->id;
        }
        OrderStatus::whereNotIn('id', $listIds)->where('type_data',1)->delete();
        return $this->sendResponse([
            'data' => $request->all(),
            'message' => 'Cập nhật trạng thái thành công!'
        ], 200);
    }
}
