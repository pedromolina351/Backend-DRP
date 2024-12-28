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
            // Validar los datos del request
            $validated = $request->validate([
                'codigo_poa' => 'required|integer|exists:poa_t_poas,codigo_poa',
                'codigo_producto_final' => 'required|integer|exists:t_productos_finales,codigo_producto_final',
                'nombre_unidad_organizativa' => 'required|string|max:100',
                'nombre_responsable_unidad_organizativa' => 'required|string|max:100',
                'codigo_unidad_medida' => 'required|integer|exists:mmr.t_unidad_medida,codigo_unidad_medida',
                'codigo_tipo_indicador' => 'required|integer|exists:mmr.tipo_indicador,codigo_tipo_indicador',
                'codigo_categorizacion' => 'required|integer|exists:mmr.t_categorizacion,codigo_categorizacion',
                'medio_verificacion' => 'required|string|max:100',
                'fuente_financiamiento' => 'required|string|max:100',
                'meta_cantidad_anual' => 'required|integer|min:1',
                'codigo_tipo_riesgo' => 'required|integer|exists:mmr.t_tipo_riesgo,codigo_tipo_riesgo',
                'codigo_nivel_impacto' => 'required|integer|exists:mmr.t_nivel_impacto,codigo_nivel_impacto',
                'descripcion_riesgo' => 'nullable|string',
            ]);

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC mmr.sp_Insert_poa_t_poas_monitoreo_productos_finales 
            @codigo_poa = :codigo_poa,
            @codigo_producto_final = :codigo_producto_final,
            @nombre_unidad_organizativa = :nombre_unidad_organizativa,
            @nombre_responsable_unidad_organizativa = :nombre_responsable_unidad_organizativa,
            @codigo_unidad_medida = :codigo_unidad_medida,
            @codigo_tipo_indicador = :codigo_tipo_indicador,
            @codigo_categorizacion = :codigo_categorizacion,
            @medio_verificacion = :medio_verificacion,
            @fuente_financiamiento = :fuente_financiamiento,
            @meta_cantidad_anual = :meta_cantidad_anual,
            @codigo_tipo_riesgo = :codigo_tipo_riesgo,
            @codigo_nivel_impacto = :codigo_nivel_impacto,
            @descripcion_riesgo = :descripcion_riesgo', [
                'codigo_poa' => $validated['codigo_poa'],
                'codigo_producto_final' => $validated['codigo_producto_final'],
                'nombre_unidad_organizativa' => $validated['nombre_unidad_organizativa'],
                'nombre_responsable_unidad_organizativa' => $validated['nombre_responsable_unidad_organizativa'],
                'codigo_unidad_medida' => $validated['codigo_unidad_medida'],
                'codigo_tipo_indicador' => $validated['codigo_tipo_indicador'],
                'codigo_categorizacion' => $validated['codigo_categorizacion'],
                'medio_verificacion' => $validated['medio_verificacion'],
                'fuente_financiamiento' => $validated['fuente_financiamiento'],
                'meta_cantidad_anual' => $validated['meta_cantidad_anual'],
                'codigo_tipo_riesgo' => $validated['codigo_tipo_riesgo'],
                'codigo_nivel_impacto' => $validated['codigo_nivel_impacto'],
                'descripcion_riesgo' => $validated['descripcion_riesgo'],
            ]);

            // Retornar el resultado
            return response()->json([
                'success' => true,
                'message' => 'Monitoreo del producto final insertado con éxito.',
                'codigo_monitoreo_producto_final' => $result[0]->codigo_monitoreo_producto_final ?? null,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al insertar el monitoreo del producto final: ' . $e->getMessage(),
            ], 500);
        }
    }
}
