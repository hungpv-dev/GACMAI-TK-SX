<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class OrderExpenses extends Eloquent{
        protected $table = 'order_expenses';

        public $timestamps = false;
        protected $guarded = [];

        public function type(){
            return $this->belongsTo(TransactionType::class,'tran_type_id');
        }

        public function user(){
            return $this->belongsTo(User::class,'user_id');
        }
    }
