<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\Unit;
use AsfyCode\Utils\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UnitController extends Controller{
 # [GET] /  =>  Danh sách dữ liệu 
 public function index(Request $request)
 {
     $factory = Unit::query();

     if ($request->has('name')) {
         $factory->where('name', 'like', '%' . $request->name . '%');
     }

     return $this->sendResponse($request->paginate($factory));
 }

 # [POST] /create  =>  Thực thi thêm dữ liệu 
 public function store(Request $request)
 {
     $validate = $request->validate(
         ['name' => 'required|unique:units,name'],
         [],
         [
             'name' => 'Tên đơn vị',
         ]
     );
     if ($validate->fails()) {
         return $this->sendResponse($validate->errors(), 422);
     }
     $data = $request->all();
     $group = Unit::create($data);
     return $this->sendResponse([
         'id' => $group->id,
         'message' => 'Thêm đơn vị thành công!'
     ], 201);
 }

 # [GET] /{id}  =>  Xem thông tin một bản ghi 
 public function show($id)
 {
     try {
         $group = Unit::findOrFail($id);
         return $this->sendResponse($group);
     } catch (ModelNotFoundException $e) {
         return $this->sendResponse([
             'message' => 'Không tìm thấy đơn vị'
         ], 404);
     }
 }

 # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
 public function update($id, Request $request)
 {
     $validate = $request->validate(['name' => 'required|unique:units,name,' . $id], [], [
         'name' => 'Tên đơn vị',
     ]);
     if ($validate->fails()) {
         return $this->sendResponse($validate->errors(), 422);
     }
     $data = $request->all();
     try {
         $group = Unit::findOrFail($id);
         $group->update($data);
         return $this->sendResponse([
             'id' => $group->id,
             'message' => 'Cập nhật đơn vị thành công'
         ]);
     } catch (ModelNotFoundException $e) {
         return $this->sendResponse([
             'message' => 'Không tìm thấy đơn vị'
         ], 404);
     }
 }
}
