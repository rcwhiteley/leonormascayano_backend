<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    // use HasFactory;
    use HasApiTokens, Notifiable, HasFactory;
    protected $table = 'usuario';
    public $timestamps = false;

    protected $guarded = ['id'];
    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
    ];

    public function Apoderado()
    {
        return $this->hasOne(Apoderado::class, 'id');
    }

    public function Estudiante()
    {
        return $this->hasOne(Estudiante::class, 'id');
    }

    public function Tutor()
    {
        return $this->hasOne(Tutor::class, 'id');
    }

    public function Administrador()
    {
        return $this->hasOne(Administrador::class, 'id');
    }

    public function Administrativo()
    {
        return $this->hasOne(Administrativo::class, 'id');
    }
}
