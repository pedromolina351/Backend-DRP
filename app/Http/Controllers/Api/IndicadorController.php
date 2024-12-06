<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndicadorController extends Controller
{
    public function getAllIndicadores(){
        try{
            $resultados = DB::select('EXEC sp_GetAll_poa_t_indicadores_an_ods');
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los indicadores: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getIndicadoresByMeta($codigo_meta){
        try{
            $resultados = DB::select('EXEC sp_GetById_poa_t_indicadores_an_odsXmeta_an_ods ?', [$codigo_meta]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los indicadores: ' . $e->getMessage(),
            ], 500);
        }
    }
}
