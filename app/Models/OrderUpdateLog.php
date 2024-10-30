<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class OrderUpdateLog extends Eloquent{
        protected $table = 'order_update_logs';
        public $timestamps = false;

        protected $guarded = [];

        public function user(){
            return $this->belongsTo(User::class,'user_id','id');
        }

        public function status_to(){
            return $this->belongsTo(OrderStatus::class,'status_to','id');
        }

        public function status_from(){
            return $this->belongsTo(OrderStatus::class,'status_from','id');
        }
    }
