<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Category extends Eloquent{
        protected $table = 'categories';

        public $timestamps = false;
        protected $guarded = [];

        public function unit(){
            return $this->belongsTo(Unit::class);
        }
    }
