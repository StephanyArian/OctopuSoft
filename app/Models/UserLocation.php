<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    protected $table = 'user_locations';

    protected $fillable = [
        'user_id',
        'address',
        'latitude',
        'longitude',
        'show_location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}