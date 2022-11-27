<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taller extends Model
{
    // use HasFactory;
    protected $table = 'taller';
    public $timestamps = false;
    protected $guarded = [];
    public function estudiantes(){
        return $this->belongsToMany(Usuario::class, 'taller_has_alumno');
    }
}
