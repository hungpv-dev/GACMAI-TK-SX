<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class SupplierInvoices extends Eloquent
{
    protected $table = 'supplier_invoices';
    public $timestamps = false;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tran_type() {
        return $this->belongsTo(TransactionType::class,'type');
    }
}
