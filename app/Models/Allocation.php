<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Allocation extends Eloquent{
        protected $table = 'allocations';
        public $timestamps = false;
        protected $guarded = [];

        public function details(){
            return $this->hasMany(CostAllocation::class,'allocation_id');
        }
        protected $casts = [
            'price_dn' => 'array',
            'price_trongthang' => 'array',
            'price_cuoinam' => 'array',
            'chuyenthangsau' => 'array',
            'tongchiphi' => 'array',
            'loinhuangop' => 'array',
            'lailo' => 'array',
        ];
    }
