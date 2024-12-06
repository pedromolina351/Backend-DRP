<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GabineteController extends Controller
{
    public function getAllGabinetes(){
        try{
            $resultados = DB::select('EXEC sp_GetAll_poa_t_gabinetes');
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los gabinetes: ' . $e->getMessage(),
            ], 500);
        }
    }

}
