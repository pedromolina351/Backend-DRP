<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InstitucionController;
use App\Http\Controllers\Api\PoaController;
use App\Http\Controllers\Api\ProgramaController;
use App\Http\Controllers\Api\ModuloController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\PantallaController;
use App\Http\Controllers\Api\PoliticaController;
use App\Http\Controllers\Api\ObjetivoController;

Route::prefix('instituciones')->group(function () {
    Route::get('/obtenerTodasInstituciones', [InstitucionController::class, 'getInstitucionesList']);
    Route::get('/obtenerInstitucion/{id}', [InstitucionController::class, 'getInstitucion']);
    Route::post('/registrarInstitucion', [InstitucionController::class, 'createInstitucion']);
    Route::delete('/desactivarInstitucion/{id}', [InstitucionController::class, 'deactivateInstitucion']);
});

Route::prefix('poas')->group(function () {
    Route::get('/obtenerTodosPoas', [PoaController::class, 'getPoasList']);
    Route::get('/obtenerPoa/{id}', [PoaController::class, 'getPoa']);
    Route::post('/registrarPoa', [PoaController::class, 'createPoa']);
    Route::delete('/desactivarPoa/{id}', [PoaController::class, 'deactivatePoa']);
});

Route::prefix('programas')->group(function () {
    Route::get('/obtenerTodosProgramas', [ProgramaController::class, 'getProgramasList']);
    Route::get('/obtenerPrograma/{id}', [ProgramaController::class, 'getPrograma']);
    Route::post('/registrarPrograma', [ProgramaController::class, 'createPrograma']);
    Route::delete('/desactivarPrograma/{id}', [ProgramaController::class, 'deactivatePrograma']);
});

Route::prefix('modulos')->group(function () {
    Route::get('/obtenerTodosModulos', [ModuloController::class, 'getModulosList']);
    Route::get('/obtenerModulo/{id}', [ModuloController::class, 'getModulo']);
    Route::post('/registrarModulo', [ModuloController::class, 'createModulo']);
});

Route::prefix('usuarios')->group(function () {
    Route::get('/obtenerTodosUsuarios', [UsuarioController::class, 'getUsuariosList']);
    Route::get('/obtenerUsuario/{id}', [UsuarioController::class, 'getUsuario']);
    Route::post('/registrarUsuario', [UsuarioController::class, 'createUsuario']);
});

Route::prefix('roles')->group(function () {
    Route::get('/obtenerTodosRoles', [RolController::class, 'getRolesList']);
    Route::get('/obtenerRol/{id}', [RolController::class, 'getRol']);
    Route::post('/registrarRol', [RolController::class, 'createRol']);
});

Route::prefix('pantallas')->group(function () {
    Route::get('/obtenerTodasPantallas', [PantallaController::class, 'getAllPantallas']);
    Route::get('/obtenerPantalla/{id}', [PantallaController::class, 'getPantalla']);
    Route::post('/registrarPantalla', [PantallaController::class, 'createPantalla']);
});

Route::prefix('politicas')->group(function () {
    Route::get('/obtenerTodasPoliticas', [PoliticaController::class, 'getPoliticasList']);
    Route::get('/obtenerPolitica/{id}', [PoliticaController::class, 'getPolitica']);
    Route::post('/registrarPolitica', [PoliticaController::class, 'createPolitica']);
});

Route::prefix('objetivos')->group(function () {
    Route::get('/obtenerTodosObjetivos', [ObjetivoController::class, 'getObjetivosList']);
    Route::get('/obtenerObjetivo/{id}', [ObjetivoController::class, 'getObjetivo']);
    Route::post('/registrarObjetivo', [ObjetivoController::class, 'createObjetivo']);
    Route::put('/desactivarObjetivo/{id}', [ObjetivoController::class, 'deactivateObjetivo']);
});