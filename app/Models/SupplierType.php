<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class SupplierType extends Eloquent{
        protected $table = 'supplier_type';

        public $timestamps = false;
        protected $guarded = [];
    }
