<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_id',
        'seat_id',
        'payment_id',
        'num_tickets',
        'amount',
        'ticket_data',
        'snap_token',
    ];

    protected $casts = [
        'ticket_data' => 'array',  // Menyimpan ticket_data sebagai array
    ];

    // Relasi dengan user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan match
    public function match()
    {
        return $this->belongsTo(Matches::class);
    }

    // Relasi dengan seat
    public function seat()
    {
        return $this->belongsTo(Seats::class);
    }

    // Relasi dengan payment
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
