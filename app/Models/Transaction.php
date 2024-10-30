<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Transaction extends Eloquent{
        protected $table = 'transactions';

        public $timestamps = false;

        protected $guarded = [];

        public function user(){
            return $this->belongsTo(User::class);
        }

        public function bankAccount(){
            return $this->belongsTo(BankAccount::class,'bank_account_id');
        }

        
        public function customer(){
            return $this->belongsTo(Customer::class);
        }

        public function type(){
            return $this->belongsTo(TransactionType::class,'transaction_type_id');
        }

        public function supplier(){
            return $this->hasOneThrough(Supplier::class, SupplierInvoices::class, 'transaction_id', 'id', 'id', 'supplier_id');
        }

        public function acc_chuyen(){
            return $this->hasOneThrough(BankAccount::class, TransactionNoiBo::class, 'id', 'id', 'tran_noibo_id', 'from_account');
        }
        public function acc_nhan(){
            return $this->hasOneThrough(BankAccount::class, TransactionNoiBo::class, 'id', 'id', 'tran_noibo_id', 'to_account');
        }
    }
