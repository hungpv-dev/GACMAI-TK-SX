<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class RollDetailPriceUser extends Eloquent{
        protected $table = 'roll_detail_price_user';
        public $timestamps = false;
        protected $guarded = [];

        public function type(){
            return $this->belongsTo(PayRollType::class,'type_id');
        }
    }
