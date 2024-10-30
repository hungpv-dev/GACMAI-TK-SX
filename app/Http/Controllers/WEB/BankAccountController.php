<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\BankAccount;
use App\Models\TransactionType;
use App\Models\TypeBankAccount;
use AsfyCode\Facades\DB;
use AsfyCode\Utils\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BankAccountController extends Controller{
    public function index()
    {
        $banks = Bank::all();
        $bank_type = TypeBankAccount::all();
        return view("banks.accounts.index", compact('banks', 'bank_type'));
    }

    public function show($id)
    {
        try {
            $bankAccount = BankAccount::findOrFail($id);
            $transactionType = TransactionType::all();
            $biendongsodu15Day = $this->biendongsodu15Day($bankAccount);
            return view('banks.accounts.show', compact('biendongsodu15Day', 'bankAccount', 'transactionType'));
        } catch (ModelNotFoundException $e) {
            abort(404);
        }
    }

    public function biendongsodu15Day($bankAccount)
    {
        $data = collect();
        $startDate = date('Y-m-d', strtotime('-14 days'));

        for ($i = 0; $i <= 14; $i++) {
            $date = date('Y-m-d', strtotime("+$i days", strtotime($startDate)));
            $total = 0;
            $tran = DB::table("transactions")
                ->join('transactions_type', 'transactions_type.id', '=', 'transactions.transaction_type_id')
                ->select(
                    'transactions_type.type as tran_type',
                    'transactions.current_balance',
                    'transactions.amount',
                    'transactions.fee',
                    'transactions.created_at',
                    'transactions.bank_account_id',
                )
                ->where("bank_account_id", $bankAccount->id)
                ->whereDate('created_at', '<=', $date)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($tran) {
                $sum = 0;
                if ($tran->tran_type == 2) {
                    $sum = $tran->current_balance - $tran->amount - $tran->fee;
                } else {
                    $sum = $tran->current_balance + $tran->amount;
                }
            } else {
                $sum = $bankAccount->opening_balance;
            }
            $total += $sum;
            $data->push([
                'date' => $date,
                'total' => $total,
            ]);
        }
        return $data;
    }
}
