<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Category;
use AsfyCode\Utils\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryController extends Controller{
    # [GET] /  =>  Danh sách dữ liệu 
    public function index(Request $request){
        $groups = Category::query();

        if($request->has('name')){
            $groups->where('name','like','%'.$request->name.'%');
        }
        $groups->with('unit');
        $groups->orderBy('id','desc');
        return $this->sendResponse($request->paginate($groups));
    }

    # [POST] /create  =>  Thực thi thêm dữ liệu 
    public function store(Request $request){
        $validate = $request->validate(
        ['name' => 'required|unique:categories,name']
        ,[], [
            'name'=> 'Tên sản phẩm',
            'unit_id'=> 'Đơn vị tính',
        ]);
        if($validate->fails()){
            return $this->sendResponse($validate->errors(),422);
        }
        $group = Category::create($request->all());
        return $this->sendResponse([
            'id'=> $group->id,
            'message' => 'Thêm sản phẩm thành công!'
        ],201);
    }

    # [GET] /{id}  =>  Xem thông tin một bản ghi 
    public function show($id){
        try{
            $group = Category::findOrFail($id);
            return $this->sendResponse($group);
        }catch(ModelNotFoundException $e){
            return $this->sendResponse([
                'message'=> 'Không tìm thấy sản phẩm'
            ],404);
        }
    }

    # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
    public function update($id,Request $request){
        $validate = $request->validate(['name' => 'required|unique:categories,name,'.$id],[], [
            'name'=> 'Tên sản phẩm',
            'unit_id'=> 'Đơn vị tính',
        ]);
        if($validate->fails()){
            return $this->sendResponse($validate->errors(),422);
        }
        try{
            $group = Category::findOrFail($id);
            $group->update($request->all());
            return $this->sendResponse([
                'id'=> $group->id,
                'message'=> 'Cập nhật sản phẩm thành công'
            ]);
        }catch(ModelNotFoundException $e){
            return $this->sendResponse([
                'message'=> 'Không tìm thấy sản phẩm'
            ],404);
        }
    }
}
