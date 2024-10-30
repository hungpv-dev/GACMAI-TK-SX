<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\SaleChannel;
use AsfyCode\Utils\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SaleChannelController extends Controller{
# [GET] /  =>  Danh sách dữ liệu 
public function index(Request $request){
    $groups = SaleChannel::query();

    if($request->has('name')){
        $groups->where('name','like','%'.$request->name.'%');
    }

    return $this->sendResponse($request->paginate($groups));
}

# [POST] /create  =>  Thực thi thêm dữ liệu 
public function store(Request $request){
    $validate = $request->validate(
    ['name' => 'required|unique:sale_channel,name']
    ,[], [
        'name'=> 'Tên kênh',
    ]);
    if($validate->fails()){
        return $this->sendResponse($validate->errors(),422);
    }
    $group = SaleChannel::create($request->all());
    return $this->sendResponse([
        'id'=> $group->id,
        'message' => 'Thêm kênh kinh doanh thành công!'
    ],201);
}

# [GET] /{id}  =>  Xem thông tin một bản ghi 
public function show($id){
    try{
        $group = SaleChannel::findOrFail($id);
        return $this->sendResponse($group);
    }catch(ModelNotFoundException $e){
        return $this->sendResponse([
            'message'=> 'Không tìm thấy kênh kinh doanh'
        ],404);
    }
}

# [PUT] /update/{id}  =>  Hiển thị form cập nhật 
public function update($id,Request $request){
    $validate = $request->validate(['name' => 'required|unique:sale_channel,name,'.$id],[], [
        'name'=> 'Tên kênh',
    ]);
    if($validate->fails()){
        return $this->sendResponse($validate->errors(),422);
    }
    try{
        $group = SaleChannel::findOrFail($id);
        $group->update($request->all());
        return $this->sendResponse([
            'id'=> $group->id,
            'message'=> 'Cập nhật kênh kinh doanh thành công'
        ]);
    }catch(ModelNotFoundException $e){
        return $this->sendResponse([
            'message'=> 'Không tìm thấy kênh kinh doanh'
        ],404);
    }
}
}
