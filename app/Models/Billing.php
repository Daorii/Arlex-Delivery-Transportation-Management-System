<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'billings';
    protected $primaryKey = 'billing_id';
    public $timestamps = false;  // ← CHANGE THIS TO false
    
    protected $fillable = [
        'client_id',
        'sipa_id',
        'sipa_ref_no',
        'week_period_text',
        'prepared_by',
        'checked_by',
        'total_amount',
        'status',
        'is_archived'  // ← ADD THIS
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}