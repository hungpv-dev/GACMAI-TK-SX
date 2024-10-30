<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class Role extends Eloquent{
        protected $table = 'roles';

        protected $guarded = [];
    }
