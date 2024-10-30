<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Group;
use AsfyCode\Utils\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GroupController extends Controller{

    # [GET] /  =>  Danh sách dữ liệu 
    public function index(Request $request){
        $groups = Group::query();

        if($request->has('name')){
            $groups->where('name','like','%'.$request->name.'%');
        }

        $groups->withCount('users');

        return $this->sendResponse($request->paginate($groups));
    }

    # [POST] /create  =>  Thực thi thêm dữ liệu 
    public function store(Request $request){
        $validate = $request->validate(
        ['name' => 'required|unique:groups,name']
        ,[], [
            'name'=> 'Tên nhóm',
        ]);
        if($validate->fails()){
            return $this->sendResponse($validate->errors(),422);
        }
        $group = Group::create($request->all());
        return $this->sendResponse([
            'id'=> $group->id,
            'message' => 'Thêm nhóm nhân sự thành công!'
        ],201);
    }

    # [GET] /{id}  =>  Xem thông tin một bản ghi 
    public function show($id){
        try{
            $group = Group::findOrFail($id);
            return $this->sendResponse($group);
        }catch(ModelNotFoundException $e){
            return $this->sendResponse([
                'message'=> 'Không tìm thấy nhóm nhân sự'
            ],404);
        }
    }

    # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
    public function update($id,Request $request){
        $validate = $request->validate(['name' => 'required|unique:groups,name,'.$id],[], [
            'name'=> 'Tên nhóm',
        ]);
        if($validate->fails()){
            return $this->sendResponse($validate->errors(),422);
        }
        try{
            $group = Group::findOrFail($id);
            $group->update($request->all());
            return $this->sendResponse([
                'id'=> $group->id,
                'message'=> 'Cập nhật nhóm nhân sự thành công'
            ]);
        }catch(ModelNotFoundException $e){
            return $this->sendResponse([
                'message'=> 'Không tìm thấy nhóm nhân sự'
            ],404);
        }
    }

}
