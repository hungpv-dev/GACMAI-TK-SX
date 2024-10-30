<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use AsfyCode\Utils\Request;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierController extends Controller
{
    # [GET] /  =>  Danh sách dữ liệu 
    public function index(Request $request)
    {
        $suppliers = Supplier::query();

        if ($request->has('code')) {
            $suppliers->where('code', 'like', '%' . $request->code . '%');
        }
        if ($request->has('name')) {
            $suppliers->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->has('type')) {
            $suppliers->where('supplier_type_id', '=',$request->type);
        }

        $suppliers->with('type');
        $sum = (clone $suppliers)->sum('current_amount');
        // not_status
        return $this->sendResponse($request->paginate($suppliers)->setAttribute(compact('sum')));
    }

    # [POST] /create  =>  Thực thi thêm dữ liệu 
    public function store(Request $request)
    {
        $validate = $request->validate(
            [
                'code' => 'required|unique:suppliers,code',
                'name' => 'required',
                'opening_amount' => 'number',
                'supplier_type_id' => 'required'
            ],
            [],
            [
                'code' => 'Mã nhà cung cấp',
                'name' => 'Tên nhà cung cấp',
                'opening_amount' => 'Công nợ đầu kỳ',
                'supplier_type_id' => 'Loại nhà cung cấp'
            ]
        );
        if ($validate->fails()) {
            return $this->sendResponse($validate->errors(), 422);
        }
        try {
            Manager::beginTransaction();
            $dataCreate = [
                'name' => $request->name,
                'code' => $request->code,
                'note' => $request->note,
                'supplier_type_id' => $request->supplier_type_id,
                'opening_amount' => $request->opening_amount,
                'current_amount' => $request->opening_amount,
                'sum_price_product' => $request->opening_amount,
                'user_id' => user()->id
            ];
            $supplier = Supplier::create($dataCreate);
            Manager::commit();
        } catch (\Exception $e) {
            logError($e);
            Manager::rollBack();
            return $this->sendResponse([
                'message' => 'Có lỗi xảy ra, vui lòng thử lại sau!'
            ], 500);
        }
        user_logs('Thêm mới nhà cung cấp');
        return $this->sendResponse([
            'id' => $supplier->id,
            'message' => 'Thêm nhà cung cấp thành công!'
        ], 201);
    }

    # [GET] /{id}  =>  Xem thông tin một bản ghi 
    public function show($id)
    {
        try {
            $customer = Supplier::findOrFail($id);
            return $this->sendResponse($customer);
        } catch (ModelNotFoundException $e) {
            return $this->sendResponse([
                'message' => 'Không tìm thấy nhà cung cấp'
            ], 404);
        }
    }

    # [PUT] /update/{id}  =>  Hiển thị form cập nhật 
    public function update($id, Request $request)
    {
        $validate = $request->validate(
            [
                'code' => 'required|unique:suppliers,code,'.$id,
                'name' => 'required',
                'opening_amount' => 'number',
                'supplier_type_id' => 'required'
            ],
            [],
            [
                'code' => 'Mã nhà cung cấp',
                'name' => 'Tên nhà cung cấp',
                'opening_amount' => 'Công nợ đầu kỳ',
                'supplier_type_id' => 'Loại nhà cung cấp'
            ]
        );
        if ($validate->fails()) {
            return $this->sendResponse($validate->errors(), 422);
        }
        try {
            try {
                Manager::beginTransaction();
                $supplier = Supplier::findOrFail($id)->update($request->all());
                Manager::commit();
            } catch (\Exception $e) {
                logError($e);
                Manager::rollBack();
                return $this->sendResponse([
                    'message' => 'Có lỗi xảy ra, vui lòng thử lại sau!'
                ], 500);
            }
        } catch (ModelNotFoundException $e) {
            return $this->sendResponse([
                'message' => 'Không tìm thấy nhà cung cấp'
            ], 404);
        }
        user_logs('Cập nhật thông tin nhà cung cấp');
        return $this->sendResponse([
            'id' => $supplier->id,
            'message' => 'Cập nhật nhà cung cấp thành công!'
        ], 200);
    }
}
