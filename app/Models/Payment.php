<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'payment_id';
    
    // Disable automatic timestamps
    public $timestamps = false;
    
    protected $fillable = [
    'invoice_id',
    'payment_ref_no',
    'payment_date',
    'payment_amount',
    'payment_method',
    'bank_name',
    'check_number',
    'transaction_ref_no',
    'payment_status',
    'remarks',
    'received_by',
    'is_archived'  // ← ADD THIS
];

    // Relationship to Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'invoice_id');
    }

    // Auto-update invoice status after payment is saved
    protected static function booted()
    {
        static::saved(function ($payment) {
            $payment->invoice->updateInvoiceStatus();
        });

        static::deleted(function ($payment) {
            $payment->invoice->updateInvoiceStatus();
        });
    }
}