<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class CostAllocation extends Eloquent{
        protected $table = 'cost_allocation';
        public $timestamps = false;
        protected $guarded = [];

        public function tran_type(){
            return $this->belongsTo(TransactionType::class,'tran_type_id');
        }

        public function group(){
            return $this->belongsTo(Group::class,'group_id');
        }
    }
