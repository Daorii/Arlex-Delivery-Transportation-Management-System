<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportOrder extends Model
{
    protected $table = 'transport_orders';
    protected $primaryKey = 'to_id';
    
    // FIXED: Set to false since table doesn't have created_at/updated_at
    public $timestamps = false;
    
    protected $fillable = [
        'to_ref_no',
        'billing_id',
        'sipa_ref_no',
        'size',
        'quantity',
        'type',
        'total_amount',
        'depot_from',
        'depot_to',
        'verification_status',
        'verified_by',
        'verified_at',
        'is_archived'
    ];

    // Relationship to Billing
    public function billing()
    {
        return $this->belongsTo(Billing::class, 'billing_id', 'billing_id');
    }

    // Relationship to Invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'to_id', 'to_id');
    }

    // Check if this TO already has an invoice
    public function hasInvoice()
    {
        return $this->invoices()->exists();
    }

    // Scope for approved TOs
    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'Approved');
    }

    // Scope for TOs without invoices
    public function scopeWithoutInvoice($query)
    {
        return $query->whereDoesntHave('invoices');
    }
}