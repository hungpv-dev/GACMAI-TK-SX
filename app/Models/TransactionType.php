<?php 
    namespace App\Models;
    use Illuminate\Database\Eloquent\Model as Eloquent;

    class TransactionType extends Eloquent{
        protected $table = 'transactions_type';
        public $timestamps = false;

        protected $guarded = [];
    }
