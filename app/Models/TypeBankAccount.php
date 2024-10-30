<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class TypeBankAccount extends Eloquent{
        protected $table = 'bank_account_type';

        public $timestamps = false;
        protected $guarded = [];
    }
