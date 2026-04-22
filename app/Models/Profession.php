<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profession extends Model
{
    // Especificar el nombre de la tabla (aunque Laravel lo infiere automáticamente)
    protected $table = 'professions';
    
    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'name'
    ];
    
    // Relación con los usuarios (una profesión tiene muchos usuarios)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}