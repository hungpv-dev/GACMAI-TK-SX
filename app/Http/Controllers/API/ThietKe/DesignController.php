<?php
namespace App\Http\Controllers\API\ThietKe;
use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use AsfyCode\Utils\Request;

class DesignController extends Controller{
    public function status(Request $request)
    {
        $listIds = [];
        foreach ($request->all() as $value) {
            if ($value['id']) {
                $status = OrderStatus::where('type_data',2)->find($value['id']);
            } else {
                $status = new OrderStatus();
            }
            $status->name = $value['name'];
            if ($value['type'] && $value['type'] != 'null') {
                $status->type = $value['type'];
            }
            $status->sort = $value['sort'];
            $status->color = $value['color'];
            $status->bg = $value['bg'];
            $status->type_data = 2;
            $status->save();
            $listIds[] = $status->id;
        }
        OrderStatus::whereNotIn('id', $listIds)->where('type_data',2)->delete();
        return $this->sendResponse([
            'data' => $request->all(),
            'message' => 'Cập nhật trạng thái thành công!'
        ], 200);
    }

    public function statusXuong(Request $request)
    {
        $listIds = [];
        foreach ($request->all() as $value) {
            if ($value['id']) {
                $status = OrderStatus::where('type_data',3)->find($value['id']);
            } else {
                $status = new OrderStatus();
            }
            $status->name = $value['name'];
            if ($value['type'] && $value['type'] != 'null') {
                $status->type = $value['type'];
            }
            $status->sort = $value['sort'];
            $status->color = $value['color'];
            $status->bg = $value['bg'];
            $status->type_data = 3;
            $status->save();
            $listIds[] = $status->id;
        }
        OrderStatus::whereNotIn('id', $listIds)->where('type_data',3)->delete();
        return $this->sendResponse([
            'data' => $request->all(),
            'message' => 'Cập nhật trạng thái thành công!'
        ], 200);
    }
}
