<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\Administrativo;
use App\Models\Apoderado;
use App\Models\Estudiante;
use App\Models\Tutor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $body = (object)$request->all();
        $validations = [
            'primer_nombre' => 'required|string',
            'segundo_nombre' => 'required|string',
            'apellido_paterno' => 'required|string',
            'apellido_materno' => 'required|string',
            'rut' => 'required|string',
            'email' => 'required|string',
            'telefono' => 'required|string',
        ];
        if (isset($body->apoderado)) {
            $validations['apoderado.direccion'] = 'required|string';
        }
        if (isset($body->estudiante)) {
            $validations['estudiante.direccion'] = 'required|string';
        }
        error_log(json_encode($validations));
        $validator = Validator::make($request->all(), $validations);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        DB::beginTransaction();
        try {
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
            if (isset($body->apoderado)) {
                $apoderado = new Apoderado([
                    'id' => $user->id,
                    'direccion' => $body->apoderado['direccion'],
                    'activo' => true
                ]);
                $apoderado->save();
            }
            if (isset($body->estudiante)) {
                $estudiante = new Estudiante([
                    'id' => $user->id,
                    'direccion' => $body->estudiante['direccion'],
                    'activo' => true
                ]);
                $estudiante->save();
            }
            if (isset($body->tutor)) {
                $tutor = new Tutor([
                    'id' => $user->id,
                    'activo' => true
                ]);
                $tutor->save();
            }
            if (isset($body->administrador)) {
                $administrador = new Administrador([
                    'id' => $user->id,
                    'activo' => true
                ]);
                $administrador->save();
            }
            if (isset($body->administrativo)) {
                $administrativo = new Administrativo([
                    'id' => $user->id,
                    'activo' => true
                ]);
                $administrativo->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            error_log($e->getMessage());
            return response()->json(['message' => 'Error al crear usuario'], 500);
        }

        $user = Usuario::with('apoderado', 'estudiante', 'tutor', 'administrador', 'administrativo')->where('email', $request->email)->first();
        if ($user->administrador == null) {
            unset($user->administrador);
        }
        if ($user->administrativo == null) {
            unset($user->administrativo);
        }
        if ($user->apoderado == null) {
            unset($user->apoderado);
        }
        if ($user->estudiante == null) {
            unset($user->estudiante);
        }
        if ($user->tutor == null) {
            unset($user->tutor);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Usuario creado exitosamente',
            'data' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                $user = Usuario::with('apoderado', 'estudiante', 'tutor', 'administrador', 'administrativo')->where('email', $request->email)->first();
                if ($user->administrador == null) {
                    unset($user->administrador);
                }
                if ($user->administrativo == null) {
                    unset($user->administrativo);
                }
                if ($user->apoderado == null) {
                    unset($user->apoderado);
                }
                if ($user->estudiante == null) {
                    unset($user->estudiante);
                }
                if ($user->tutor == null) {
                    unset($user->tutor);
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Login correcto',
                    'token' => $user->createToken('authToken')->plainTextToken,
                    'data' => $user
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid data'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->administrador == null) {
                unset($user->administrador);
            }
            if ($user->administrativo == null) {
                unset($user->administrativo);
            }
            if ($user->apoderado == null) {
                unset($user->apoderado);
            }
            if ($user->estudiante == null) {
                unset($user->estudiante);
            }
            if ($user->tutor == null) {
                unset($user->tutor);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'Login correcto',
                'token' => $user->createToken('authToken')->plainTextToken,
                'data' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout correcto'
        ], 200);
    }
}
