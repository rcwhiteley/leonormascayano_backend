<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    // use HasFactory;
    protected $table = 'alumno';
    public $timestamps = false;
    protected $guarded = [];

    function talleres(){
        return $this->belongsToMany(Taller::class, TallerHasAlumno::class);
    }

    function asistenciaTaller(){
        return $this->hasManyThrough(AsistenciaATaller::class, TallerHasAlumno::class);
    }
}
