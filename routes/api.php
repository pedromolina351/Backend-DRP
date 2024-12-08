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
use App\Http\Controllers\Api\MetaController;
use App\Http\Controllers\Api\IndicadorController;
use App\Http\Controllers\Api\ObjetivoVisionPaisController;
use App\Http\Controllers\Api\GabineteController;
use App\Http\Controllers\Api\EjeEstrategicoController;
use App\Http\Controllers\Api\ObjetivoPegController;
use App\Http\Controllers\Api\ResultadoPegController;
use App\Http\Controllers\Api\IndicadorResultadoPegController;

Route::prefix('instituciones')->group(function () {
    Route::get('/obtenerTodasInstituciones', [InstitucionController::class, 'getInstitucionesList']);
    Route::get('/obtenerInstitucion/{id}', [InstitucionController::class, 'getInstitucion']);
    Route::post('/registrarInstitucion', [InstitucionController::class, 'createInstitucion']);
    Route::put('/desactivarInstitucion/{id}', [InstitucionController::class, 'deactivateInstitucion']);
});

Route::prefix('poas')->group(function () {
    Route::get('/obtenerTodosPoas', [PoaController::class, 'getPoasList']);
    Route::get('/obtenerPoa/{id}', [PoaController::class, 'getPoa']);
    Route::post('/registrarPoa', [PoaController::class, 'createPoa']);
    Route::put('/desactivarPoa/{id}', [PoaController::class, 'deactivatePoa']);
    Route::get('/obtenerPoasPorInstitucion/{id}', [PoaController::class, 'getPoasByInstitucion']);
});

Route::prefix('programas')->group(function () {
    Route::get('/obtenerTodosProgramas', [ProgramaController::class, 'getProgramasList']);
    Route::get('/obtenerPrograma/{id}', [ProgramaController::class, 'getPrograma']);
    Route::post('/registrarPrograma', [ProgramaController::class, 'createPrograma']);
    Route::put('/desactivarPrograma/{id}', [ProgramaController::class, 'deactivatePrograma']);
    Route::get('/obtenerProgramasPorInstitucion/{id}', [ProgramaController::class, 'getProgramasByInstitucion']);
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

Route::prefix('metas')->group(function () {
    Route::get('/metasbyobjetivo/{codigo_objetivo}', [MetaController::class, 'getMetasByObjetivo']);
    Route::get('/obtenerTodasMetas', [MetaController::class, 'getAllMetas']);
});

Route::prefix('indicadores')->group(function () {
    Route::get('/obtenerTodosIndicadores', [IndicadorController::class, 'getAllIndicadores']);
    Route::get('/indicadoresbymeta/{codigo_meta}', [IndicadorController::class, 'getIndicadoresByMeta']);
});

Route::prefix('vision-pais')->group(function () {
    Route::get('/obtenerObjetivosVisionPais', [ObjetivoVisionPaisController::class, 'getAllObjetivosVisionPais']);
    Route::get('/obtenerMetasVisionPaisPorObjetivo/{id}', [ObjetivoVisionPaisController::class, 'getMetasVisionPaisByObjetivo']);
});

Route::prefix('gabinetes')->group(function () {
    Route::get('/obtenerTodosGabinetes', [GabineteController::class, 'getAllGabinetes']);
});

Route::prefix('ejes-estrategicos')->group(function () {
    Route::get('/obtenerEjeEstrategicoPorGabinete/{codigo_gabinete}', [EjeEstrategicoController::class, 'getEjeEstrategicoByGabinete']);
});

Route::prefix('objetivos-peg')->group(function () {
    Route::get('/obtenerObjetivoPegPorEjeEstrategico/{codigo_eje_estrategico}', [ObjetivoPegController::class, 'getObjetivoPegByEjeEstrategico']);
});

Route::prefix('resultados-peg')->group(function () {
    Route::get('/obtenerResultadosPegPorObjetivoPeg/{codigo_objetivo_peg}', [ResultadoPegController::class, 'getResultadosPegByObjetivoPeg']);
});

Route::prefix('indicadores-resultados-peg')->group(function () {
    Route::get('/obtenerIndicadoresResultadosPegPorResultadoPeg/{codigo_resultado_peg}', [IndicadorResultadoPegController::class, 'getIndicadoresResultadosPegByResultadoPeg']);
});


