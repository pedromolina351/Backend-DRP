<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultadoFinalController extends Controller
{
    public function getAllResultadoFinal(){
        try{
            $resultados = DB::select('EXEC sp_GetAll_t_resultado_final');
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los resultados finales: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getAllIndicadoresByResultadoFinalId($resultadoFinalId){
        try{
            $resultados = DB::select('EXEC sp_GetById_t_indicador_resultado_final ?', [$resultadoFinalId]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los indicadores por resultado final: ' . $e->getMessage(),
            ], 500);
        }
    }
}
