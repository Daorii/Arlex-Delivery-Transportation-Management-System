<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sipa extends Model
{
    protected $table = 'siparequest';  // Changed from 'sipa_requests'
    protected $primaryKey = 'sipa_id';
    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'sipa_ref_no',
        'type',
        'created_at'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    public function details()
    {
        return $this->hasMany(Sipadetail::class, 'sipa_id', 'sipa_id');
    }

    public function dispatches()
    {
        return $this->hasMany(Dispatch::class, 'sipa_id', 'sipa_id');
    }
}