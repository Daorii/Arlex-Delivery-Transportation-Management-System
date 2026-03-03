<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SipaDetail extends Model
{
    protected $table = 'sipadetails';
    protected $primaryKey = 'sipa_detail_id';
    public $timestamps = false; 

    protected $fillable = [
        'sipa_id',
        'volume',
        'size',
        'price',
        'route_from',
        'route_to',
        'effectivity_from',
        'effectivity_to',
    ];

    public function sipa()
    {
        return $this->belongsTo(Sipa::class, 'sipa_id', 'sipa_id');
    }
}
