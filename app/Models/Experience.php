<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    protected $table = 'experiences';

    protected $fillable = [
        'user_id',
        'type',
        'institution',
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'is_current',
        'is_visible',
        'display_order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
        'is_visible' => 'boolean',
    ];

    // Solo registros de educación
    public function scopeEducation($query)
    {
        return $query->where('type', 'education');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}