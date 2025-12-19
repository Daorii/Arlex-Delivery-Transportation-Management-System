<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverCommission extends Model
{
    protected $table = 'drivercommissions'; // matches your DB table
    protected $primaryKey = 'commission_id';
    public $timestamps = false;

    protected $fillable = [
        'driver_id',
        'dispatch_id',
        'total_trip_amount',
        'commission_rate',
        'commission_amount',
        'week_period_text',
        'status',
        'paid_at'
    ];

    // Relationship to Driver
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'driver_id');
    }
        public function dispatch()
    {
        return $this->belongsTo(Dispatch::class, 'dispatch_id', 'dispatch_id');
    }
}
