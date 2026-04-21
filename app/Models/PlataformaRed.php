<?php
// app/Models/PlataformaRed.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlataformaRed extends Model
{
    protected $table = 'platform_network';

    protected $fillable = ['name', 'base_url'];

    // timestamps sí existen en esta tabla
    public $timestamps = true;
}