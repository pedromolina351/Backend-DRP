<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InstitucionController;
use App\Http\Controllers\Api\PoaController;
use App\Http\Controllers\Api\ProgramaController;

Route::prefix('instituciones')->group(function () {
    Route::get('/obtenerTodasInstituciones', [InstitucionController::class, 'getInstitucionesList']);
    Route::get('/obtenerInstitucion/{id}', [InstitucionController::class, 'getInstitucion']);
    Route::post('/registrarInstitucion', [InstitucionController::class, 'createInstitucion']);
});

Route::prefix('poas')->group(function () {
    Route::get('/obtenerTodosPoas', [PoaController::class, 'getPoasList']);
    Route::get('/obtenerPoa/{id}', [PoaController::class, 'getPoa']);
    Route::post('/registrarPoa', [PoaController::class, 'createPoa']);
});

Route::prefix('programas')->group(function () {
    Route::get('/obtenerTodosProgramas', [ProgramaController::class, 'getProgramasList']);
    Route::get('/obtenerPrograma/{id}', [ProgramaController::class, 'getPrograma']);
    Route::post('/registrarPrograma', [ProgramaController::class, 'createPrograma']);
});