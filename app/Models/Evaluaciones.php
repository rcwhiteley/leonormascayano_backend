<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluaciones extends Model
{
    // use HasFactory;
    protected $table = 'evaluaciones';
    public $timestamps = false;
    protected $guarded = [];

    public function evaluaciones_rendidas(){
        return $this->hasMany(EvaluacionesRendidas::class, 'evaluaciones_Id');
    }
}
