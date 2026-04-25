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

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}