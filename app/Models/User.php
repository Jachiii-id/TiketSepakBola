<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID when creating a new record
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Uuid::uuid4()->getBytes();
            }
        });
    }

    /**
     * Get the UUID attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getUuidAttribute($value)
    {
        return Uuid::fromBytes($value)->toString();
    }

    /**
     * Set the UUID attribute.
     *
     * @param  string  $value
     * @return void
     */
    public function setUuidAttribute($value)
    {
        $this->attributes['uuid'] = Uuid::fromString($value)->getBytes();
    }
}
