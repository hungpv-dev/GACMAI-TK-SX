<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Order extends Eloquent{
        protected $table = 'orders';
        protected $guarded = [];

        public function customer(){
            return $this->belongsTo(Customer::class);
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
        public function expenses(){
            return $this->hasMany(OrderExpenses::class);
        }
        public function ward(){
            return $this->belongsTo(Ward::class,'ward_id');
        }
        public function category(){
            return $this->belongsTo(Category::class,'category_id');
        }
        
        public function district(){
            return $this->belongsTo(District::class,'district_id');
        }
        public function province(){
            return $this->belongsTo(Province::class,'province_id');
        }
        public function user_tk(){
            return $this->belongsTo(User::class,'rela');
        }
        public function order_logs() {
            return $this->hasMany(OrderLogs::class, 'order_id', 'id');
        }
        public function price_pending() {
            return $this->order_logs()->where('status','=',1)->sum('amount');
        }
        public function getPricePendingAttribute() {
            return $this->price_pending();
        }
        public function user(){
            return $this->belongsTo(User::class);
        }
        public function city(){
            return $this->belongsTo(Province::class);
        }
        public function status(){
            return $this->belongsTo(OrderStatus::class,'status_id');
        }
        public function current_status(){
            return $this->belongsTo(OrderStatus::class,'current_status');
        }
        public function file_latest(){
            return $this->hasOne(OrderFile::class,'order_id','id')->latest();
        }
        public function updateLogLastest(){
            return $this->hasOne(OrderUpdateLog::class,'order_id','id')->latest();
        }
    }
