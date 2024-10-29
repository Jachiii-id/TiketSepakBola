<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clubs extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'name', 'logo', 'description']; // Replace with actual fields

    protected $table = 'clubs';

    protected static function boot()
    {
        parent::boot();
    }
}
