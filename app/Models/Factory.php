<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Factory extends Eloquent{
        protected $table = 'factories';
        public $timestamps = false;

        protected $guarded = [];

        
    }
