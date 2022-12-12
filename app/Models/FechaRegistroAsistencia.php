<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FechaRegistroAsistencia extends Model
{
    //use HasFactory;
    protected $table = 'fecha_registro_asistencia';
    public $timestamps = false;
    protected $guarded = [];

    public function asistencia_clases()
    {
        return $this->hasMany(AsistenciaClases::class, 'fecha_registro_asistencia_id');
    }
}
