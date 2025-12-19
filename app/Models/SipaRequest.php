<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SipaRequest extends Model
{
    use HasFactory;

    protected $table = 'siparequest';
    protected $primaryKey = 'sipa_id';
    public $timestamps = false;

    protected $fillable = [
        'client_id',
        'sipa_ref_no',
        'type',
        'created_at',
        'is_archived'  // ← ADD THIS!
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}