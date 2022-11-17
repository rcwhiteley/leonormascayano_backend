<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'primer_nombre' => 'required|string',
            'segundo_nombre' => 'required|string',
            'apellido_paterno' => 'required|string',
            'apellido_materno' => 'required|string',
            'rut' => 'required|string',
            'email' => 'required|string',
            'telefono' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors(), 400);
        }
        $user = new Usuario([
            'primer_nombre' => $request->primer_nombre,
            'segundo_nombre' => $request->segundo_nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'rut' => $request->rut,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'password' => Hash::make($request->rut),
        ]);
        $user->save();
        return response()->json([
            'message' => 'Usuario creado exitosamente'
        ], 201);
    }
    public function login(Request $request)
    {
        try{
            $validateUser = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ]);
            if($validateUser->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation errors',
                    'errors' => $validateUser->errors()
                ], 401);
            }
            if(!Auth::attempt($request->only('email', 'password'))){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Datos incorrectos'
                ], 401);
            }

            $user = Usuario::with('apoderado', 'estudiante', 'tutor', 'administrador', 'administrativo')->where('email', $request->email)->first();
            if($user->administrador == null){
                unset($user->administrador);
            }
            if($user->administrativo == null){
                unset($user->administrativo);
            }
            if($user->apoderado == null){
                unset($user->apoderado);
            }
            if($user->estudiante == null){
                unset($user->estudiante);
            }
            if($user->tutor == null){
                unset($user->tutor);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Login correcto',
                'token' => $user->createToken('authToken')->plainTextToken,
                'data' => $user
            ], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
