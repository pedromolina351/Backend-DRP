<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\InsertProductosFinalesRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreMonitoreoProductosFinalesRequest;

class ProductosFinalesController extends Controller
{
    public function insertProductosFinales(InsertProductosFinalesRequest $request)
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

            // Iterar sobre los productos_finales recibidos en el body
            foreach ($request->productos_finales as $producto) {
                try {
                    // Ejecutar el procedimiento almacenado para cada producto final
                    DB::statement('EXEC sp_Insert_t_productos_finales 
                        @objetivo_operativo = ?, 
                        @producto_final = ?, 
                        @indicador_producto_final = ?, 
                        @producto_final_primario = ?, 
                        @programa = ?, 
                        @fecha_inicio = ?,
                        @fecha_fin = ?,
                        @responsable = ?,
                        @medio_verificacion = ?,
                        @subprograma = ?, 
                        @proyecto = ?, 
                        @actividad = ?, 
                        @costo_total_aproximado = ?, 
                        @nombre_obra = ?, 
                        @estado = ?, 
                        @codigo_poa = ?', [
                        $producto['objetivo_operativo'],
                        $producto['producto_final'],
                        $producto['indicador_producto_final'],
                        $producto['producto_final_primario'],
                        $producto['programa'],
                        $producto['fecha_inicio'],
                        $producto['fecha_fin'],
                        $producto['responsable'],
                        $producto['medio_verificacion'],
                        $producto['subprograma'],
                        $producto['proyecto'],
                        $producto['actividad'],
                        $producto['costo_total_aproximado'],
                        $producto['nombre_obra'],
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
                    'message' => 'Algunos productos finales no se pudieron procesar.',
                    'errors' => $errors,
                ], 207); // 207 Multi-Status indica éxito parcial
            }

            return response()->json([
                'success' => true,
                'message' => 'Productos finales procesados con éxito.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los productos finales: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getProductosFinalesByPoa($codigo_poa)
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

            $productos_finales = DB::select('EXEC sp_GetById_t_productos_finales_by_poa_or_objetivo_operativo @codigo_poa = ?', [$codigo_poa]);

            return response()->json([
                'success' => true,
                'productos_finales' => $productos_finales,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos finales: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function insertMonitoreoProductosFinales(StoreMonitoreoProductosFinalesRequest $request)
    {
        try {
            $validated = $request->validated();
            $codigoPoa = $validated['codigo_poa'];
            $errores = [];
    
            foreach ($validated['listado_monitoreo'] as $monitoreo) {
                try {
                    // Ejecutar el primer procedimiento almacenado
                    $result = DB::select('EXEC mmr.sp_Insert_poa_t_poas_monitoreo_productos_finales 
                    @codigo_poa = :codigo_poa,
                    @codigo_producto_final = :codigo_producto_final,
                    @codigo_unidad_medida = :codigo_unidad_medida,
                    @codigo_tipo_indicador = :codigo_tipo_indicador,
                    @codigo_categorizacion = :codigo_categorizacion,
                    @medio_verificacion = :medio_verificacion,
                    @fuente_financiamiento = :fuente_financiamiento,
                    @meta_cantidad_anual = :meta_cantidad_anual,
                    @codigo_tipo_riesgo = :codigo_tipo_riesgo,
                    @codigo_nivel_impacto = :codigo_nivel_impacto,
                    @descripcion_riesgo = :descripcion_riesgo', [
                        'codigo_poa' => $codigoPoa,
                        'codigo_producto_final' => $monitoreo['codigo_producto_final'],
                        'codigo_unidad_medida' => $monitoreo['codigo_unidad_medida'],
                        'codigo_tipo_indicador' => $monitoreo['codigo_tipo_indicador'],
                        'codigo_categorizacion' => $monitoreo['codigo_categorizacion'],
                        'medio_verificacion' => $monitoreo['medio_verificacion'],
                        'fuente_financiamiento' => $monitoreo['fuente_financiamiento'],
                        'meta_cantidad_anual' => $monitoreo['meta_cantidad_anual'],
                        'codigo_tipo_riesgo' => $monitoreo['codigo_tipo_riesgo'],
                        'codigo_nivel_impacto' => $monitoreo['codigo_nivel_impacto'],
                        'descripcion_riesgo' => $monitoreo['descripcion_riesgo'] ?? null,
                    ]);
    
                    // Obtener el código del monitoreo insertado
                    $codigoMonitoreo = $result[0]->codigo_monitoreo_producto_final ?? null;
    
                    if (!$codigoMonitoreo) {
                        throw new \Exception('No se pudo obtener el código del monitoreo insertado.');
                    }
    
                    // Ejecutar el segundo procedimiento almacenado para insertar los meses
                    DB::statement('EXEC mmr.sp_Insert_t_monitoreo_meses 
                    @codigo_monitoreo_producto_final = :codigo_monitoreo_producto_final,
                    @lista_meses = :lista_meses,
                    @lista_cantidades = :lista_cantidades', [
                        'codigo_monitoreo_producto_final' => $codigoMonitoreo,
                        'lista_meses' => $monitoreo['lista_meses'],
                        'lista_cantidades' => $monitoreo['lista_cantidades'],
                    ]);
                } catch (\Exception $e) {
                    $errores[] = [
                        'monitoreo' => $monitoreo,
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
    
            return response()->json([
                'success' => true,
                'message' => 'Todos los monitoreos y meses asociados se insertaron con éxito.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al insertar el monitoreo del producto final: ' . $e->getMessage(),
            ], 500);
        }
    }    

    public function getMonitoreoProductosFinales($codigo_poa){
        try {
            // Verificar si el codigo_poa existe en la tabla correspondiente
            $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $codigo_poa)->exists();

            if (!$poaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe.',
                ], 400); // Bad Request
            }

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC mmr.sp_Get_t_poa_t_poas_monitoreo_productos_finales @codigo_poa = ?', [$codigo_poa]);

            // Decodificar el JSON desde la respuesta
            $jsonField = $result[0]->Monitoreos ?? null;

            if ($jsonField) {
                $data = json_decode($jsonField, true); // Convertir JSON en un array asociativo
                return response()->json([
                    'success' => true,
                    'monitoreo_productos_finales' => $data,
                ], 200);
            } else {
                $data = [];
                return response()->json([
                    'success' => false,
                    'monitoreo_productos_finales' => $data,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el monitoreo de productos finales: ' . $e->getMessage(),
            ], 500);
        }
    }
}
