<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class UserActiveLog extends Eloquent{
        protected $table = 'user_active_log';
        public $timestamps = false;
        protected $guarded = [];
    }
