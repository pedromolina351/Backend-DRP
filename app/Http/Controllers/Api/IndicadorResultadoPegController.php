<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndicadorResultadoPegController extends Controller
{
    public function getIndicadoresResultadosPegByResultadoPeg($codigo_resultado_peg){
        try{
            $resultados = DB::select('EXEC sp_GetById_poa_t_indicador_resultado_pegXresultado_peg ?', [$codigo_resultado_peg]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los indicadores de resultado PEG por resultado PEG: ' . $e->getMessage(),
            ], 500);
        }
    }
}
