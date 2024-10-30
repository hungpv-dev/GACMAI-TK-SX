<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class OrderStatus extends Eloquent{
        protected $table = 'order_status';
        public $timestamps = false;
        protected $guarded = [];
    }
