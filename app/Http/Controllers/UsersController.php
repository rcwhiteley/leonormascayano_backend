<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        try {
            $users =  Usuario::with('apoderado', 'estudiante', 'tutor', 'administrativo', 'administrador')->simplePaginate(10);
            return response()->json([
                'status' => 'success',
                'message' => 'request completed successfully',
                'data' => $users
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
