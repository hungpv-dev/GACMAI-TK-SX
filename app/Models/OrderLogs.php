<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class OrderLogs extends Eloquent{
        protected $table = 'order_logs';

        protected $guarded = [];

        public function user(){ 
            return $this->belongsTo(User::class,'user_id','id');
        }

        public function bankAccount(){
            return $this->belongsTo(BankAccount::class,'bank_account_id','id');
        }

        public function order(){
            return $this->belongsTo(Order::class,'order_id','id');
        }
    }