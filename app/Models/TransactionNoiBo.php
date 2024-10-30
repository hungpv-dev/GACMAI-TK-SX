<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class TransactionNoiBo extends Eloquent{
        protected $table = 'tran_noibo';

        protected $guarded = [];

        public $timestamps = false;

        public function from(){
            return $this->belongsTo(BankAccount::class,'from_account');
        }
        
        public function to(){
            return $this->belongsTo(BankAccount::class,'to_account');
        }
        public function user(){
            return $this->belongsTo(User::class,'user_id');
        }
    }
