<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiasDeClases extends Model
{
    protected $table = 'dias_de_clases';
    public $timestamps = false;
    protected $guarded = [];

    public function asistencia_a_taller()
    {
        return $this->hasMany(AsistenciaATaller::class);
    }
}
