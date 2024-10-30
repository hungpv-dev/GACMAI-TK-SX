<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use AsfyCode\Utils\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BankAccountController extends Controller{
    public function index(Request $request){
        $groups = BankAccount::query();

        if($request->has('bank_number')){
            $groups->where('bank_number','like','%'.$request->bank_number.'%');
        }
        if($request->has('full_name')){
            $groups->where('full_name','like','%'.$request->full_name.'%');
        }
        if($request->has('bank_id')){
            $groups->where('bank_id','like','%'.$request->bank_id.'%');
        }
        if($request->has('bank_account_type_id')){
            $groups->where('bank_account_type_id','like','%'.$request->bank_account_type_id.'%');
        }

        $groups->with([
            'bank',
            'bank_account_type'
        ]);
        $groups->orderBy('id','desc');
        $sum = (clone $groups)->sum('current_balance');
        return $this->sendResponse($request->paginate($groups)->setAttribute('sum',$sum));
    }

    # [POST] /create  =>  Thực thi thêm dữ liệu 
    public function store(Request $request){
        $validate = $request->validate([
            'bank_number' => 'required',
            'full_name' => 'required',
            'bank_id' => 'required',
            'bank_account_type_id' => 'required',
        ],[], [
            'bank_number' => 'Số tài khoản',
            'full_name' => 'Tên tài khoản',
            'bank_id' => 'Ngân hàng',
            'bank_account_type_id' => 'Loại tài khoản',
        ]);
        if($validate->fails()){
            return $this->sendResponse($validate->errors(),422);
        }
        $account = BankAccount::where('bank_id',$request->bank_id)->where('bank_number',$request->bank_number)->first();
        if($account){
            return $this->sendResponse([
                'bank_number' => ['Tài khoản ngày đã tồn tại!']
            ],422);
        }
        $data = $request->all();
        $data['user_id'] = user()->id;
        $data['current_balance'] = $request->input('opening_balance',0);
        $group = BankAccount::create($data);
        user_logs('Thêm tài khoản ngân hàng');
        return $this->sendResponse([
            'id'=> $group->id,
            'message' => 'Thêm tài khoản thành công!'
        ],201);
    }

    # [GET] /{id}  =>  Xem thông tin một bản ghi 
    public function show($id){
        try{
            $group = BankAccount::findOrFail($id);
            return $this->sendResponse($group);
        }catch(ModelNotFoundException $e){
            return $this->sendResponse([
                'message'=> 'Không tìm thấy ngân hàng'
            ],404);
        }
    }

    # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
    public function update($id,Request $request){
        $validate = $request->validate([
            'bank_number' => 'required',
            'full_name' => 'required',
            'bank_id' => 'required',
            'bank_account_type_id' => 'required',
        ],[], [
            'bank_number' => 'Số tài khoản',
            'full_name' => 'Tên tài khoản',
            'bank_id' => 'Ngân hàng',
            'bank_account_type_id' => 'Loại tài khoản',
        ]);
        if($validate->fails()){
            return $this->sendResponse($validate->errors(),422);
        }
        $account = BankAccount::where('bank_id',$request->bank_id)->where('id','!=',$id)->where('bank_number',$request->bank_number)->first();
        user_logs('Cập nhật tài khoản ngân hàng');
        if($account){
            return $this->sendResponse([
                'bank_number' => ['Tài khoản ngày đã tồn tại!']
            ],422);
        }
        try{
            $group = BankAccount::findOrFail($id);
            $group->update($request->all());
            return $this->sendResponse([
                'id'=> $group->id,
                'message'=> 'Cập nhật ngân hàng thành công'
            ]);
        }catch(ModelNotFoundException $e){
            return $this->sendResponse([
                'message'=> 'Không tìm thấy ngân hàng'
            ],404);
        }
    }
}
