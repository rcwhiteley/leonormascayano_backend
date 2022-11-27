<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    // use HasFactory;
    protected $table = 'periodos';
    public $timestamps = false;
    protected $guarded = [];

    function talleres(){
        return $this->hasMany(Taller::class, 'periodos_id');
    }
}