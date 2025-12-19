<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoices';
    protected $primaryKey = 'invoice_id';
    
    // Disable automatic timestamps
    public $timestamps = false;
    
    protected $fillable = [
    'to_id',
    'invoice_no',
    'invoice_date',
    'due_date',
    'total_sales',
    'net_total',
    'voucher_no',
    'invoice_status',
    'is_archived'  // ← ADD THIS
];

    // Add this to make accessors available as attributes
    protected $appends = ['total_paid', 'remaining_balance'];

    // Relationship to Transport Order
    public function transportOrder()
    {
        return $this->belongsTo(TransportOrder::class, 'to_id', 'to_id');
    }

    // Relationship to Payments
    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id', 'invoice_id');
    }

    // Calculate total paid amount (only completed payments)
    public function getTotalPaidAttribute()
    {
        return $this->payments()
            ->where('payment_status', 'Completed')
            ->sum('payment_amount');
    }

    // Calculate remaining balance
    public function getRemainingBalanceAttribute()
    {
        return $this->net_total - $this->total_paid;
    }

    // Check if fully paid
    public function isFullyPaid()
    {
        return $this->remaining_balance <= 0;
    }

    // Auto-update invoice status based on payments
    public function updateInvoiceStatus()
    {
        $totalPaid = $this->total_paid;

        if ($totalPaid == 0) {
            $this->invoice_status = 'Sent';
        } elseif ($totalPaid < $this->net_total) {
            $this->invoice_status = 'Partially Paid';
        } else {
            $this->invoice_status = 'Fully Paid';
        }

        $this->save();
    }
}