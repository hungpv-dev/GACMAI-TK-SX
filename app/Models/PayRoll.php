<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class PayRoll extends Eloquent{
        protected $table = 'payrolls';
        public $timestamps = false;
        protected $guarded = [];

        public function details(){
            return $this->hasMany(PayRollDetail::class,'payroll_id');
        }
    }
