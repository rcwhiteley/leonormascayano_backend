<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CursoHasAlumno extends Pivot
{
    protected $table = 'curso_has_alumno';
    public $timestamps = false;
    protected $guarded = [];
}
