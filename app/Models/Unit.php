<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Unit extends Eloquent{
        protected $table = 'units';
        public $timestamps = false;

        protected $guarded = [];
    }
