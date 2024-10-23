<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'availability_id',
        'ticket_number',
        'name',
        'phone',
        'address',
        'status',
        'message',
    ];

    public function availability()
    {
        return $this->belongsTo(Availability::class);
    }
}
