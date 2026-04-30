<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
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

    /**
     * Mutadores para limitar automáticamente los campos a sus longitudes máximas
     * Esto sirve como capa adicional de seguridad en el modelo
     */
    public function setFirstNameAttribute($value)
    {
        $this->attributes['first_name'] = $value ? substr($value, 0, 30) : null;
    }

    public function setLastNameAttribute($value)
    {
        $this->attributes['last_name'] = $value ? substr($value, 0, 30) : null;
    }

    public function setBiographyAttribute($value)
    {
        $this->attributes['biography'] = $value ? substr($value, 0, 500) : null;
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
}