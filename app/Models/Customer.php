<?php 
    namespace App\Models;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Eloquent\Model as Eloquent;

    class Customer extends Eloquent{
        protected $table = 'customers';
        protected $guarded = [];

        public function logs(){
            return $this->hasMany(CustomerLog::class,'customer_id');
        }
        public function latestLog()
        {
            return $this->hasOne(CustomerLog::class, 'customer_id')->latest();
        }
        public function province(){
            return $this->belongsTo(Province::class,'province_id');
        }
        public function district(){
            return $this->belongsTo(District::class,'district_id');
        }
        public function ward(){
            return $this->belongsTo(Ward::class,'ward_id');
        }
        public function debt(){
            return $this->orders()
            ->groupBy('customer_id')
            ->sum(Manager::raw('du_kien - thuc_thu'));
        }
        public function user(){
            return $this->belongsTo(User::class);
        }
        public function getAreasAttribute() {
            $addressParts = array_filter([
                $this->address,
                optional($this->ward)->name,
                optional($this->district)->name,
                optional($this->province)->name
            ]);
            return implode(', ', $addressParts);
        }
        public function status(){
            return $this->belongsTo(CustomerStatus::class,'status_id');
        }
        public function category(){
            return $this->belongsTo(Category::class,'category_id');
        }
        public function orders(){
            return $this->hasMany(Order::class,'customer_id');
        }
        public function sale_channel(){
            return $this->belongsTo(SaleChannel::class,'sale_channel_id');
        }
    }
