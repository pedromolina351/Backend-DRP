<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObjetivoPegController extends Controller
{
    public function getObjetivoPegByEjeEstrategico($codigo_eje_estrategico){
        try{
            $resultados = DB::select('EXEC sp_GetById_poa_t_objetivos_pegXeje_estrategico ?', [$codigo_eje_estrategico]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los objetivos PEG por eje estratégico: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
