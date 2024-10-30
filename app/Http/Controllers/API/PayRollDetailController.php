<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\RollDetailPriceUser;
use AsfyCode\Utils\Request;

class PayRollDetailController extends Controller{
    public function getPrice(Request $request){
        $id = $request->input('id');
        $list = RollDetailPriceUser::where('roll_detail_id',$id);
        if($request->has('type')){
            $type = $request->input('type');
            
            $list->whereHas('type',function($query) use ($type){
                $query->where('type',$type);
            });
        }

        $list->with('type');
        $list = $list->orderBy('created_at','desc')
        ->get();
        return $this->sendResponse($list);
    }
}
