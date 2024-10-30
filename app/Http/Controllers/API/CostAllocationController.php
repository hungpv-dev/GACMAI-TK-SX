<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\CostAllocation;
use App\Models\Group;
use App\Models\Order;
use AsfyCode\Utils\Request;
use Exception;
use Illuminate\Database\Capsule\Manager;

class CostAllocationController extends Controller
{
    public function index(Request $request){
        $costs = CostAllocation::query();

        if($request->has('report')){
            $costs->where('allocation_id',$request->report);
        }
        
        if($request->has('tran')){
            $costs->where('tran_type_id',$request->tran);
        }

        if($request->has('group_id')){
            $costs->where('group_id',$request->group_id);
        }

        $costs->orderBy('created_at','desc');
        $costs->with('tran_type','group');
        $tongTien = (clone $costs)->sum('amount');
        return $this->sendResponse($request->paginate($costs)->setAttribute(compact('tongTien')));
    }


    public function store(Request $request)
    {
        try {
            Manager::beginTransaction();
            $date = \DateTime::createFromFormat('m-Y', $request->date);
            $formattedDate = $date->format('Y-m-15');
            $reports = Allocation::whereDate('time', $formattedDate)->first();
            if (!$reports) {
                $reports = Allocation::create(['time' => $formattedDate]);
            }
            $dateStart = date('Y-m-01 00:00:00', strtotime($formattedDate));
            $dateEnd = date('Y-m-t 23:59:59', strtotime($formattedDate));
            $dateQuery = [$dateStart, $dateEnd];

            $formattedDate = CostAllocation::create([
                'tran_type_id' => $request->input('tran_type_id'),
                'group_id' => $request->input('group_id'),
                'allocation_id' => $reports->id,
                'amount' => $request->input('amount'),
                'time' => $formattedDate,
                'note' => $request->input('note'),
                'user_id' => user()->id,
                'created_at' => now(),
            ]);
            $this->beforeStore($reports, $dateQuery);

            $this->updateRowAfter($reports);
            Manager::commit();
        } catch (Exception $e) {
            Manager::rollBack();
            return $this->sendResponse([
                'message' => 'Lỗi xảy ra, vui lòng thử lại sau!',
            ]);
        }
        return $this->sendResponse([
            'id' => $reports->id,
            'message' => 'Thêm mới chi phí thành công!'
        ], 201);
    }

    public function beforeStore($reports, $dateQuery)
    {
        $time = \DateTime::createFromFormat('Y-m-d H:i:s', $reports->time);
        if (!$time) {
            $time = \DateTime::createFromFormat('Y-m-d', $reports->time);
        }
        $time->modify('-1 month');
        $time = $time->format('Y-m-15');
        $beforeReport = Allocation::whereDate('time', $time)->first();
        $thangTruocChuyenQua = $beforeReport ? $beforeReport->chuyenthangsau : [];

        $listTranIds = $reports->details->pluck('tran_type_id')->unique();
        $orderQuery = Order::whereBetween('finish_at', $dateQuery);
        $groups = Group::all();
        $tongchiphi = [];
        $loinhuangop = [];
        $lailo = [];
        $price_dn = [];
        $price_trongthang = [];
        $price_cuoinam = [];
        $chuyenthangsau = [];
        $groups = $groups->map(function ($item) use (
            $listTranIds,
            $thangTruocChuyenQua,
            &$tongchiphi,
            &$loinhuangop,
            &$price_dn,
            &$price_trongthang,
            &$chuyenthangsau,
            &$price_cuoinam,
            &$lailo,
            $reports,
            $orderQuery
        ) {
            $typeGroup = 'group_' . $item->id;

            $userIds = $item->users()->pluck('id');
            $total_group = $item->allocations()->whereIn('tran_type_id', $listTranIds)->where('allocation_id',$reports->id)->sum('amount'); // Lấy tổng chi phí phân bổ
            $orders = (clone $orderQuery)
                ->whereIn('user_id', $userIds)
                ->get();
            $totalChiPhi = $orders->sum(function ($order) {
                return $order->thuc_thu - $order->expenses->sum('amount');
            });  // Lấy tổng tiền thu về 
            $lailoSum = $totalChiPhi - $total_group; // Xem lãi lỗ bao nhiêu

            $priceThangTrc = $thangTruocChuyenQua[$typeGroup] ?? 0; // Lấy tiền lỗ tháng trước nếu có

            $currentPrice = (float) $lailoSum + (float) $priceThangTrc; // Lỗ thì + với sỗ lỗ tháng trước

            if ($currentPrice >= 0) { // Sau khi +- nếu ra tiền dương thì chia tiền
                $price_dn[$typeGroup] = $currentPrice * 0.6;
                $price_trongthang[$typeGroup] = $currentPrice * 0.2;
                $price_cuoinam[$typeGroup] = $currentPrice * 0.2;
                $chuyenthangsau[$typeGroup] = 0; // Chuyển tháng sau không do không còn lỗ
            } else {
                $chuyenthangsau[$typeGroup] = $currentPrice; // Chuyển tiền lỗ sang tháng sau
            }

            $tongchiphi[$typeGroup] = $total_group;
            $loinhuangop[$typeGroup] = $totalChiPhi;
            $lailo[$typeGroup] = $lailoSum;
        });

        $reports->price_dn = $price_dn;
        $reports->price_trongthang = $price_trongthang;
        $reports->price_cuoinam = $price_cuoinam;
        $reports->chuyenthangsau = $chuyenthangsau;
        $reports->tongchiphi = $tongchiphi;
        $reports->loinhuangop = $loinhuangop;
        $reports->lailo = $lailo;
        $reports->save();
    }

    public function updateRowAfter($reports){
        $time = $reports->time;
        $listReports = Allocation::where('time','>',$time)->get();
        foreach($listReports as $r){
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $r->time);
            $formattedDate = $date->format('Y-m-15');
            $dateStart = date('Y-m-01 00:00:00', strtotime($formattedDate));
            $dateEnd = date('Y-m-t 23:59:59', strtotime($formattedDate));
            $dateQuery = [$dateStart, $dateEnd];
            $this->beforeStore($r,$dateQuery);
        }
    }

}
