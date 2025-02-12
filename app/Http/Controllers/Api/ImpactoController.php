<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreImpactoRequest;

class ImpactoController extends Controller
{
    public function insertImpactos(StoreImpactoRequest $request)
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
                    EXEC V2.sp_Insert_t_poa_t_poas_impactos 
                        @codigo_poa = :codigo_poa, 
                        @codigos_resultado_final = :codigos_resultado_final, 
                        @codigos_indicador_resultado_final = :codigos_indicador_resultado_final;
                ";

                DB::statement($sql, [
                    'codigo_poa' => $request->codigo_poa,
                    'codigos_resultado_final' => $request->codigos_resultado_final,
                    'codigos_indicador_resultado_final' => $request->codigos_indicador_resultado_final
                ]);
            } catch (\Exception $e) {
                // Capturar errores por cada impacto fallido
                $errors[] = [
                    'error' => $e->getMessage(),
                ];
            }
    
            // Si hubo errores, retornarlos
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos impactos no se pudieron procesar.',
                    'errors' => $errors,
                ], 207); // 207 Multi-Status indica éxito parcial
            }
    
            // Si todos fueron exitosos
            return response()->json([
                'success' => true,
                'message' => 'Todos los impactos fueron creados con éxito.',
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los impactos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getImpactosByPoaId($codigo_poa){
        try {
            // Verificar si el codigo_poa existe en la tabla correspondiente
            $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $codigo_poa)->exists();

            if (!$poaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe.',
                ], 400); // Bad Request
            }
            $impactos = DB::select('EXEC sp_GetAll_t_poa_t_poas_impactos_by_poa @codigo_poa = ?', [$codigo_poa]);

            if(empty($impactos)){
                return response()->json([
                    'success' => true,
                    'message' => 'No se encontraron impactos para el POA proporcionado.',
                ], 404); // Not Found
            }

            return response()->json([
                'success' => true,
                'message' => 'Impactos obtenidos con éxito.',
                'impactos' => $impactos,
            ], 200);

        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los impactos: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
