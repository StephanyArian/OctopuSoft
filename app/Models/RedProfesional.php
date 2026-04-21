<?php
// app/Models/RedProfesional.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedProfesional extends Model
{
    protected $table = 'professional_networks';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'platform_id',
        'username',
        'profile_url',
        'is_visible',
        'is_primary',
        'display_order',
    ];

    // Relación con la plataforma
    public function plataforma()
    {
        return $this->belongsTo(PlataformaRed::class, 'platform_id');
    }
}