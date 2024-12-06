<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EjeEstrategicoController extends Controller
{
    public function getEjeEstrategicoByGabinete($codigo_gabinete){
        try{
            $resultados = DB::select('EXEC sp_GetById_poa_t_eje_estrategicosXgabinete ?', [$codigo_gabinete]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los ejes estratégicos por gabinete: ' . $e->getMessage(),
            ], 500);
        }
    }
}
