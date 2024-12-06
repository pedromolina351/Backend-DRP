<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultadoPegController extends Controller
{
    public function getResultadosPegByObjetivoPeg($codigo_objetivo_peg){
        try{
            $resultados = DB::select('EXEC sp_GetById_poa_t_resultado_pegXobjetivo_peg ?', [$codigo_objetivo_peg]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los resultados PEG por objetivo PEG: ' . $e->getMessage(),
            ], 500);
        }
    }
}
