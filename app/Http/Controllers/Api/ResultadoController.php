<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreResultadoRequest;
use App\Http\Requests\InsertPoaResultadosImpactosRequest;

class ResultadoController extends Controller
{

    public function insertResultado(StoreResultadoRequest $request)
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
            try {
                $sql = "
                        DECLARE @codigo_resultado INT;
                        EXEC [v2].[sp_Insert_t_resultados]
                            @resultado_institucional = :resultado_institucional,
                            @indicador_resultado_institucional = :indicador_resultado_institucional,
                            @codigo_poa = :codigo_poa;
                    ";

                DB::statement($sql, [
                    'resultado_institucional' => $request->resultado_institucional,
                    'indicador_resultado_institucional' => $request->indicador_resultado_institucional,
                    'codigo_poa' => $request->codigo_poa,
                ]);
            } catch (\Exception $e) {
                // Capturar errores por cada resultado fallido
                $errors[] = [
                    'error' => $e->getMessage(),
                ];
            }

            // Si hubo errores, retornarlos
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos resultados no se pudieron procesar.',
                    'errors' => $errors,
                ], 207); // 207 Multi-Status indica éxito parcial
            }

            // Si todos fueron exitosos
            return response()->json([
                'success' => true,
                'message' => 'Todos los resultados fueron creados con éxito.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los resultados: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function insertPoaResultadosImpactos(InsertPoaResultadosImpactosRequest $request)
    {
        try {
            // Ejecutar el procedimiento almacenado para cada combinación de impacto y resultado
            DB::statement('EXEC [v2].[sp_Insert_t_poa_t_poas_impactos_resultados]
                @codigo_poa = ?, 
                @codigos_resultado_final = ?, 
                @codigos_indicador_resultado_final = ?, 
                @codigos_resultado = ?', [
                $request->codigo_poa,
                $request->codigos_resultado_final,
                $request->codigos_indicador_resultado_final,
                $request->codigos_resultado,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Impactos y resultados procesados con éxito.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los impactos y resultados: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getResultadosByPoa($codigo_poa)
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

            $resultados = DB::select('EXEC sp_GetById_poa_t_poas_resultados @codigo_poa = ?', [$codigo_poa]);

            return response()->json([
                'success' => true,
                'resultados' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los resultados: ' . $e->getMessage(),
            ], 500);
        }
    }
}
