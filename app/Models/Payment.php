<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'reff_id',
        'status',
        'customer_email',
        'customer_name',
        'customer_phone',
        'payment_channel',
        'ip_address',
        'device_id',
        'platform',
        'browser',
        'total_harga',
        'total_dibayar',
        'total_diterima',
    ];

    protected static function boot()
    {
        parent::boot();
    }
}
