<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Order;
use App\Models\OrderLogs;
use App\Models\User;
use App\Repositories\UserRepository;
use AsfyCode\Facades\DB;
use AsfyCode\Utils\Request;
use Illuminate\Database\Capsule\Manager;

class HomeController extends Controller
{
    public $data = [];
    public function index(Request $request)
    {
        if ($request->has('dates')) {
            $date = $request->dates;
        } else {
            $date = currentMonth();
        }
        $dateSearch = getDateQuery($date);
        $_GET['dates'] = $dateSearch;
        $this->chartAllGroup($dateSearch);
        $this->thongKe($dateSearch);
        $this->thongKeDHKH($dateSearch);
        return $this->sendResponse($this->data);
    }
    public function thongKeDHKH($dateSearch)
    {
        $groups = Group::all();
        foreach ($groups as $group) {
            $userQuery = User::where('group_id', $group->id);
            $group->customer_count = (clone $userQuery)
            ->rightJoin('customers','customers.user_id','=','users.id')
            ->whereBetween('customers.created_at',$dateSearch)
            ->count() ;
            $group->order_count = (clone $userQuery)
            ->rightJoin('orders','orders.user_id','=','users.id')
            ->whereBetween('orders.created_at',$dateSearch)
            ->count() ;
        }
        $this->data['chartUserOK'] = $groups;
    }
    public function chartAllGroup($dateSearch)
    {
        $chartAllGroupQuery = Manager::table('orders as o')
            ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
            ->join('groups as g', 'g.id', '=', 'u.group_id')
            ->select(Manager::raw('SUM(o.thuc_thu) as total_thuc_thu'), 'g.name as group_name')
            ->groupBy('g.id');

        $chartAllGroup = (clone $chartAllGroupQuery)->whereBetween('o.finish_at', $dateSearch)
            ->get();
        $this->data['chartAllGroup'] = $chartAllGroup;

        // $chartAllGroupTTD = (clone $chartAllGroupQuery)->get();
        $chartAllGroupTTD = Manager::table('order_logs as ol')
        ->where('ol.status',2)
        ->whereBetween('ol.created_at',$dateSearch)
        ->leftJoin('users as u', 'ol.user_id', '=', 'u.id')
        ->join('groups as g', 'g.id', '=', 'u.group_id')
        ->select(Manager::raw('SUM(ol.amount) as total_thuc_thu'), 'g.name as group_name')
        ->groupBy('g.id')
        ->get();
        $this->data['chartAllGroupTTD'] = $chartAllGroupTTD;

        // Doanh thu dự kiến của đơn hàng
        $orderDuKien = Manager::table('orders as o')->whereBetween('o.created_at',$dateSearch)
        ->select(Manager::raw('SUM(o.du_kien) as total_du_kien'), 'g.name as group_name')
        ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
        ->join('groups as g', 'g.id', '=', 'u.group_id')
        ->groupBy('g.id')->get();
        $this->data['orderDuKien'] = $orderDuKien;

        $chartUserGroupTTD = User::query();
        $chartUserGroupTTD->withSum(['transactions' => function ($query) use ($dateSearch) {
            $query->whereBetween('created_at', $dateSearch);
        }], 'amount');

        $chartUserGroupTTD->having('transactions_sum_amount', '>', 0);

        $this->data['chartUserGroupTTD'] = $chartUserGroupTTD->get();
    }

    public function thongKe($dateSearch)
    {
        /*
        * Tổng số khách hàng
        * Tổng số đơn hàng
        * Tổng số tiền thu được hôm nay
        * Tổng số tiền thu được theo kỳ
        * Doanh thu đơn hàng đã hoàn thành
        * Tổng số đơn hàng chưa hoàn thành
        * Tổng số đơn hàng đã hoàn thành
        * Tổng số công nợ cần thu
        */

        // Tổng số khách hàng
        $customerCount = Customer::whereBetween('created_at', $dateSearch)
            ->count();
        $this->data['customerCount'] = $customerCount;


        $orderQuery = Order::whereBetween('created_at', $dateSearch);

        // Tổng số đơn hàng
        $orderCount = (clone $orderQuery)->count();
        $this->data['orderCount'] = $orderCount;

        $orderQueryFinish = Order::whereBetween('finish_at', $dateSearch);

        $orderSuccessCount = (clone $orderQueryFinish)
            ->where('status_id', status('order_success'))
            ->count();
        $orderThucThuSuccess = (clone $orderQueryFinish)
            ->where('status_id', status('order_success'))
            ->sum('thuc_thu');
        $this->data['orderSuccessCount'] = $orderSuccessCount;
        $this->data['orderThucThuSuccess'] = $orderThucThuSuccess;

        $orderUnSuccessCount = (clone $orderQueryFinish)->where('status_id', '!=', status('order_success'))
            ->count();
        $this->data['orderUnSuccessCount'] = $orderUnSuccessCount;

        // Đơn hàng bỏ cọc
        $orderBoCoc = (clone $orderQueryFinish)->where('status_id', status('order_back'))->sum('thuc_thu');
        $this->data['orderBoCoc'] = $orderBoCoc;

        // Tiền thu được hôm nay
        $tienThu = OrderLogs::where('status', 2);
        $this->data['tienThuHomNay'] = (clone $tienThu)->whereBetween('created_at', [
            now('Y-m-d 00:00:00'),
            now('Y-m-d 23:59:59'),
        ])->sum('amount');

        $this->data['tienThuTheoKy'] = (clone $tienThu)->whereBetween('created_at', $dateSearch)->sum('amount');

        $congNoCanThu = Order::sum(Manager::raw('du_kien - thuc_thu'));
        $this->data['congNoCanThu'] = $congNoCanThu;

        $totalPriceOrderDuKien = Order::whereBetween('created_at',$dateSearch)
            ->sum('du_kien');
        $this->data['totalPriceOrderDuKien'] = $totalPriceOrderDuKien;
    }
}
