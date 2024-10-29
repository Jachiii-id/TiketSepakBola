<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'user_id',
        'match_id',
        'seat_id',
        'payment_id',
        'num_tickets',
        'amount',
        'ticket_data'
    ];
}
