<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreActividadInsumoRequest;
use App\Http\Requests\UpdateActividadRequest;
use Illuminate\Support\Facades\DB;

class ActividadInsumoController extends Controller
{
    public function insertActividadInsumo(StoreActividadInsumoRequest $request)
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
            // Inicializar un arreglo para registrar errores
            $errors = [];

            // Iterar sobre cada actividad_insumo en el arreglo
            foreach ($request->actividades_insumos as $actividadInsumo) {
                try {
                    $sql = "
                        EXEC sp_Insert_t_actividades_insumos 
                            @codigo_producto_final = :codigo_producto_final,
                            @actividad = :actividad,
                            @fecha_inicio = :fecha_inicio,
                            @fecha_fin = :fecha_fin,
                            @responsable = :responsable,
                            @medio_verificacion = :medio_verificacion,
                            @insumo_PACC = :insumo_PACC,
                            @insumo_no_PACC = :insumo_no_PACC,
                            @codigo_poa = :codigo_poa,
                            @codigo_objetivo_operativo = :codigo_objetivo_operativo;
                    ";

                    DB::statement($sql, [
                        'codigo_producto_final' => $actividadInsumo['codigo_producto_final'],
                        'actividad' => $actividadInsumo['actividad'],
                        'fecha_inicio' => $actividadInsumo['fecha_inicio'],
                        'fecha_fin' => $actividadInsumo['fecha_fin'],
                        'responsable' => $actividadInsumo['responsable'],
                        'medio_verificacion' => $actividadInsumo['medio_verificacion'],
                        'insumo_PACC' => $actividadInsumo['insumo_PACC'],
                        'insumo_no_PACC' => $actividadInsumo['insumo_no_PACC'],
                        'codigo_poa' => $request->codigo_poa,
                        'codigo_objetivo_operativo' => $actividadInsumo['codigo_objetivo_operativo'],
                    ]);
                } catch (\Exception $e) {
                    // Capturar errores por cada actividad_insumo fallida
                    $errors[] = [
                        'actividad_insumo' => $actividadInsumo,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            // Si hubo errores, retornarlos
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunas actividades_insumos no se pudieron procesar.',
                    'errors' => $errors,
                ], 207); // 207 Multi-Status indica éxito parcial
            }

            // Si todos fueron exitosos
            return response()->json([
                'success' => true,
                'message' => 'Todas las actividades_insumos fueron creadas con éxito.',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar las actividades_insumos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getActividadesInsumosByPoaId($codigo_poa){
        try {
            //Verificar si el codigo_poa existe en la tabla correspondiente
            $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $codigo_poa)->exists();

            if (!$poaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe.',
                ], 400); // Bad Request
            }

            $actividadesInsumos = DB::select('EXEC sp_GetById_actividades_insumosXPoa :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);

            $jsonField = $actividadesInsumos[0]->{'JSON_F52E2B61-18A1-11d1-B105-00805F49916B'} ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];

            return response()->json([
                'success' => true,
                'actividades_insumos' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las actividades_insumos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getActividadesInsumosByProductoAndPoa($codigo_producto_final, $codigo_poa)
    {
        try {
            // Verificar si el codigo_producto_final existe en la tabla correspondiente
            $productoExists = DB::table('t_productos_finales')->where('codigo_producto_final', $codigo_producto_final)->exists();

            if (!$productoExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_producto_final proporcionado no existe.',
                ], 400); // Bad Request
            }

            // Verificar si el codigo_poa existe en la tabla correspondiente
            $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $codigo_poa)->exists();

            if (!$poaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe.',
                ], 400); // Bad Request
            }

            $actividadesInsumos = DB::select('EXEC sp_GetById_actividades_insumosXProductoFinal_POA :codigo_producto_final, :codigo_poa', [
                'codigo_producto_final' => $codigo_producto_final,
                'codigo_poa' => $codigo_poa
            ]);

            $jsonField = $actividadesInsumos[0]->{'JSON_F52E2B61-18A1-11d1-B105-00805F49916B'} ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];

            return response()->json([
                'success' => true,
                'actividades_insumos' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las actividades_insumos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getActividadesByProductoIntermedio($codigo_producto_intermedio, $codigo_producto_final = null)
    {
        try {
            // Validar si el producto intermedio existe
            $productoIntermedioExiste = DB::table('dbo.t_productos_intermedios')
                ->where('codigo_producto_intermedio', $codigo_producto_intermedio)
                ->exists();
    
            if (!$productoIntermedioExiste) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto intermedio especificado no existe.',
                ], 404);
            }
    
            // Ejecutar el procedimiento almacenado
            $actividades = DB::select('EXEC dbo.sp_getById_t_actividades_x_producto_intermedio 
                @codigo_producto_intermedio = :codigo_producto_intermedio, 
                @codigo_producto_final = :codigo_producto_final', [
                'codigo_producto_intermedio' => $codigo_producto_intermedio,
                'codigo_producto_final' => $codigo_producto_final,
            ]);
    
            // Verificar si se encontraron actividades
            if (empty($actividades)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No se encontraron actividades para el producto intermedio especificado.',
                    'data' => [],
                ], 200);
            }

            //Organizar arreglo para que el campo FECHA_INICIO esté en minúsculas
            $actividades_arreglo = array_map(function ($actividad) {
                return [
                    'codigo_producto_final' => $actividad->codigo_producto_final,
                    'codigo_producto_intermedio' => $actividad->codigo_producto_intermedio,
                    'fecha_inicio' => $actividad->FECHA_INICIO,
                    'fecha_fin' => $actividad->FECHA_FIN,
                    'responsable' => $actividad->RESPONSABLE,
                    'medio_verificacion' => $actividad->MEDIO_VERIFICACION,
                    'actividad' => $actividad->actividad,
                    'codigo_actividad_insumo' => $actividad->codigo_actividad_insumo,
                ];
            }, $actividades);
    
            // Retornar actividades
            return response()->json([
                'success' => true,
                'message' => 'Actividades obtenidas correctamente.',
                'data' => $actividades_arreglo,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las actividades: ' . $e->getMessage(),
            ], 500);
        }
    }    

    public function updateActividad(UpdateActividadRequest $request)
    {
        try {
            // Validar los datos del request
            $validated = $request->validated();
    
            // Ejecutar el procedimiento almacenado para actualizar la actividad
            DB::statement('EXEC dbo.sp_Update_t_actividades_insumos 
                @codigo_actividad_insumo = :codigo_actividad_insumo,
                @codigo_producto_final = :codigo_producto_final,
                @actividad = :actividad,
                @fecha_inicio = :fecha_inicio,
                @fecha_fin = :fecha_fin,
                @responsable = :responsable,
                @medio_verificacion = :medio_verificacion,
                @insumo_PACC = :insumo_PACC,
                @insumo_no_PACC = :insumo_no_PACC,
                @codigo_poa = :codigo_poa,
                @codigo_objetivo_operativo = :codigo_objetivo_operativo', [
                'codigo_actividad_insumo' => $validated['codigo_actividad_insumo'],
                'codigo_producto_final' => $validated['codigo_producto_final'] ?? null,
                'actividad' => $validated['actividad'] ?? null,
                'fecha_inicio' => $validated['fecha_inicio'] ?? null,
                'fecha_fin' => $validated['fecha_fin'] ?? null,
                'responsable' => $validated['responsable'] ?? null,
                'medio_verificacion' => $validated['medio_verificacion'] ?? null,
                'insumo_PACC' => $validated['insumo_PACC'] ?? null,
                'insumo_no_PACC' => $validated['insumo_no_PACC'] ?? null,
                'codigo_poa' => $validated['codigo_poa'] ?? null,
                'codigo_objetivo_operativo' => $validated['codigo_objetivo_operativo'] ?? null,
            ]);
    
            // Responder con éxito
            return response()->json([
                'success' => true,
                'message' => 'Actividad actualizada correctamente.',
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Manejar errores generales
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la actividad: ' . $e->getMessage(),
            ], 500);
        }
    }    

}
