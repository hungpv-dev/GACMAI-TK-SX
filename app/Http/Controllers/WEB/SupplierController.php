<?php
namespace App\Http\Controllers\WEB;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Supplier;
use App\Models\SupplierType;
use AsfyCode\Utils\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SupplierController extends Controller{
    public function index(){
        $types = SupplierType::all();
        return view('suppliers.index',compact('types'));
    }

    public function show($id){
        try{
            $bankAccounts = BankAccount::all();
            $supplier = Supplier::findOrFail($id);
            return view('suppliers.show',compact('supplier','bankAccounts'));
        }catch(ModelNotFoundException $e){
            abort(404);
        }
    }
}
