<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $table = 'portfolios';

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'description',
        'is_public',
        'show_email',
        'show_phone',
    ];

    /**
     * Relación con el usuario (dueño del portafolio)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con los proyectos del portafolio
     */
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}