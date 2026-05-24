<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectEvidencia extends Model
{
    protected $table = 'project_evidencias';
    
    protected $fillable = [
        'project_id',
        'tipo',
        'titulo',
        'url',
        'imagen_path',
        'descripcion',
        'plataforma',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}