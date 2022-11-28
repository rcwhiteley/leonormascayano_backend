<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionesTallerRendidas extends Model
{
    // use HasFactory;
    protected $table = 'evaluaciones_taller_rendidas';
    public $timestamps = false;
    protected $guarded = [];
}
