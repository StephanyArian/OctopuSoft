<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    // T3: campos que se pueden llenar masivamente
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'role',
        'status',
        'terms_accepted',
    ];

    // Campos que NUNCA se envían al frontend
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Conversión automática de tipos
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed', // T6: bcrypt automático
        ];
    }
}
