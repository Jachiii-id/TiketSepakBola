<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scrapping extends Model
{
    use HasFactory;

    protected $table = 'scrapping'; // Nama tabel di database
    protected $primaryKey = 'scrappingId'; // Primary Key

    // Kolom yang dapat diisi
    protected $fillable = [
        'scrappingId',
        'hashtag',
        'post_id',
        'post_url',
        'like_count',
        'comment_count',
        'thumbnail_url',
        'is_video',
        'location',
    ];
}
