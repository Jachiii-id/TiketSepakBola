<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitras extends Model
{
    use HasFactory;

    protected $table = 'mitra';

    protected $fillable = ['name', 'description', 'rating', 'registration_date', 'status'];

    protected static function boot()
    {
        parent::boot();
    }
}
