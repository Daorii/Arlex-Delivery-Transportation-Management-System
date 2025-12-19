<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $table = 'trucks';
    protected $primaryKey = 'truck_id'; // <-- Add this line
    public $timestamps = false;
    
    protected $fillable = [
    'plate_no',
    'description',
    'status',
    'maintenance_reason',
    'is_archived'  // ← ADD THIS
];

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class, 'truck_id', 'truck_id');
    }
}