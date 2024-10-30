<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class BankAccount extends Eloquent{
        protected $table = 'bank_accounts';

        protected $guarded = [];

        public function bank(){
            return $this->belongsTo(Bank::class,'bank_id');
        }

        
        public function last_transaction(){
            return $this->hasOne(Transaction::class,'bank_account_id')->latest();
        }

        public function bank_account_type(){
            return $this->belongsTo(TypeBankAccount::class,'bank_account_type_id');
        }
    }
