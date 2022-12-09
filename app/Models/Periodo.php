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

    function cursos(){
        return $this->hasMany(Curso::class, 'periodos_id');
    }

    function colegios(){
        return $this->hasManyThrough(Colegio::class, Curso::class, 'periodos_id', 'id', 'id', 'colegio_id');
    }
}
