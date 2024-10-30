<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerLog;
use App\Models\CustomerNotify;
use App\Models\CustomerStatus;
use App\Models\User;
use App\Repositories\UserRepository;
use AsfyCode\Utils\Request;
use DateTime;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerController extends Controller
{
    # [GET] /  =>  Danh sách dữ liệu 
    public function index(Request $request)
    {
        $customers = Customer::query();

        if($request->has('order')){
            $customers->orderBy($request->order,'desc');
        }else{
            $customers->orderBy('created_at', 'desc');
        }

        if ($request->has('name')) {
            $customers->where('customers.name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('phone')) {
            $customers->where('customers.phone', 'like', '%' . $request->phone . '%');
        }
        $dates = currentMonth();
        if ($request->has('dates')) {
            $dates = $request->dates;
        } else {
            $request->merge('dates', $dates);
        }
        $dateQuery = getDateQuery($dates);
        $customers->whereBetween('created_at', $dateQuery);

        if ($request->has('status')) {
            $customers->where('status_id', $request->status);
        }
        if ($request->has('category_id')) {
            $customers->where('category_id', $request->category_id);
        }
        if ($request->has('province_id')) {
            $customers->where('province_id', $request->province_id);
        }
        if ($request->has('sale_channel_id')) {
            $customers->where('sale_channel_id', $request->sale_channel_id);
        }
        if ($request->has('district_id')) {
            $customers->where('district_id', $request->district_id);
        }
        if ($request->has('ward_id')) {
            $customers->where('ward_id', $request->ward_id);
        }
        if ($request->has('user_id')) {
            $customers->where('customers.user_id', $request->user_id);
        }
        if ($request->has('group_id')) {
            $customers->whereHas('user', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
        }
        if ($request->has('log')) {
            if ($request->log == 2) {
                $customers->whereDoesntHave('logs');
            } else {
                $customers->whereHas('logs');
            }
        }

        $customers->with([
            'user',
            'user.group',
            'status',
            'category',
            'province',
            'district',
            'latestLog',
            'sale_channel',
            'ward'
        ]);

        $data_status = (clone $customers)
        ->rightJoin('customer_status', 'customer_status.id', '=', 'customers.status_id')
        ->groupBy('customers.status_id')
        ->select(Manager::raw('COUNT(customers.id) as count_customer'), 'customer_status.*')->get();
        
        $not_status = (clone $customers)->whereDoesntHave('logs')->count();
        $response = $request->paginate($customers)->setAttribute(compact('not_status','data_status'));
        $response->data->map(function ($item) {
            $item->append('areas');
        });
        // not_status
        return $this->sendResponse($response);
    }

    # [POST] /create  =>  Thực thi thêm dữ liệu 
    public function store(Request $request)
    {
        $validate = $request->validate(
            [
                'name' => 'required',
                'phone' => 'required',
                'phone_2' => 'number|length:10',
                'user_id' => 'required',
                'status_id' => 'required'
            ],
            [],
            [
                'name' => 'Tên nhóm',
                'phone' => 'Số điện thoại',
                'phone_2' => 'Số điện thoại',
                'status_id' => 'Trạng thái',
                'user_id' => 'Nhân sự',
            ]
        );
        if ($validate->fails()) {
            return $this->sendResponse($validate->errors(), 422);
        }
        $customer = Customer::where('phone', $request->phone)->first();
        if ($customer) {
            return $this->sendResponse([
                'phone' => ['Khách hàng này đã tồn tại!']
            ], 422);
        }
        if(in_array($request->input('status_id'),[status('order_customer'),status('customer_success')])){
            $validate = $request->validate(
                [
                    'province_id' => 'required',
                    'district_id' => 'required',
                    'ward_id' => 'required'
                ],
                [],
                [
                    'province_id' => 'Tỉnh thành',
                    'district_id' => 'Quận huyện',
                    'ward_id' => 'Thị xã'
                ]
            );
            if ($validate->fails()) {
                return $this->sendResponse($validate->errors(), 422);
            }
        }
        try {
            Manager::beginTransaction();
            $dataCreate = [
                'name' => $request->name,
                'phone' => $request->phone,
                'phone_2' => $request->phone_2,
                'category_id' => $request->category_id,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'sale_channel_id' => $request->sale_channel_id,
                'ward_id' => $request->ward_id,
                'status_id' => $request->status_id,
                'address' => $request->address,
                'user_id' => user()->id
            ];
            if($request->input('status_id') == status('customer_schelude')){
                $dataCreate['schedule'] = formatDate($request->schedule);
            }else{
                $dataCreate['schedule'] = NULL;
            }
            $customer = Customer::create($dataCreate);


            if ($request->has('note') && $request->note != '') {
                CustomerLog::create([
                    'customer_id' => $customer->id,
                    'user_id' => user()->id,
                    'nhu_cau' => $customer->category_id,
                    'from_status' => 0,
                    'to_status' => $customer->status_id,
                    'content' => $request->note,
                    'created_at' => now()
                ]);
                $customer->updated_log_at = now();
                $customer->save();
            }
            Manager::commit();
        } catch (\Exception $e) {
            logError($e);
            Manager::rollBack();
            return $this->sendResponse([
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau!'
            ], 500);
        }
        return $this->sendResponse([
            'id' => $customer->id,
            'check' => $customer->status->type == 1,
            'message' => 'Thêm khách hàng thành công!'
        ], 201);
    }

    # [GET] /{id}  =>  Xem thông tin một bản ghi 
    public function show($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            return $this->sendResponse($customer);
        } catch (ModelNotFoundException $e) {
            return $this->sendResponse([
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }
    }

    # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
    public function update($id, Request $request)
    {
        $validate = $request->validate(
            [
                'name' => 'required',
                'phone' => 'required',
                'user_id' => 'required',
                'status_id' => 'required'
            ],
            [],
            [
                'name' => 'Tên nhóm',
                'phone' => 'Số điện thoại',
                'status_id' => 'Trạng thái',
                'user_id' => 'Nhân sự',
            ]
        );
        if ($validate->fails()) {
            return $this->sendResponse($validate->errors(), 422);
        }
        $customer = Customer::where('phone', $request->phone)->where('id','!=',$id)->first();
        if ($customer) {
            return $this->sendResponse([
                'phone' => ['Khách hàng này đã tồn tại!']
            ], 422);
        }
        if(in_array($request->input('status_id'),[status('order_customer'),status('customer_success')])){
            $validate = $request->validate(
                [
                    'province_id' => 'required',
                    'district_id' => 'required',
                    'ward_id' => 'required'
                ],
                [],
                [
                    'province_id' => 'Tỉnh thành',
                    'district_id' => 'Quận huyện',
                    'ward_id' => 'Thị xã'
                ]
            );
            if ($validate->fails()) {
                return $this->sendResponse($validate->errors(), 422);
            }
        }
        $dataUpdate = [
            'name' => $request->name,
            'phone' => $request->phone,
            'phone_2' => $request->phone_2,
            'category_id' => $request->category_id,
            'province_id' => $request->province_id,
            'sale_channel_id' => $request->sale_channel_id,
            'district_id' => $request->district_id,
            'ward_id' => $request->ward_id,
            'updated_log_at' => now(),
            'status_id' => $request->status_id,
            'address' => $request->address,
        ];

        if($request->input('status_id') == status('customer_schelude')){
            $dataUpdate['schedule'] = formatDate($request->schedule);
        }else{
            $dataUpdate['schedule'] = NULL;
        }
        try {
            $customer = Customer::findOrFail($id);
            $currentStatus = $customer->status_id;
            try {
                Manager::beginTransaction();
                $customer->update($dataUpdate);
                CustomerLog::create([
                    'customer_id' => $customer->id,
                    'user_id' => user()->id,
                    'nhu_cau' => $customer->category_id,
                    'from_status' => $currentStatus,
                    'to_status' => $customer->status_id,
                    'content' => $request->note,
                    'created_at' => now()
                ]);
                Manager::commit();
            } catch (\Exception $e) {
                logError($e);
                Manager::rollBack();
                return $this->sendResponse([
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại sau!'
                ], 500);
            }
        } catch (ModelNotFoundException $e) {
            return $this->sendResponse([
                'message' => 'Không tìm thấy khách hàng'
            ], 404);
        }
        return $this->sendResponse([
            'id' => $customer->id,
            'check' => $customer->status->type == 1,
            'message' => 'Cập nhật khách hàng thành công!'
        ], 200);
    }

    public function expired(Request $request)
    {
        $customers = Customer::query()
            ->orderBy('updated_log_at', 'asc');

        if ($request->has('name')) {
            $customers->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('phone')) {
            $customers->where('phone', 'like', '%' . $request->phone . '%');
        }
        if ($request->has('group')) {
            $group = $request->group;
            $customers->whereHas('user', function ($q) use ($group) {
                $q->where('group_id', $group);
            });
        }

        if ($request->has('schedule') && $request->schedule == 1) {
            $customers->where('status_id', $request->status)
            ->whereDate('schedule','<=',now());
        }else{
            if ($request->has('status')) {
                $status = CustomerNotify::where('status_id', $request->status);
                if ($request->has('group')) {
                    $status->where('group_id', $group);
                }
                $status = $status->orderBy('time_notify','asc')->get();
                $first = (clone $status)->first();
                $groupIds = (clone $status)->pluck('group_id');

                $customers->where('status_id', $first->status_id);
                $timeThreshold = (new DateTime())->modify("-{$first->time_notify} days")->format("Y-m-d H:i:s");
                $customers->where('updated_log_at','<', $timeThreshold);
                $customers->whereHas('user',function($query) use ($groupIds) {
                    $query->whereIn('group_id', $groupIds);
                });
            }else{
                $customers->where('status_id', 0);
            }
        }

        $customers->with([
            'user',
            'user.group',
            'status',
            'category',
            'province',
            'district',
            'latestLog',
            'sale_channel',
            'ward'
        ]);

        $not_status = (clone $customers)->whereDoesntHave('logs')->count();
        $response = $request->paginate($customers)->setAttribute(compact('not_status'));
        $response->data->map(function ($item) {
            $item->append('areas');
        });
        // not_status
        return $this->sendResponse($response);
    }

    public function updateStatus(Request $request){
        $listIds = [];
        foreach($request->all() as $value){
            if($value['id']){
                $status = CustomerStatus::find($value['id']);
            }else{
                $status = new CustomerStatus();
            }
            $status->name = $value['name'];
            if($value['type'] && $value['type'] != 'null'){
                $status->type = $value['type'];
            }
            $status->sort = $value['sort'];
            $status->color = $value['color'];
            $status->bg = $value['bg'];
            $status->save();
            $listIds[] = $status->id;
        }
        CustomerStatus::whereNotIn('id',$listIds)->delete();
        return $this->sendResponse([
            'message'=> 'Cập nhật trạng thái thành công!'
        ],200);
    }
}
