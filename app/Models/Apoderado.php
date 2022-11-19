<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apoderado extends Model
{
    // use HasFactory;
    protected $table = 'apoderado';
    public $timestamps = false;
    protected $guarded = [];

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'id');
    }
}
