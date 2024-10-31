<?php
namespace App\Http\Controllers\API\Xuong;
use App\Http\Controllers\API\OrderUpdateLogController;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLogs;
use App\Models\OrderUpdateLog;
use App\Repositories\UserRepository;
use AsfyCode\Utils\Request;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class OrderController extends Controller{
    public function index(Request $request,UserRepository $userRepository)
    {   
        $facIds = $userRepository->checkXuong();

        $orders = Order::query()->whereIn('rela',$facIds);

        $dates = currentMonth();
        
        if ($request->has('city_id')) {
            $orders->where('province_id', $request->city_id);
        }
        if ($request->has('expried')) {
            $orders->where('du_kien_time','<', now());
        }
        if ($request->has('customer_id')) {
            $orders->where('customer_id', $request->customer_id);
        }
        if ($request->has('type_status')) {
            $orders->where('current_status', $request->type_status);
        }
        if ($request->has('nostatus')) {
            $orders->where('status_id','!=', $request->nostatus);
        }
        if ($request->has('user_id')) {
            $orders->where('rela', $request->user_id);
        }
        if ($request->has('group_id')) {
            $orders->whereHas('user', function($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
        }
        
        if ($request->has('status')) {
            $orders->where('status_id', $request->status);
        }

        $data_status = (clone $orders)
        ->rightJoin('order_status', 'order_status.id', '=', 'orders.status_id')
        ->groupBy('orders.status_id')
        ->select(Manager::raw('COUNT(orders.id) as count_order'), 'order_status.*')->get();

        $orders->with([
            'customer:id,name',
            'xuong',
            'current_status',
            'status',
            'category'
        ]);

        $orders->orderBy('created_at', 'desc');
        $response = $request->paginate($orders)->setAttribute(compact('data_status'));
        $response->data->map(function ($item) {
            $item->append('areas');
            $item->append('price_pending');
        });
        return $this->sendResponse($response);
    }

    public function store(Request $request){
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
            $customer = Customer::findOrFail($request->customer_id);
            Manager::beginTransaction();
            $dataOrder = [
                'customer_id' => $request->customer_id,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'ward_id' => $request->ward_id,
                'address' => $request->address,
                'du_kien' => $request->du_kien,
                'du_kien_time' => formatDate($request->du_kien_time),
                'thuc_thu' => $request->thuc_thu,
                'note' => $request->input('note'),
                'status_id' => $request->status_id,
                'user_id' => $request->user_id,
            ];
            if($request->status_id == 10){
                $dataOrder['rela'] = $request->input('factory_id',NULL);
                $dataOrder['current_status'] = 19;
            }
            $order = Order::create($dataOrder);
            Manager::commit();
        } catch (\Exception $e) {
            Manager::rollBack();
            return $this->sendResponse([
                'message' => 'Thêm đơn hàng thất bại!'
            ], 400);
        }

        user_logs('Thêm mới đơn hàng!');
        return $this->sendResponse([
            'id' => $order->id,
            'message' => 'Thêm đơn hàng thành công!'
        ], 201);
    }

    # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
    public function update($id, Request $request)
    {
        $validate = $request->validate(
            [
                'customer_id' => 'required',
                'province_id' => 'required',
                'district_id' => 'required',
                'status_id' => 'required',
            ],
            [],
            [
                'customer_id' => 'Khách hàng',
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
            $order = Order::findOrFail($id);
            if(in_array($request->input('status_id'),[status('order_done')])){
                if($order->status_id != 13){
                    $order->finish_at = now();
                }
            }
            $order->save();

            $orderArray = $order->getAttributes();
            $order->update(['status_id' => $request->status_id]);
            if ($orderArray != $order->getAttributes()) {
                foreach ($order->getAttributes() as $key => $value) {
                    if ($orderArray[$key] != $value) {
                        if (isset(status('orderLogType')[$key])) {
                            $from = is_null($orderArray[$key]) ? $orderArray[$key] : 0;
                            OrderUpdateLog::create([
                                'order_id' => $order->id,
                                'type' => $key,
                                'from' => $from,
                                'to' => $value,
                                'user_id' => user()->id,
                                'created_at' => now(),
                            ]);
                        }
                    }
                }
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
}
