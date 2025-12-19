<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DriverCommission;
use App\Models\Dispatch;


class Driver extends Model
{
    protected $table = 'drivers';
    protected $primaryKey = 'driver_id';
    public $timestamps = false;

    protected $fillable = [
    'fname',
    'mname',
    'lname',
    'license_no',
    'contact_number',
    'username',
    'password',
    'status',
    'is_archived'  // ← ADD THIS
];

    protected $hidden = ['password'];


        public function commissions()
    {
        return $this->hasMany(DriverCommission::class, 'driver_id', 'driver_id');
    }

    // Add relationship to dispatches
    public function dispatches()
    {
        return $this->hasMany(Dispatch::class, 'driver_id', 'driver_id');
    }

    
}