<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';

    protected $fillable = [
        'portfolio_id',
        'name',
        'summary',
        'description',
        'role',
        'company',           // ← AGREGA ESTA LÍNEA
        'demo_url',
        'repository_url',
        'start_date',
        'end_date',
        'status',
        'is_featured',
        'is_visible',
        'display_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
    ];

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function technologies()
    {
        return $this->belongsToMany(Technology::class, 'project_technology');
    }

    // Relación con evidencias
    public function evidencias()
    {
        return $this->hasMany(ProjectEvidencia::class, 'project_id');
    }

    public function skills()
    {
        return $this->belongsToMany(
            \App\Models\Skill::class,
            'project_skill'
        );
    }

}