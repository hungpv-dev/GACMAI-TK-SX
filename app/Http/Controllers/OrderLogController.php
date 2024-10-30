<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderLogs;
use App\Models\OrderStatus;
use App\Models\Transaction;
use AsfyCode\Utils\Request;
use Illuminate\Database\Capsule\Manager;

class OrderLogController extends Controller
{
    public function index(Request $request)
    {
        $orderLogs = OrderLogs::query();
        if ($request->has('order_id')) {
            $orderLogs->where('order_id', $request->order_id);
        }
        if ($request->has('bank_account_id')) {
            $orderLogs->where('bank_account_id', $request->bank_account_id);
        }
        if ($request->has('customer_id')) {
            $orderLogs->whereHas('order', function ($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            });
        }
        if ($request->has('status')) {
            $orderLogs->where('status', $request->status);
        }
        
        if ($request->has('name')) {
            $orderLogs->whereHas('order.customer', function ($query) use ($request) {
                $query->where('name','like','%'.$request->name.'%');
            });
        }
        if ($request->has('phone')) {
            $orderLogs->whereHas('order.customer', function ($query) use ($request) {
                $query->where('phone','like','%'.$request->phone.'%');
            });
        }
        if ($request->has('user_id')) {
            $orderLogs->where('user_id', $request->user_id);
        }
        if ($request->has('group_id')) {
            $orderLogs->whereHas('user', function ($query) use ($request) {
                $query->where('group_id', $request->group_id);
            });
        }
        if(!$request->has('searchnone')){
            $dates = currentMonth();
            if ($request->has('dates')) {
                $dates = $request->dates;
            } else {
                $request->merge('dates', $dates);
            }
            $dateQuery = getDateQuery($dates);
            $orderLogs->whereBetween('created_at', $dateQuery);
        }

        $orderLogs->with('order:id,customer_id');
        $orderLogs->with('order.customer:id,name');
        $orderLogs->with('user:id,name,group_id');
        $orderLogs->with('user.group:id,name');
        $orderLogs->with('bankAccount');
        $orderLogs->with('bankAccount.bank');
        if ($request->has('orderby')) {
            $orderLogs->orderBy('created_at', 'asc');
        } else {
            $orderLogs->orderBy('created_at', 'desc');
        }

        $chuaDuyet = (clone $orderLogs)->where('status', 1)->sum('amount');
        $tongTien = (clone $orderLogs)->where('status', 2)->sum('amount');
        $response = $request
            ->paginate($orderLogs, $request->input('limit', 50))
            ->setAttribute(compact('chuaDuyet', 'tongTien'));
        return $this->sendResponse($response);
    }

    public function store(Request $request)
    {
        $validate = $request->validate(
            [
                'bank_account_id' => 'required',
                'amount' => 'required',
            ],
            [],
            [
                'bank_account_id' => 'Tài khoản nhận',
                'amount' => 'Số tiền',
            ]
        );
        if ($validate->fails()) {
            return $this->sendResponse($validate->errors(), 422);
        }
        $data = [
            'bank_account_id' => $request->bank_account_id,
            'amount' => $request->amount,
            'note' => $request->note,
            'order_id' => $request->order_id,
            'user_id' => user()->id,
        ];
        if ($request->amount >= 0) {
            $data['status'] = 2;
        } else {
            $data['status'] = 1;
        }
        try {
            Manager::beginTransaction();
            $orderLog = OrderLogs::create($data);
            $this->afterStore($orderLog);
            Manager::commit();
        } catch (\Exception $e) {
            Manager::rollBack();
            return $this->sendResponse([
                'message' => 'Đã có lỗi xảy ra vui lòng thử lại sau!',
                'error' => $e->getMessage(),
            ], 500);
        }
        return $this->sendResponse([
            'id' => $orderLog->id,
            'message' => 'Thêm thanh toán thành công!'
        ], 201);
    }

    public function afterStore(OrderLogs $orderLog)
    {
        $order = $orderLog->order;
        $bankAccount = $orderLog->bankAccount;
        if ($orderLog->status == 2) {
            $transaction = new Transaction();
            $transaction->bank_account_id = $orderLog->bank_account_id;
            $transaction->amount = $orderLog->amount;
            $transaction->customer_id = $order->customer_id;
            $transaction->order_id = $order->id;
            $transaction->user_duyet = user()->id;
            $transaction->note = $orderLog->note;
            $transaction->user_id = $orderLog->user_id;
            $transaction->current_balance = $bankAccount->current_balance;
            $transaction->created_at = now();
            $transaction->save();

            $order->thuc_thu = $order->thuc_thu + $orderLog->amount;
            $order->save();

            $bankAccount->current_balance = $bankAccount->current_balance + $orderLog->amount;
            $bankAccount->save();
        }
        $this->checkOrder($order);
        
    }
    public function checkOrder(Order $order){
        if($order->thuc_thu >= $order->du_kien) {
            $order->status_id = status('order_success');
            $customer = Customer::findOrFail($order->customer_id);
            $order->finish_at = now();
            $customer->update([
                'status_id' => status('customer_success'),
                'schedule' => NULL,
            ]);
            $order->save();
        }
    }
}
