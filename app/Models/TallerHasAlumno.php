<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TallerHasAlumno extends Pivot
{
    // use HasFactory;
    protected $table = 'taller_has_alumno';
    public $timestamps = false;
    protected $guarded = [];
}
