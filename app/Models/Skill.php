<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'level',
        'category',
        'is_visible',
        'display_order',
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Devuelve la etiqueta legible del nivel 

    public function levelLabel(): string
    {
        return match ((int) $this->level) {
            1 => 'Básico',
            2 => 'Intermedio',
            3 => 'Avanzado',
            default => 'Básico',
        };
    }

    /**
     * Devuelve la clase CSS del badge según nivel.
     */
    public function levelBadgeClass(): string
    {
        return match ((int) $this->level) {
            1 => 'badge-basico',
            2 => 'badge-intermedio',
            3 => 'badge-avanzado',
            default => 'badge-basico',
        };
    }

    /**
     * Porcentaje para la barra de progreso (33 / 66 / 100).
     */
    public function levelPercent(): int
    {
        return match ((int) $this->level) {
            1 => 33,
            2 => 66,
            3 => 100,
            default => 33,
        };
    }

    public function projects()
    {
        return $this->belongsToMany(
            \App\Models\Project::class,
            'project_skill' 
        );
    }
}