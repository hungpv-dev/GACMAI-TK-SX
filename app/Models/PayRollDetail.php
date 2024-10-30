<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class PayRollDetail extends Eloquent{
        protected $table = 'pay_roll_detail';
        public $timestamps = false;
        protected $guarded = [];

        public function user(){
            return $this->belongsTo(User::class);
        }
    }
