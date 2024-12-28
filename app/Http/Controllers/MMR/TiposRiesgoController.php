<?php

namespace App\Http\Controllers\MMR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TiposRiesgoController extends Controller
{
    public function getTiposRiesgoList()
    {
        try {
            $tipo_riesgo = DB::select('EXEC mmr.sp_GetAll_t_tipo_riesgo');
            $jsonField = $tipo_riesgo[0]->{'JSON_F52E2B61-18A1-11d1-B105-00805F49916B'} ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el listado de tipos de riesgo: ' . $e->getMessage(),
            ], 500);
        }
    }
}
