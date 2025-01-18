<?php

namespace App\Http\Controllers\MMR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnidadesController extends Controller
{
    public function getUnidadesList()
    {
        try {
            $unidades_medida = DB::select('EXEC mmr.sp_GetAll_t_unidad_medida');
            $jsonField = $unidades_medida[0]->JSONResult ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el listado de unidades de medida: ' . $e->getMessage(),
            ], 500);
        }
    }
}
