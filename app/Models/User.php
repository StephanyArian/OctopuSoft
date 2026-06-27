<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerificacionCorreo;          
use App\Notifications\ResetPasswordPersonalizado;


class User extends Authenticatable implements MustVerifyEmail {
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'biography',
        'photo_url',
        'photo_base64',
        'phone',
        'country',
        'city',
        'website',
        'profession_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getAuthPassword(): string
    {
        return $this->password ?? '';
    }

    public function skills()
    {
        return $this->hasMany(\App\Models\Skill::class);
    }

    // Relación con profesión
    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = $value ? substr($value, 0, 30) : null;
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = $value ? substr($value, 0, 30) : null;
    }



    public function setCityAttribute($value)
    {
        $this->attributes['city'] = $value ? substr($value, 0, 30) : null;
    }

    public function setCountryAttribute($value)
    {
        $this->attributes['country'] = $value ? substr($value, 0, 30) : null;
    }

    public function portfolio()
    {
        return $this->hasOne(\App\Models\Portfolio::class);
    }

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    /**
     * Relación con redes profesionales (usando tu modelo RedProfesional)
     */
    public function professionalNetworks()
    {
        return $this->hasMany(RedProfesional::class);
    }

        // En app/Models/User.php
    public function location()
    {
        return $this->hasOne(UserLocation::class);
    }
        public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerificacionCorreo());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordPersonalizado($token));
    }

}