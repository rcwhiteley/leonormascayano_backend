<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        return Usuario::with('apoderado', 'estudiante', 'tutor', 'administrativo', 'administrador')->simplePaginate(10);
    }
}
