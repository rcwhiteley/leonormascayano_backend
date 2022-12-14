<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    // use HasFactory;
    protected $table = 'tutor';
    public $timestamps = false;

    protected $guarded = [];

    public function Usuario()
    {
        return $this->belongsTo(Usuario::class, 'id');
    }
}
