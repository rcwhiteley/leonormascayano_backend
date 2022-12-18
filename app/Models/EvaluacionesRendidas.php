<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionesRendidas extends Model
{
    // use HasFactory;
    protected $table = 'evaluaciones_rendidas';
    public $timestamps = false;
    protected $guarded = [];

    public function curso_has_alumnos(){
        return $this->belongsTo(CursoHasAlumno::class, 'curso_has_alumno_id');
    }
}
