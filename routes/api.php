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
use App\Http\Controllers\Api\ComentarioController;
use App\Http\Controllers\Api\ResultadoFinalController;
use App\Http\Controllers\Api\ResultadoController;
use App\Http\Controllers\Api\ObjetivosOperativosController;
use App\Http\Controllers\Api\ProductosFinalesController;
use App\Http\Controllers\Api\ProductosIntermediosController;
use App\Http\Controllers\Api\ImpactoController;
use App\Http\Controllers\Api\ActividadInsumoController;
use App\Http\Controllers\Api\DatosComplementariosController;

Route::prefix('instituciones')->group(function () {
    Route::get('/obtenerTodasInstituciones', [InstitucionController::class, 'getInstitucionesList']);
    Route::get('/obtenerInstitucion/{id}', [InstitucionController::class, 'getInstitucion']);
    Route::post('/registrarInstitucion', [InstitucionController::class, 'createInstitucion']);
    Route::put('/desactivarInstitucion/{id}', [InstitucionController::class, 'deactivateInstitucion']);
});

Route::prefix('poas')->group(function () {
    Route::get('/obtenerTodosPoas', [PoaController::class, 'getPoasList']);
    Route::get('/obtenerPoa/{codigo_poa}', [PoaController::class, 'getPoa']);
    Route::post('/registrarPoa', [PoaController::class, 'createPoa']);
    Route::put('/desactivarPoa/{id}', [PoaController::class, 'deactivatePoa']);
    Route::get('/obtenerPoasPorInstitucion', [PoaController::class, 'getPoasByInstitution']);
    Route::post('/insertPoaMain', [PoaController::class, 'insertPoaMain']);
    Route::put('/editPoa', [PoaController::class, 'editPoaMain']);
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

Route::prefix('comentarios')->group(function () {
    Route::get('/comentariosbypoa/{poaId}', [ComentarioController::class, 'getCommentsByPoaId']);
    Route::post('/insertarcomentario', [ComentarioController::class, 'insertNewComment']);
});

Route::prefix('resultados-finales')->group(function () {
    Route::get('/obtenerTodosResultadosFinales', [ResultadoFinalController::class, 'getAllResultadoFinal']);
    Route::get('/indicadoresbyresultado/{resultadoFinalId}', [ResultadoFinalController::class, 'getAllIndicadoresByResultadoFinalId']);
});

Route::prefix('resultados')->group(function () {
    Route::post('/insertarResultados', [ResultadoController::class, 'insertResultado']);
    Route::post('/insertarImpactos', [ImpactoController::class, 'insertImpactos']);
    Route::get('/impactosbypoa/{codigo_poa}', [ImpactoController::class, 'getImpactosByPoaId']);
    Route::get('/resultadosbypoa/{codigo_poa}', [ResultadoController::class, 'getResultadosByPoa']);
});

Route::prefix('objetivos-operativos')->group(function () {
    Route::post('/insertObjetivosOperativos', [ObjetivosOperativosController::class, 'insertObjetivosOperativos']);
    Route::get('/objetivos-operativos-by-poa/{codigo_poa}', [ObjetivosOperativosController::class, 'getObjetivosOperativosByPoa']);
});

Route::prefix('productos')->group(function () {
    Route::post('/insertProductosFinales', [ProductosFinalesController::class, 'insertProductosFinales']);
    Route::post('/insertProductosIntermedios', [ProductosIntermediosController::class, 'insertProductosIntermedios']);
    Route::get('/productos-finales-by-poa/{codigo_poa}', [ProductosFinalesController::class, 'getProductosFinalesByPoa']);
    Route::get('/productos-intermedios-by-poa/{codigo_poa}', [ProductosIntermediosController::class, 'getProductosIntermediosByPoa']);
});

Route::prefix('actividades-insumos')->group(function () {
    Route::post('/insertarActividadesInsumos', [ActividadInsumoController::class, 'insertActividadInsumo']);
    Route::get('/actividades-insumos-by-poa/{codigo_poa}', [ActividadInsumoController::class, 'getActividadesInsumosByPoaId']);
    Route::get('/actividades-insumos-by-producto-and-poa/{codigo_producto_final}/{codigo_poa}', [ActividadInsumoController::class, 'getActividadesInsumosByProductoAndPoa']);
});

Route::prefix('datos-complementarios')->group(function () {
    Route::get('/datos-complementarios-by-poa/{codigo_poa}', [DatosComplementariosController::class, 'getDatosComplementariosByPoa']);
    Route::post('/insertar-datos-complementarios', [DatosComplementariosController::class, 'insertDatosComplementarios']);
});

