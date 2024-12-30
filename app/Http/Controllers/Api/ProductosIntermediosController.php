<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\InsertProductosIntermediosRequest;
use App\Http\Requests\StoreMonitoreoProductosIntermediosRequest;
use Illuminate\Support\Facades\DB;

class ProductosIntermediosController extends Controller
{
    public function insertProductosIntermedios(InsertProductosIntermediosRequest $request)
    {
        try {
            // Verificar si el codigo_poa existe en la tabla correspondiente
            $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $request->codigo_poa)->exists();

            if (!$poaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe.',
                ], 400); // Bad Request
            }
            // Inicializar un arreglo para errores
            $errors = [];
    
            // Iterar sobre los productos_intermedios recibidos en el body
            foreach ($request->productos_intermedios as $producto) {
                try {
                    // Ejecutar el procedimiento almacenado para cada producto intermedio
                    DB::statement('EXEC sp_Insert_t_productos_intermedios 
                        @objetivo_operativo = ?, 
                        @producto_intermedio = ?, 
                        @codigo_producto_final = ?, 
                        @indicador_producto_intermedio = ?, 
                        @producto_intermedio_primario = ?, 
                        @programa = ?, 
                        @subprograma = ?, 
                        @proyecto = ?, 
                        @actividad = ?, 
                        @fuente_financiamiento = ?, 
                        @ente_de_financiamiento = ?, 
                        @costro_aproximado = ?, 
                        @estado = ?, 
                        @codigo_poa = ?', [
                        $producto['objetivo_operativo'],
                        $producto['producto_intermedio'],
                        $producto['codigo_producto_final'],
                        $producto['indicador_producto_intermedio'],
                        $producto['producto_intermedio_primario'],
                        $producto['programa'],
                        $producto['subprograma'],
                        $producto['proyecto'],
                        $producto['actividad'],
                        $producto['fuente_financiamiento'],
                        $producto['ente_de_financiamiento'],
                        $producto['costro_aproximado'],
                        $producto['estado'],
                        $request->codigo_poa
                    ]);
                } catch (\Exception $e) {
                    // Capturar el error y agregarlo al arreglo
                    $errors[] = [
                        'producto' => $producto,
                        'error' => $e->getMessage(),
                    ];
                }
            }
    
            // Verificar si hubo errores
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos productos intermedios no se pudieron procesar.',
                    'errors' => $errors,
                ], 207); // 207 Multi-Status indica éxito parcial
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Productos intermedios procesados con éxito.',
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los productos intermedios: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getProductosIntermediosByPoa($codigo_poa)
    {
        try {
            // Verificar si el codigo_poa existe en la tabla correspondiente
            $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $codigo_poa)->exists();

            if (!$poaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe.',
                ], 400); // Bad Request
            }

            $productos_intermedios = DB::select('EXEC sp_GetById_t_productos_intermedios_by_poa_or_objetivo_operativo @codigo_poa = ?', [$codigo_poa]);

            return response()->json([
                'success' => true,
                'productos_intermedios' => $productos_intermedios,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos intermedios: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function insertMonitoreoProductosIntermedios(StoreMonitoreoProductosIntermediosRequest $request)
    {
        try {
            $validated = $request->validated();
            $codigoPoa = $validated['codigo_poa'];
            $errores = [];

            foreach ($validated['listado_monitoreo'] as $producto) {
                    try{
                        
                    $codigoMonitoreoProductoIntermedio = DB::select('EXEC mmr.sp_Insert_poa_t_poas_monitoreo_productos_intermedios 
                        @codigo_poa = :codigo_poa,
                        @codigo_producto_intermedio = :codigo_producto_intermedio,
                        @nombre_unidad_organizativa = :nombre_unidad_organizativa,
                        @nombre_responsable_unidad_organizativa = :nombre_responsable_unidad_organizativa,
                        @codigo_unidad_medida = :codigo_unidad_medida,
                        @codigo_tipo_indicador = :codigo_tipo_indicador,
                        @codigo_categorizacion = :codigo_categorizacion,
                        @medio_verificacion = :medio_verificacion,
                        @meta_cantidad_anual = :meta_cantidad_anual,
                        @codigo_tipo_riesgo = :codigo_tipo_riesgo,
                        @codigo_nivel_impacto = :codigo_nivel_impacto,
                        @descripcion_riesgo = :descripcion_riesgo', [
                        'codigo_poa' => $codigoPoa,
                        'codigo_producto_intermedio' => $producto['codigo_producto_intermedio'],
                        'nombre_unidad_organizativa' => $producto['nombre_unidad_organizativa'],
                        'nombre_responsable_unidad_organizativa' => $producto['nombre_responsable_unidad_organizativa'],
                        'codigo_unidad_medida' => $producto['codigo_unidad_medida'],
                        'codigo_tipo_indicador' => $producto['codigo_tipo_indicador'],
                        'codigo_categorizacion' => $producto['codigo_categorizacion'],
                        'medio_verificacion' => $producto['medio_verificacion'],
                        'meta_cantidad_anual' => $producto['meta_cantidad_anual'],
                        'codigo_tipo_riesgo' => $producto['codigo_tipo_riesgo'],
                        'codigo_nivel_impacto' => $producto['codigo_nivel_impacto'],
                        'descripcion_riesgo' => $producto['descripcion_riesgo'],
                    ]);
        
                    // Obtener el código del monitoreo insertado
                    $codigoMonitoreoProductoIntermedio = $codigoMonitoreoProductoIntermedio[0]->codigo_monitoreo_producto_intermedio ?? null;
        
                    if (!$codigoMonitoreoProductoIntermedio) {
                        throw new \Exception('Error al insertar el monitoreo del producto intermedio.');
                    }
        
                    // Insertar los meses asociados al producto intermedio
                    DB::statement('EXEC mmr.sp_Insert_t_monitoreo_meses_productos_intermedios 
                        @codigo_monitoreo_producto_intermedio = :codigo_monitoreo_producto_intermedio,
                        @lista_meses = :lista_meses,
                        @lista_cantidades = :lista_cantidades', [
                        'codigo_monitoreo_producto_intermedio' => $codigoMonitoreoProductoIntermedio,
                        'lista_meses' => $producto['lista_meses'],
                        'lista_cantidades' => $producto['lista_cantidades'],
                    ]);
                } catch (\Exception $e) {
                    $errores[] = [
                        'producto' => $producto,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            if (!empty($errores)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos monitoreos no se pudieron procesar.',
                    'errors' => $errores,
                ], 207); // 207 Multi-Status indica éxito parcial
            }
    
            // Respuesta de éxito
            return response()->json([
                'success' => true,
                'message' => 'Monitoreo de productos intermedios insertado con éxito.',
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al insertar el monitoreo de productos intermedios: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
