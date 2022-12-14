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
        return $this->belongsToMany(Taller::class, TallerHasAlumno::class, 'alumno_id', 'taller_id')->withPivot(['id']);
    }

    function usuario(){
        return $this->belongsTo(Usuario::class, 'id');
    }

    function asistenciaTaller(){
        return $this->hasManyThrough(AsistenciaATaller::class, TallerHasAlumno::class, 'alumno_id', 'taller_has_alumno_id');
    }

    function evaluacionesTallerRendidas(){
        return $this->hasManyThrough(EvaluacionesTallerRendidas::class, TallerHasAlumno::class, 'alumno_id', 'taller_has_alumno_id');
    }

    function cursos(){
        return $this->belongsToMany(Curso::class, CursoHasAlumno::class, 'alumno_id', 'curso_id')->withPivot(['id']);
    }

    function asistenciaClases(){
        return $this->hasManyThrough(AsistenciaClases::class, CursoHasAlumno::class, 'alumno_id', 'curso_has_alumno_id');
    }

    function evaluacionesRendidas(){
        return $this->hasManyThrough(EvaluacionesRendidas::class, CursoHasAlumno::class, 'alumno_id', 'curso_has_alumno_id');
    }
}
