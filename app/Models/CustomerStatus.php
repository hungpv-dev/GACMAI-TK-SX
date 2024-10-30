<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class CustomerStatus extends Eloquent{
        protected $table = 'customer_status';
        protected $guarded = [];
        public $timestamps = false;
    }
