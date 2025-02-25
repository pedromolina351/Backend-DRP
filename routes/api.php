<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InstitucionController;
use App\Http\Controllers\Api\PoaController;
use App\Http\Controllers\Api\ProgramaController;
use App\Http\Controllers\Api\ModuloController;
use App\Http\Controllers\Api\usuarioController;
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
use App\Http\Controllers\DictamenController;
use App\Http\Controllers\MMR\CategorizacionController;
use App\Http\Controllers\MMR\TipoIndicadorController;
use App\Http\Controllers\MMR\UnidadesController;
use App\Http\Controllers\MMR\NivelImpactoController;
use App\Http\Controllers\MMR\TiposRiesgoController;
use App\Http\Controllers\Api\IntervencionesPriorizadasController;
use App\Http\Controllers\Api\FilController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\Indicador;
use App\Http\Controllers\MatrizPoaController;

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
    Route::put('/aprobarPOA/{codigo_poa}', [PoaController::class, 'aprobarPOA']);
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
    Route::get('/obtenerTodosUsuarios', [usuarioController::class, 'getUsuariosList']);
    Route::get('/obtenerUsuario/{id}', [usuarioController::class, 'getUsuario']);
    Route::post('/registrarUsuario', [usuarioController::class, 'createUsuario']);
    Route::put('/modificarUsuario', [usuarioController::class, 'updateUser']);
    Route::delete('/eliminarUsuario/{codigo_usuario}', [usuarioController::class, 'deleteUser']);
    Route::post('/login', [usuarioController::class, 'userLogin']);
    Route::post('/cambiarClave', [usuarioController::class, 'changePassword']);
    Route::post('/enviar-correo-reset', [usuarioController::class, 'enviarCorreoReseteoInicioSesion']);
});

Route::prefix('roles')->group(function () {
    Route::get('/obtenerTodosRoles', [RolesController::class, 'getAllRoles']);
    Route::post('/registrarRol', [RolesController::class, 'createRole']);
    Route::put('/modificarEstadoRol', [RolesController::class, 'modificarEstadoRol']);
    Route::put('/actualizarRol', [RolesController::class, 'updateRole']);
    Route::get('/obtenerAccesos/{codigo_rol}', [RolesController::class, 'getAccesosRol']);
    Route::get('/obtenerDetalles/{codigo_rol}', [RolesController::class, 'getInfoRol']);
    Route::get('/obtenerModulos', [RolesController::class, 'getAllModulos']);
    Route::delete('/eliminarRol/{codigo_rol}', [RolesController::class, 'deleteRole']);
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
    Route::get('/obtenerResultadosPegPorEjeEstrategico/{codigo_eje_estrategico}', [ResultadoPegController::class, 'getResultadosPegByEjeEstrategico']);
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
    Route::post('/insertPoaResultadosImpactos', [ResultadoController::class, 'insertPoaResultadosImpactos']);
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
    Route::post('/insertMonitoreoProductosFinales', [ProductosFinalesController::class, 'insertMonitoreoProductosFinales']);
    Route::get('/monitoreo-productos-finales/{codigo_poa}', [ProductosFinalesController::class, 'getMonitoreoProductosFinales']);
    Route::post('/insertMonitoreoProductosIntermedios', [ProductosIntermediosController::class, 'insertMonitoreoProductosIntermedios']);
    Route::get('/monitoreo-productos-intermedios/{codigo_poa}', [ProductosIntermediosController::class, 'getMonitoreoProductosIntermedios']);
    Route::put('/modificar-producto-final', [ProductosFinalesController::class, 'updateProductosFinales']);
    Route::put('/modificar-producto-intermedio', [ProductosIntermediosController::class, 'updateProductoIntermedio']);
    Route::delete('/eliminar-productos-finales', [ProductosFinalesController::class, 'deleteNewProductosFinales']);
    Route::delete('/eliminar-productos-intermedios', [ProductosIntermediosController::class, 'deleteProductosIntermedios']);
    Route::delete('/eliminar-monitoreo-productos-finales', [ProductosFinalesController::class, 'deleteMonitoreoProductosFinales']);
    Route::delete('/eliminar-monitoreo-productos-intermedios', [ProductosIntermediosController::class, 'deleteMonitoreoProductosIntermedios']);
    
});

Route::prefix('actividades-insumos')->group(function () {
    Route::post('/insertarActividadesInsumos', [ActividadInsumoController::class, 'insertActividadInsumo']);
    Route::get('/actividades-insumos-by-poa/{codigo_poa}', [ActividadInsumoController::class, 'getActividadesInsumosByPoaId']);
    Route::get('/actividades-insumos-by-producto-and-poa/{codigo_producto_final}/{codigo_poa}', [ActividadInsumoController::class, 'getActividadesInsumosByProductoAndPoa']);
    Route::get('/actividades-por-producto-intermedio/{codigo_producto_intermedio}/{codigo_producto_final?}', [ActividadInsumoController::class, 'getActividadesByProductoIntermedio']);
    Route::put('/modificar-actividad', [ActividadInsumoController::class, 'updateActividad']);
    Route::delete('/eliminar-actividades-insumos', [ActividadInsumoController::class, 'deleteActividadesInsumos']);
});

Route::prefix('datos-complementarios')->group(function () {
    Route::get('/datos-complementarios-by-poa/{codigo_poa}', [DatosComplementariosController::class, 'getDatosComplementariosByPoa']);
    Route::post('/insertar-datos-complementarios', [DatosComplementariosController::class, 'insertDatosComplementarios']);
});

Route::prefix('mmr')->group(function () {
    Route::get('/obtenerTodasCategorizaciones', [CategorizacionController::class, 'getCategorizacionList']);
    Route::get('/obtenerTodosTiposIndicador', [TipoIndicadorController::class, 'getTiposIndicadoresList']);
    Route::get('/obtenerTodasUnidades', [UnidadesController::class, 'getUnidadesList']);
    Route::get('/obtenerTodosNivelesImpacto', [NivelImpactoController::class, 'getNivelesImpactoList']);
    Route::get('/obtenerTodosTiposRiesgo', [TiposRiesgoController::class, 'getTiposRiesgoList']);
});

Route::prefix('intervenciones-priorizadas')->group(function () {
    Route::get('/obtenerAldeasPriorizadas', [IntervencionesPriorizadasController::class, 'getAldeasPriorizadas']);
    Route::post('/insertarAldeas', [IntervencionesPriorizadasController::class, 'insertAldeas']);
    Route::get('/getIntervencionesPriorizadasByInstitucion/{codigo_institucion}', [IntervencionesPriorizadasController::class, 'getIntervencionesPriorizadasByInstitucion']);
    Route::get('/obtenerDepartamentos', [IntervencionesPriorizadasController::class, 'getAllDepartamentos']);
    Route::get('/obtenerMunicipios/{codigo_departamento}', [IntervencionesPriorizadasController::class, 'getMunicipiosByDepartamento']);
    Route::get('/obtenerAldeas/{codigo_municipio}', [IntervencionesPriorizadasController::class, 'getAldeasByMunicipio']);
    Route::post('/insertarIntervencionesPriorizadas', [IntervencionesPriorizadasController::class, 'insertIntervencionesPriorizadas']);
    Route::put('/modificarIntervencion', [IntervencionesPriorizadasController::class, 'updateIntervencionPriorizada']);
    Route::get('/obtenerTodasIntervenciones', [IntervencionesPriorizadasController::class, 'getAllIntervenciones']);
});

Route::prefix('fil')->group(function () {
    Route::get('/obtenerFil', [FilController::class, 'getAllFil']);
    Route::post('/insertarComentarioFil', [FilController::class, 'insertarComentarioFil']);
    Route::get('/obtenerGruposVulnerables', [FilController::class, 'getGruposVulnerables']);
});

Route::prefix('indicador')->group(function () {
    Route::get('/programas-inversion-genero', [Indicador::class, 'getProgramasConInversionEnGenero']);
    Route::get('/instituciones-vision-pais', [Indicador::class, 'getInstitucionesByVisionPais']);
    Route::get('/instituciones-resultado-peg', [Indicador::class, 'getInstitucionesByResultadoPEG']);
    Route::get('/instituciones-politica', [Indicador::class, 'getInstitucionesByPolitica']);
    Route::get('/instituciones-indicador', [Indicador::class, 'getInstitucionesByIndicadorResultado']);
    Route::get('/beneficiarios-pueblos', [Indicador::class, 'getBeneficiariosByPueblos']);
    Route::get('/beneficiarios-grupo-edad', [Indicador::class, 'getBeneficiariosByGrupoEdad']);
    Route::get('/instituciones-eje-estrategico', [Indicador::class, 'getInstitucionesByEjeEstrategico']);
    Route::get('/instituciones-gabinete', [Indicador::class, 'getInstitucionesByGabinete']);
    Route::get('/instituciones-an-ods', [Indicador::class, 'getInstitucionesByAnOds']);
    Route::get('/instituciones-intervencion', [Indicador::class, 'getInstitucionesByIntervencion']);
    Route::get('/actividades-producto', [Indicador::class, 'getActividadesByProductoIntermedio']);
});


//Api para generar archivo Word:
Route::get('/get-dictamen/{codigo_poa}', [DictamenController::class, 'generateDictamen']);
Route::get('/get-data/{codigo_poa}', [DictamenController::class, 'obtenerDatosPoa']);
Route::get('/get-matriz/{codigo_poa}', [MatrizPoaController::class, 'generarExcel']);
