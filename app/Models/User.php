<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->hasOne(Role::class);
    }

    public function isAdmin()
    {
        return $this->role && $this->role->role === 'admin';
    }

    public function permintaan()
    {
        return $this->hasMany(Permintaan::class);
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    /**
     * Get the full URL for the user's photo.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return \Storage::disk('public')->url($this->photo);
        }
        return null;
    }
}
