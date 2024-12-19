<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreActividadInsumoRequest;
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
                            @insumo_PACC = :insumo_PACC,
                            @insumo_no_PACC = :insumo_no_PACC,
                            @codigo_poa = :codigo_poa,
                            @codigo_objetivo_operativo = :codigo_objetivo_operativo;
                    ";

                    DB::statement($sql, [
                        'codigo_producto_final' => $actividadInsumo['codigo_producto_final'],
                        'actividad' => $actividadInsumo['actividad'],
                        'insumo_PACC' => $actividadInsumo['insumo_PACC'],
                        'insumo_no_PACC' => $actividadInsumo['insumo_no_PACC'],
                        'codigo_poa' => $request->codigo_poa,
                        'codigo_objetivo_operativo' => $request->codigo_objetivo_operativo,
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

}
