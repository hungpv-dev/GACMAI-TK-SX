<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\CustomerStatus;
use App\Models\Order;
use App\Models\OrderStatus;

class TypeDBController extends Controller{

    public $user = [
        1 => 'Hoạt động',
        2 => 'Ngưng hoạt động',
    ];

    public $order_log = [
        1 => 'Chưa duyệt',
        2 => 'Đã duyệt',
        3 => 'Đã hủy',
    ];

    public $orderLogType = [
        'customer_id' => 'Khách hàng',
        'province_id' => 'Tỉnh thành',
        'district_id' => 'Quận huyện',
        'ward_id' => 'Thị xã',
        'address' => 'Địa chỉ',
        'current_status' => 'Trạng thái',
        'du_kien' => 'Doanh thu dự kiến',
        'finish_at' => 'Ngày hoàn thành',
        'du_kien_time' => 'Thời gian dự kiến',
        'status_id' => 'Trạng thái',
        'user_id' => 'Người phụ trách',
        'note' => 'Ghi chú',
     ];

    public $order;
    public $order_xuong;

    // order_customer => 'Khách hàng được phép tạo tk';
    public $order_customer; 
    
    // order_success => 'Đơn hàng hoành thành';
    public $order_success;

     // order_success => 'Trạng thái khách hàng bỏ cọc';
     public $order_back = 15;

     
     public $order_done;
     public $count_start_tk;
     public $count_start_xuong;


    //  customer_schelude => Trạng thái khách hẹn lịch
     public $customer_schelude = 41;

     public $noneGroup = [1];

    // customer_success => 'Khách hàng đã hoàn thành';
    public $customer_success;
    public function __construct(){
        $this->customer_success = CustomerStatus::where('type',2)->first()->id;
        $this->order_customer = CustomerStatus::where('type',1)->first()->id;
        $this->count_start_tk = Order::where('current_status',16)->where('status_id',9)->count();
        $this->count_start_xuong = Order::where('current_status',19)->where('status_id',10)->count();
        $this->order_success = OrderStatus::where('type',1)->where('type_data',1)->first()->id;
        $this->order_done = [$this->order_success,$this->order_back];

        $this->order = OrderStatus::orderBy('sort','asc')->where('type_data',2)->get();
        $this->order_xuong = OrderStatus::orderBy('sort','asc')->where('type_data',3)->get();
    }
}
