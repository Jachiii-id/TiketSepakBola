<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'payment_channel',
        'ip_address',
        'device_id',
        'platform',
        'browser',
        'language',
        'total_harga',
        'total_dibayar',
        'total_diterima',
        'snap_token',
    ];

    // Relasi dengan tiket
    public function tickets()
    {
        return $this->hasMany(Tickets::class);
    }
}
