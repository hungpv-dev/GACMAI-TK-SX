<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\PayRollDetail;
use App\Models\User;
use AsfyCode\Utils\Request;
use Google\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller{
    # [GET] /  =>  Danh sách dữ liệu 
    public function index(Request $request){
        $users = User::query();

        if($request->has('name')){
            $users->where('name','like','%'.$request->name.'%');
        }
        if($request->has('status')){
            $users->where('status',$request->status);
        }
        if($request->has('group')){
            $users->where('group_id',$request->group);
        }
        if($request->has('role_id')){
            $users->where('role_id',$request->role_id);
        }

        $users->with([
            'group',
            'factory',
            'role',
        ]);

        $users->orderBy('updated_at','desc');
        return $this->sendResponse($request->paginate($users));
    }

    # [POST] /create  =>  Thực thi thêm dữ liệu 
    public function store(Request $request){
        $validate = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'status' => 'required',
                'role_id' => 'required',
            ],
            [],
            [
            'name'=> 'Tên nhóm',
            'email' => 'Email',
            'status' => 'Trạng thái',
            'role_id' => 'Loại nhân sự'
        ]);
        
        if($validate->fails()){
            return $this->sendResponse($validate->errors(),422);
        }
        $data = $request->all();
        if($request->role_id == 7){
            $data['group_id'] = $data['factory_id'];
        }
        unset($data['factory_id']);
        $user = User::create($data);

        return $this->sendResponse([
            'id'=> $user->id,
            'message' => 'Thêm nhân sự thành công!'
        ],201);
    }

    # [GET] /{id}  =>  Xem thông tin một bản ghi 
    public function show($id){
        try{
            $user = User::findOrFail($id);
            return $this->sendResponse($user);
        }catch(ModelNotFoundException $e){
            return $this->sendResponse([
                'message'=> 'Không tìm thấy nhân sự'
            ],404);
        }
    }

    # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
    public function update($id,Request $request){
        $validate = $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,'.$id,
                'status' => 'required',
                'role_id' => 'required'
            ],
            [],
            [
            'name'=> 'Tên nhóm',
            'email' => 'Email',
            'status' => 'Trạng thái',
            'role_id' => 'Loại nhân sự'
        ]);
        if($validate->fails()){
            return $this->sendResponse($validate->errors(),422);
        }
        $data = $request->all();
        if($request->role_id == 7){
            $data['group_id'] = $data['factory_id'];
        }
        unset($data['factory_id']);
        try{
            $user = User::findOrFail($id);
            $user->update($data);
            return $this->sendResponse([
                'id'=> $user->id,
                'message'=> 'Cập nhật nhân sự thành công'
            ]);
        }catch(ModelNotFoundException $e){
            return $this->sendResponse([
                'message'=> 'Không tìm thấy nhân sự'
            ],404);
        }
    }

    public function totalPrice(Request $request)
    {
        $users = User::query();

        if($request->has('dates')){
            $finish = $request->dates;
        }else{
            $finish = currentMonth();
        }
        $dateSearch = getDateQuery($finish);
        $_GET['dates'] = $finish;

        if ($request->has('name')) {
            $users->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('status')) {
            $users->where('status', $request->status);
        }
        if ($request->has('group')) {
            $users->where('group_id', $request->group);
        }
        $users->with([
            'group',
            'latestLog'
        ]);
        $users->withSum(['orders' => function ($query) use ($dateSearch) {
            $query->whereBetween('finish_at', $dateSearch);
        }], 'thuc_thu');
        $users->withCount(['orders' => function ($query) use ($dateSearch) {
            $query->whereBetween('finish_at', $dateSearch);
        }], 'thuc_thu');
        $users->orderBy('orders_sum_thuc_thu', 'desc');
        $tongThu = (clone $users)->get()->sum('orders_sum_thuc_thu');
        return $this->sendResponse($request->paginate($users)->setAttribute('total_price', $tongThu));
    }
    

    public function loginCode(Request $request){
        $id = $request->id;
        try{
            $user = User::findOrFail($id);
            $login_code = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10/strlen($x)) )),1,10);
            $user->login_code = $login_code;
            $user->save();
            return $this->sendResponse([
                'link' => "http://kd.gacmai.vn/login-code/".$user->id."?code=".$login_code,
            ]);
        }catch(ModelNotFoundException $e){
            return $this->sendResponse([
                'message' => 'Không tìm thấy user',
            ]);
        }
    }
}

