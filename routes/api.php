<?php

use App\Http\Controllers\AsignaturasController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ColegiosController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\NivelesController;
use App\Http\Controllers\PeriodosController;
use App\Http\Controllers\TallerAsistenciaController;
use App\Http\Controllers\TallerCalificacionesController;
use App\Http\Controllers\TalleresController;
use App\Http\Controllers\TalleresEstudiantesController;
use App\Http\Controllers\UsersController;
use App\Models\Administrador;
use App\Models\Administrativo;
use App\Models\Apoderado;
use App\Models\Asignaturas;
use App\Models\Colegio;
use App\Models\Estudiante;
use App\Models\Niveles;
use App\Models\NivelEspecializacion;
use App\Models\NivelVinculo;
use App\Models\Tutor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/hola', function (Request $request) {
    return Usuario::find(1)->with('Tutor')->with('Apoderado')->get();
});

Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

Route::middleware('auth:sanctum')->get('/logout', [AuthController::class, 'logout']);

Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);
Route::middleware('auth:sanctum')->post('/usuarios', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->get('/usuarios', [UsersController::class, 'index']);


Route::/* middleware('auth:sanctum')-> */get('/talleres', [TalleresController::class, 'index']);
Route::middleware('auth:sanctum')->post('/talleres', [TalleresController::class, 'create']);
Route::get('/talleres/{id}', [TalleresController::class, 'show']);
Route::post('/talleres/{id}/estudiantes', [TalleresEstudiantesController::class, 'addStudent']);
Route::get('/talleres/{id}/estudiantes', [TalleresEstudiantesController::class, 'show']);
Route::get('/talleres/{id}/noestudiantes', [TalleresEstudiantesController::class, 'notStudents']);

Route::post('/talleres/{id}/evaluaciones', [TallerCalificacionesController::class, 'addEvaluacion']);
Route::get('/talleres/{id}/evaluaciones', [TallerCalificacionesController::class, 'getAll']);
Route::put('/talleres/{id}/evaluaciones/{evaluacionid}', [TallerCalificacionesController::class, 'updateEvaluacion']);
Route::patch('/talleres/{id}/evaluaciones/{evaluacionid}', [TallerCalificacionesController::class, 'updateEvaluacion']);

Route::get('/estudiantes/search', [UsersController::class, 'searchEstudiantes']);

Route::get('/talleres/{id}/asistencia', [TallerAsistenciaController::class, 'getAll']);
Route::post('/talleres/{id}/asistencia', [TallerAsistenciaController::class, 'add']);

Route::get('/niveles', [NivelesController::class, 'getAll']);
Route::get('/colegios', [ColegiosController::class, 'getAll']);
Route::get('/asignaturas', [AsignaturasController::class, 'getAll']);

Route::get('/cursos', [CursoController::class, 'getAll']);
Route::post('/cursos', [CursoController::class, 'createCurso']);

Route::/* middleware('auth:sanctum')-> */get('/periodos', [PeriodosController::class, 'index']);
Route::/* middleware('auth:sanctum')-> */post('/periodos', [PeriodosController::class, 'create']);

Route::get('/resetUsers', function (Request $request) {
    Administrador::all()->each(function ($item, $key) {
        $item->delete();
    });
    Administrativo::all()->each(function ($item, $key) {
        $item->delete();
    });
    Estudiante::all()->each(function ($item, $key) {
        $item->delete();
    });
    Apoderado::all()->each(function ($item, $key) {
        $item->delete();
    });
    Tutor::all()->each(function ($item, $key) {
        $item->delete();
    });
    Usuario::all()->each(function ($item, $key) {
        $item->delete();
    });
    $admin = new Usuario([
        'primer_nombre' => 'administrador',
        'segundo_nombre' => 'administrador',
        'apellido_paterno' => 'administrador',
        'apellido_materno' => 'administrador',
        'rut' => 'administrador',
        'email' => 'administrador',
        'telefono' => 'administrador',
        'password' => Hash::make('administrador'),
    ]);
    $admin->save();
    $administrador = new Administrador([
        'id' => $admin->id,
        'activo' => 1,
    ]);
    $administrador->save();

    $user1 = new Usuario([
        'primer_nombre' => 'administrativo',
        'segundo_nombre' => 'administrativo',
        'apellido_paterno' => 'administrativo',
        'apellido_materno' => 'administrativo',
        'rut' => 'administrativo',
        'email' => 'administrativo',
        'telefono' => 'administrativo',
        'password' => Hash::make('administrativo'),
    ]);
    $user1->save();
    $administrativo = new Administrativo([
        'id' => $user1->id,
        'activo' => 1,
    ]);
    $administrativo->save();

    for ($i = 0; $i < 10; $i++) {
        $user2 = new Usuario([
            'primer_nombre' => 'apoderado' . $i,
            'segundo_nombre' => 'apoderado' . $i,
            'apellido_paterno' => 'apoderado' . $i,
            'apellido_materno' => 'apoderado' . $i,
            'rut' => 'apoderado' . $i,
            'email' => 'apoderado' . $i,
            'telefono' => 'apoderado' . $i,
            'password' => Hash::make('apoderado' . $i),
        ]);
        $user2->save();
        $apoderado = new Apoderado([
            'id' => $user2->id,
            'activo' => 1,
            'direccion' => 'direccion_apoderado' . $i
        ]);
        $apoderado->save();
    }
    for ($i = 0; $i < 5; $i++) {
        $user3 = new Usuario([
            'primer_nombre' => 'tutor' . $i,
            'segundo_nombre' => 'tutor' . $i,
            'apellido_paterno' => 'tutor' . $i,
            'apellido_materno' => 'tutor' . $i,
            'rut' => 'tutor' . $i,
            'email' => 'tutor' . $i,
            'telefono' => 'tutor' . $i,
            'password' => Hash::make('tutor' . $i),
        ]);
        $user3->save();
        $tutor = new Tutor([
            'id' => $user3->id,
            'activo' => 1,
        ]);
        $tutor->save();
    }

    for ($i = 0; $i < 40; $i++) {
        $user4 = new Usuario([
            'primer_nombre' => 'alumno' . $i,
            'segundo_nombre' => 'alumno' . $i,
            'apellido_paterno' => 'alumno' . $i,
            'apellido_materno' => 'alumno' . $i,
            'rut' => 'alumno' . $i,
            'email' => 'alumno' . $i,
            'telefono' => 'alumno' . $i,
            'password' => Hash::make('alumno' . $i),
        ]);
        $user4->save();
        $estudiante = new Estudiante([
            'id' => $user4->id,
            'activo' => 1,
            'direccion' => 'direccion_alumno' . $i,
        ]);
        $estudiante->save();
    }
    return 'Usuarios creados';
});

Route::get('/generateStaticData', function (Request $request) {
    $ne = new NivelEspecializacion([
        'nivel_especializacion' => 'Alto',
    ]);
    $ne->save();
    $ne = new NivelEspecializacion([
        'nivel_especializacion' => 'Medio',
    ]);
    $ne->save();
    $ne = new NivelEspecializacion([
        'nivel_especializacion' => 'Bajo',
    ]);
    $ne->save();


    $nv = new NivelVinculo([
        'nivel_vinculo' => 'Alto',
    ]);
    $nv->save();
    $nv = new NivelVinculo([
        'nivel_vinculo' => 'Medio',
    ]);
    $nv->save();
    $nv = new NivelVinculo([
        'nivel_vinculo' => 'Bajo',
    ]);
    $nv->save();

    return 'Niveles creados';
});

Route::get('/generateStaticDataCursos', function (Request $request) {
    for ($i = 0; $i < 4; $i++) {
        $curso = new Colegio([
            'nombre' => 'Colegio ' . $i,
            'nombre_director' => 'Descripcion ' . $i,
            'direccion' => 'Direccion colegio' . $i,
            'email' => 'email' . $i . '@gmail.com',
            'celular' => '123456789',
        ]);
        $curso->save();
    }
    $nivel = new Niveles([
        'nombre' => '6° Basico',
    ]);
    $nivel->save();
    $nivel = new Niveles([
        'nombre' => '7° Basico',
    ]);
    $nivel->save();
    $nivel = new Niveles([
        'nombre' => '8° Basico',
    ]);
    $nivel->save();
    $nivel = new Niveles([
        'nombre' => '1° Medio',
    ]);
    $nivel->save();
    $nivel = new Niveles([
        'nombre' => '2° Medio',
    ]);
    $nivel->save();
    $nivel = new Niveles([
        'nombre' => '3° Medio',
    ]);
    $nivel->save();
    $nivel = new Niveles([
        'nombre' => '4° Medio',
    ]);
    $nivel->save();

    $asignatura = new Asignaturas([
        'nombre' => 'Matematicas',
    ]);
    $asignatura->save();

    $asignatura = new Asignaturas([
        'nombre' => 'Lenguaje',
    ]);
    $asignatura->save();

    $asignatura = new Asignaturas([
        'nombre' => 'Fisica',
    ]);
    $asignatura->save();

    $asignatura = new Asignaturas([
        'nombre' => 'Biología',
    ]);
    $asignatura->save();
    return 'Cursos creados';
});
