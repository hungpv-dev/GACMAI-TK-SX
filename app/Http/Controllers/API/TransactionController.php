<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Http\Controllers\OrderLogController;
use App\Models\BankAccount;
use App\Models\Order;
use App\Models\OrderLogs;
use App\Models\Supplier;
use App\Models\SupplierInvoices;
use App\Models\Transaction;
use App\Models\TransactionType;
use AsfyCode\Utils\Request;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TransactionController extends Controller{
    public function index(Request $request){
        $transactions = Transaction::query();
        if($request->has('bank_account_id')){
            $transactions->where('bank_account_id', $request->input('bank_account_id'));
        }
        if($request->has('type')){
            $transactions->where('transaction_type_id', $request->input('type'));
        }
        
        if($request->has('transaction_type')){
            $type = $request->transaction_type;
            $transactions->whereHas('type',function($query) use ($type){
                $query->where('type', $type);
            });
        }
        if($request->has('created_at')){
            $dateSearch = getDateQuery($request->input('created_at'));
            $transactions->whereBetween('created_at', $dateSearch);
        }
        if($request->has('amount_min')){
            $min = $request->input('amount_min');
            if($min && $min != ''){
                $transactions->where('amount','>=',$request->amount_min);
            }
        }
        if($request->has('note')){
            $transactions->where('note','like','%'.$request->note.'%');
        }
        if($request->has('amount_max')){
            $max = $request->input('amount_max');
            if($max && $max != ''){
                $transactions->where('amount','<=',$request->amount_max);
            }
        }
        if($request->has('order')){
            $transactions->orderBy('id','asc');
        }else{
            $transactions->orderBy('id','desc');
        }
        

        $transactions->with([
            'user',
            'customer',
            'type',
            'supplier',
            'acc_nhan',
            'acc_chuyen',
            'bankAccount',
            'bankAccount.bank'
        ]);
        $response = $request->paginate($transactions);
        return $this->sendResponse($response);
    }
    
    public function store(Request $request){

        $amount = $request->input('amount');
        $fee = $request->input('fee');
        $bank_account_id = $request->input('bank_account');
        $note = $request->input('note');
        $type = $request->input('transaction_type_id');

        $dataCreateTransaction = [
            'note' => $note,
            'fee' => $fee,
            'amount' => $amount,
            'bank_account_id' => $bank_account_id,
            'transaction_type_id' => $type,
            'user_id' => user()->id,
            'user_duyet' => user()->id,
            'created_at' => now()
        ];
        try{
            $totalPrice = $amount + $fee;
            $bankAccount = BankAccount::findOrFail($bank_account_id);
            $transactionType = TransactionType::findOrFail($type);
            if($transactionType->type == 2){
                if($bankAccount->current_balance < $totalPrice){
                    return $this->sendResponse([
                        'amount' => ['Số tiền trong tài khoản này không đủ để thanh toán công nợ!'],
                    ],422);
                }
            }

           
            Manager::beginTransaction();
            $dataCreateTransaction['current_balance'] = $bankAccount->current_balance;
            if($transactionType->type == 2){
                $currAmou = $bankAccount->current_balance - $totalPrice;
            }else{
                $currAmou = $bankAccount->current_balance + $totalPrice;
            }
            $bankAccount->current_balance = $currAmou;
            $bankAccount->save();

            $tran = Transaction::create($dataCreateTransaction);
            if($request->has('supplier_id') && $request->supplier_id != ''){
                $supplier_id = $request->supplier_id;
                $dataCreateSupplierInvoice = [
                    'note' => $note,
                    'amount' => $amount,
                    'supplier_id' => $supplier_id,
                    'user_id' => user()->id,
                    'type' => $type,
                    'created_at' => now(),
                ];
                $dataCreateSupplierInvoice['transaction_id'] = $tran->id;
                $supplier = Supplier::findOrFail($supplier_id);
                $dataCreateSupplierInvoice['current_amount'] = $supplier->current_amount;
                $currentAmount = $supplier->current_amount - $amount;
                $supplier->current_amount = $currentAmount;
                $supplier->save();
                SupplierInvoices::create($dataCreateSupplierInvoice);
            }
            if($request->has('order_id') && $request->order_id != ''){
                $order_id = $request->order_id;
                OrderLogs::create([
                    'order_id' => $order_id,
                    'status' => 2,
                    'amount' => $amount,
                    'bank_account_id' => $bank_account_id,
                    'user_id'=> user()->id,
                    'note' => $note
                ]);
                $order = Order::findOrFail($order_id);
                $tran->order_id = $order->id;
                $tran->customer_id = $order->customer_id;
                $tran->save();
                $order->thuc_thu = $order->thuc_thu + $amount;
                $order->save();
                $orderCheck = new OrderLogController();
                $orderCheck->checkOrder($order);
            }

            Manager::commit();
        }catch(ModelNotFoundException $e){
            Manager::rollBack();
            return $this->sendResponse([
                'message' => 'Không tìm thấy nhà cung cấp',
                'error' => $e->getMessage()
            ],404);
        }catch(\Exception $e){
            Manager::rollBack();
            return $this->sendResponse([
                'message' => 'Đã có lỗi xảy ra, vui lòng thử lại sau',
                'error' => $e->getMessage()
            ],500);
        }
        user_logs('Thanh toán chi phí!');
        return $this->sendResponse([
            'id' => $tran->id,
            'message' => 'Thanh toán chi phí thành công!',
        ],201);
    }
}
