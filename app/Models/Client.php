<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    // Actual primary key
    protected $primaryKey = 'client_id';

    // If your table doesn't have created_at / updated_at
    public $timestamps = false;

    // Allow mass assignment
    protected $fillable = [
        'fname',
        'mname',
        'lname',
        'company_name',
        'address',
        'contact',
        'email',
        'is_archived',
    ];
    
        public function billings()
    {
        return $this->hasMany(Billing::class, 'client_id');
    }
}


