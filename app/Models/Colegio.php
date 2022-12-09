<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colegio extends Model
{
    // use HasFactory;
    protected $table = 'colegio';
    public $timestamps = false;
    protected $guarded = [];

    public function cursos()
    {
        return $this->hasMany(Curso::class);
    }
}
