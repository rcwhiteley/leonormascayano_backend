<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taller extends Model
{
    // use HasFactory;
    protected $table = 'taller';
    protected $appends = ['numero_estudiantes', 'numero_tutores'];
    public $timestamps = false;
    protected $guarded = [];
    public function estudiantes(){
        return $this->belongsToMany(Estudiante::class, 'taller_has_alumno', 'taller_id', 'alumno_id')->withPivot(['id']);
    }

    public function tutores(){
        return $this->belongsToMany(Tutor::class, 'tutor_has_taller', 'taller_id', 'tutor_id');
    }

    public function periodo(){
        return $this->belongsTo(Periodo::class, 'periodos_id');
    }

    public function nivel_especializacion(){
        return $this->belongsTo(NivelEspecializacion::class, 'nivel_especializacion_id');
    }

    public function nivel_vinculo(){
        return $this->belongsTo(NivelVinculo::class, 'nivel_vinculo_id');
    }

    public function evaluaciones(){
        return $this->hasMany(EvaluacionesTaller::class, 'taller_id');
    }

    public function dias_de_clases(){
        return $this->hasMany(DiasDeClases::class, 'taller_id');
    }

    public function getNumeroEstudiantesAttribute(){
        return $this->estudiantes()->count();
    }

    public function getNumeroTutoresAttribute(){
        return $this->tutores()->count();
    }
}
