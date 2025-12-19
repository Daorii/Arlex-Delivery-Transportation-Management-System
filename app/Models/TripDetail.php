<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripDetail extends Model
{
    protected $table = 'tripdetails';
    protected $primaryKey = 'detail_id';
    public $timestamps = false;

    protected $fillable = [
        'dispatch_id',
        'sipa_detail_id',
        'container_no',
        'eir_no',
        'delivery_date',
        'is_verified',
        'verified_by',
        'verified_at'
        
    ];

    // Set default attributes
    protected $attributes = [
        'is_verified' => 0,  // Default to pending (0)
    ];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class, 'dispatch_id', 'dispatch_id');
    }

    public function sipaDetail()
    {
        return $this->belongsTo(Sipadetail::class, 'sipa_detail_id', 'sipa_detail_id');
    }

    
}