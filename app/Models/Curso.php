<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    // use HasFactory;
    protected $table = 'curso';
    public $timestamps = false;
    protected $guarded = [];

    public function colegio()
    {
        return $this->belongsTo(Colegio::class);
    }

    public function nivel()
    {
        return $this->belongsTo(Niveles::class, 'niveles_id');
    }
}
