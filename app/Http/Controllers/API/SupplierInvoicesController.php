<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Supplier;
use App\Models\SupplierInvoices;
use App\Models\Transaction;
use AsfyCode\Utils\Request;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierInvoicesController extends Controller{
    public function index(Request $request){
        $invoices = SupplierInvoices::query();
        if($request->has('supplier_id')){
            $invoices->where('supplier_id','=',$request->supplier_id);
        }
        if($request->has('dates')){
            $query = getDateQuery($request->dates);
            $invoices->whereBetween('created_at',$query);
        }
        if($request->has('note')){
            $invoices->where('note','like','%'.$request->note.'%');
        }
        if($request->has('type')){
            $invoices->where('type','=',$request->type);
        }
        if($request->has('amount_min')){
            $min = $request->input('amount_min');
            if($min && $min != ''){
                $invoices->where('amount','>=',$request->amount_min);
            }
        }
        if($request->has('amount_max')){
            $max = $request->input('amount_max');
            if($max && $max != ''){
                $invoices->where('amount','<=',$request->amount_max);
            }
        }
        $invoices->with(['user','tran_type']);
        // $invoices->orderBy('id','desc');
        $response = $request->paginate($invoices);
        return $this->sendResponse($response);
    }

    public function addCongNo(Request $request,$id){
        $dataCreate = $request->all();
        try{
            Manager::beginTransaction();
            $supplier = Supplier::findOrFail($id);
            $dataCreate['current_amount'] = $supplier->current_amount;
            $amount = $dataCreate['amount'];
            $currentAmount = $supplier->current_amount + $amount;
            $currentTotal = $supplier->sum_price_product + $amount;
            $supplier->sum_price_product = $currentTotal;
            $supplier->current_amount = $currentAmount;
            $supplier->save();
            $dataCreate['user_id'] = user()->id;
            $dataCreate['type'] = 16;
            $dataCreate['created_at'] = now();
            $dataCreate['supplier_id'] = $supplier->id;
            $data = SupplierInvoices::create($dataCreate);
            Manager::commit();
        }catch(ModelNotFoundException $e){
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
        user_logs('Thêm công nợ nhà cung cấp');
        return $this->sendResponse([
            'id' => $data->id,
            'message' => 'Tăng công nợ nhà cung cấp thành công',
        ],201);
    }

    public function thanhToanCongNo(Request $request,$id){
        $amount = $request->input('amount');
        $fee = $request->input('fee');
        $bank_account_id = $request->input('bank_account');
        $note = $request->input('note');

        $dataCreateSupplierInvoice = [
            'note' => $note,
            'amount' => $amount,
            'supplier_id' => $id,
            'user_id' => user()->id,
            'type' => 7,
            'created_at' => now(),
        ];
        $dataCreateTransaction = [
            'note' => $note,
            'fee' => $fee,
            'amount' => $amount,
            'bank_account_id' => $bank_account_id,
            'transaction_type_id' => 7,
            'user_id' => user()->id,
            'user_duyet' => user()->id,
            'created_at' => now()
        ];
        try{
            $totalPrice = $amount + $fee;
            $bankAccount = BankAccount::findOrFail($bank_account_id);
            if($bankAccount->current_balance < $totalPrice){
                return $this->sendResponse([
                    'amount' => ['Số tiền trong tài khoản này không đủ để thanh toán công nợ!'],
                ],422);
            }
            Manager::beginTransaction();
            $dataCreateTransaction['current_balance'] = $bankAccount->current_balance;
            $currAmou = $bankAccount->current_balance - $totalPrice;
            $bankAccount->current_balance = $currAmou;
            $bankAccount->save();

            $tran = Transaction::create($dataCreateTransaction);
            $dataCreateSupplierInvoice['transaction_id'] = $tran->id;

            $supplier = Supplier::findOrFail($id);
            $dataCreateSupplierInvoice['current_amount'] = $supplier->current_amount;
            $currentAmount = $supplier->current_amount - $amount;
            $supplier->current_amount = $currentAmount;
            $supplier->save();
            $invoi = SupplierInvoices::create($dataCreateSupplierInvoice);

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
        user_logs('Thanh toán công nợ nhà cung cấp');
        return $this->sendResponse([
            'id' => $invoi->id,
            'message' => 'Thanh toán công nợ nhà cung cấp thành công',
        ],201);
    }
}
