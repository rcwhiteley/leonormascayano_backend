<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignaturaCurso extends Model
{
    // use HasFactory;
    protected $table = 'asignatura_curso';
    public $timestamps = false;
    protected $guarded = [];

    public function asignatura()
    {
        return $this->belongsTo(Asignaturas::class, 'asignaturas_id');
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluaciones::class, 'asignatura_curso_id');
    }
}