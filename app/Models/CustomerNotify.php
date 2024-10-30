<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class CustomerNotify extends Eloquent{
        protected $table = 'customer_status_notification';
        public $timestamps = false;
        protected $guarded = [];

        public function group(){
            return $this->belongsTo(Group::class);
        }
        
        public function status(){
            return $this->belongsTo(CustomerStatus::class,'status_id');
        }
    }
