<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // T3: campos que se pueden llenar masivamente
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
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
