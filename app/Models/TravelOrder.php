<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelOrder extends Model {
    
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'requester_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}