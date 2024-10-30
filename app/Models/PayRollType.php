<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class PayRollType extends Eloquent{
        protected $table = 'pay_roll_price_type';

        public $timestamps = false;
        protected $guarded = [];
    }
