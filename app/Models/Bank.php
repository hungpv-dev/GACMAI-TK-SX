<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Bank extends Eloquent{
        protected $table = 'banks';

        protected $guarded = [];
    }
