<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class CustomerLog extends Eloquent{
        protected $table = 'customer_logs';

        protected $guarded = [];

        public $timestamps = false;

        public function user(){
            return $this->belongsTo(User::class);
        }

        public function customer(){
            return $this->belongsTo(Customer::class);
        }

        public function category(){
            return $this->belongsTo(Category::class,'nhu_cau');
        }

        public function from_status(){
            return $this->belongsTo(CustomerStatus::class,'from_status');
        }

        public function to_status(){
            return $this->belongsTo(CustomerStatus::class,'to_status');
        }
    }
