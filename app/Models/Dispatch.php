<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $table = 'dispatches';
    protected $primaryKey = 'dispatch_id';
    public $timestamps = false;

    protected $fillable = [
        'sipa_id',
        'driver_id',
        'truck_id',
        'status',
        'is_archived'
    ];

    public function sipa()
    {
        return $this->belongsTo(Sipa::class, 'sipa_id', 'sipa_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id', 'driver_id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id', 'truck_id');
    }

    public function tripDetails()
    {
        return $this->hasMany(TripDetail::class, 'dispatch_id', 'dispatch_id');
    }
    
    public function commissions()
    {
        return $this->hasMany(DriverCommission::class, 'dispatch_id', 'dispatch_id');
    }


    
}