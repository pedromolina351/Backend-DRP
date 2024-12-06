<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObjetivoVisionPaisController extends Controller
{
    public function getAllObjetivosVisionPais(){
        try{
            $resultados = DB::select('EXEC sp_GetAll_poa_t_obtjetivos_visión_pais');
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los objetivos de visión país: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getMetasVisionPaisByObjetivo($id){
        try{
            $resultados = DB::select('EXEC sp_GetById_poa_t_metas_vision_paisXobjetivo_vision_pais ?', [$id]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las metas de visión país por objetivo: ' . $e->getMessage(),
            ], 500);
        }
    }
}
