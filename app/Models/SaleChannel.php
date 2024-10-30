<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class SaleChannel extends Eloquent{
        protected $table = 'sale_channel';

        public $timestamps = false;
        protected $guarded = [];
    }
