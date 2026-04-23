<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'fecha',
        'estado',
        'tecnologias',
    ];

    protected $casts = [
        'tecnologias' => 'array',
        'fecha'       => 'date',
    ];

    // Relación con evidencias
    public function evidencias()
    {
        return $this->hasMany(ProyectoEvidencia::class, 'proyecto_id');
    }

    // Scope para filtrar por usuario
    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}