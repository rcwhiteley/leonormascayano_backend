<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenciaClases extends Model
{
    // use HasFactory;
    protected $table = 'asistencia_clases';
    public $timestamps = false;
    protected $guarded = [];

    public function estudiante_pivot(){
        return $this->belongsTo(CursoHasAlumno::class, 'curso_has_alumno_id');
    }
}
