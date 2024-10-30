<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Supplier extends Eloquent{
        protected $table = 'suppliers';

        protected $guarded = [];

        public function type(){
            return $this->belongsTo(SupplierType::class,'supplier_type_id');
        }

        public function user(){
            return $this->belongsTo(User::class,'user_id');
        }

        public function supplier_invoices(){
            return $this->hasMany(SupplierInvoices::class,'supplier_id');
        }
        public function supplier_invoices_last(){
            return $this->supplier_invoices()->latest();
        }
    }
