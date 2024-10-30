<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Group;
use App\Models\TransactionType;
use AsfyCode\Utils\Request;

class FinanceController extends Controller{
    public function index(){
        $groups = Group::get();
        return view("reports.index",compact('groups'));
    }

    public function allo(){
        $allo = Allocation::orderBy('time','desc')->get();
        $group = Group::all();
        $transtype = TransactionType::where('type',2)->get();
        return view('reports.allo',compact('allo','group','transtype'));
    }
}
