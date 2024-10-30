<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\BankAccount;
use App\Models\Group;
use App\Models\Order;
use App\Models\PayRoll;
use App\Models\PayRollType;
use App\Models\Supplier;
use AsfyCode\Facades\DB;
use AsfyCode\Utils\Request;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FinanceController extends Controller{
    private Request $request;

    public function index(Request $request)
    {
        $this->request = $request;
        $type = $request->input('type', 'default');
        $dates = $request->input('dates');
        $dateQuery = getDateQuery($dates);
        if (method_exists($this, $type)) {
            $data = $this->$type($dateQuery,$dates);
        } else {
            $data = $this->default($dateQuery,$dates);
        }
        return response()->html($data->content);
    }

    public function default($dateQuery,$dates)
    {
        return $this->tongquan($dateQuery,$dates);
    }

    public function tongquan($dateQuery,$dates){
        $customerDebt = Order::sum(Manager::raw('du_kien - thuc_thu'));
        $ketoanSum = BankAccount::sum(Manager::raw('current_balance'));
        $supplierDebt = Supplier::sum('current_amount');
        $biendongsodu15Day = $this->biendongsodu15Day(); 
        return view('api.reports.index',compact('customerDebt','ketoanSum','biendongsodu15Day','supplierDebt'));
    }
    public function biendongsodu15Day(){
        $data = collect();
        $startDate = date('Y-m-d', strtotime('-14 days'));
        
        $banks = DB::table('bank_accounts')->get();
        for ($i = 0; $i <= 14; $i++) {
            $date = date('Y-m-d', strtotime("+$i days", strtotime($startDate)));
            $total = 0;
            foreach ($banks as $bank) {
                $tran = DB::table("transactions")
                ->join('transactions_type','transactions_type.id','=','transactions.transaction_type_id')
                ->select(
                    'transactions_type.type as tran_type',
                    'transactions.current_balance',
                    'transactions.amount',
                    'transactions.fee',
                    'transactions.created_at',
                    'transactions.bank_account_id',
                )
                ->where("bank_account_id",$bank->id)
                ->whereDate('created_at','<=',$date)
                ->orderBy('created_at','desc')
                ->first();
                if ($tran) {
                    $sum = 0;
                    if($tran->tran_type == 2){
                        $sum = $tran->current_balance - $tran->amount - $tran->fee;
                    }else{
                        $sum = $tran->current_balance + $tran->amount;
                    }
                }else{
                    $sum = $bank->opening_balance;
                }
                $total += $sum;
            }
            $data->push([
                'date' => $date,
                'total' => $total,
            ]);
        }
        return $data;
    }

    public function kqkd($dateQuery,$dates)
    {
        $chartAllGroup = Manager::table('orders as o')
            ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
            ->join('groups as g', 'g.id', '=', 'u.group_id')
            ->select(Manager::raw('SUM(o.thuc_thu) as total_thuc_thu'), 'g.id as group_id', 'g.name as group_name')
            ->whereBetween('o.finish_at', $dateQuery)
            ->groupBy('g.id')
            ->get()
            ->each(function ($item) {
                $item->total_chi_phi = Manager::table('order_expenses as oe')
                    ->leftJoin('orders as o', 'oe.order_id', '=', 'o.id')
                    ->leftJoin('users as u', 'o.user_id', '=', 'u.id')
                    ->where('u.group_id', $item->group_id)
                    ->sum('amount');
            });

        $chartAllGroupTTD = Manager::table('order_logs as ol')
            ->where('ol.status', 2)
            ->whereBetween('ol.created_at', $dateQuery)
            ->leftJoin('users as u', 'ol.user_id', '=', 'u.id')
            ->join('groups as g', 'g.id', '=', 'u.group_id')
            ->select(Manager::raw('SUM(ol.amount) as total_thuc_thu'),'g.id as group_id', 'g.name as group_name')
            ->groupBy('g.id')
            ->get();
        return view('api.reports.kqkd', compact('chartAllGroup', 'chartAllGroupTTD','dates'));
    }
    public function phanbochiphiloinhuan($dateQuery,$dates){
        $request = $this->request;
        $allocations = Allocation::orderBy('time','desc')->get();

        if($request->has('search')){
            $reports = $allocations->find($request->search);
            if(!$reports){
                $reports = $allocations->first();
            }
        }else{
            $reports = $allocations->first();
        }
        if($reports){
            $timeAfter = \DateTime::createFromFormat('Y-m-d H:i:s', $reports->time);
            $timeAfter->modify('-1 month');
            $timeAfter = $timeAfter->format('Y-m-15');
            $beforeReport = Allocation::whereDate('time', $timeAfter)->first(); // Bảng tháng trước
            $cost_allocations = $reports->details()
            ->select(Manager::raw('SUM(amount) as total_amount'),'name')
            ->groupBy('name')
            ->get();
        }else{
            $beforeReport = [];
            $cost_allocations = [];
        }

        $groups = Group::all();
        return view('api.reports.phanbochiphiloinhuan',compact('groups','cost_allocations','allocations','reports','beforeReport'));
    }
    public function congnoncc($dateQuery,$dates){
        $suppliers = Supplier::all();
        return view('api.reports.congnoncc',compact('suppliers','dateQuery','dates'));
    }
    public function bangluong($dateQuery,$dates){
        $request = $this->request;
        $listPayRoll = PayRoll::orderBy('year','desc')->orderBy('month','desc')->get();
        if($request->has('search')){
            $roll = $listPayRoll->find($request->search);
            if(!$roll){
                $roll = $listPayRoll->first();
            }
        }else{
            $roll = $listPayRoll->first();
        }
        $listPriceType = PayRollType::get();
        return view('payrolls.index',compact('roll','listPayRoll','listPriceType'));
    }
}
