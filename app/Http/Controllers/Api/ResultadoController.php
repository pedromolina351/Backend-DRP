<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreResultadoRequest;

class ResultadoController extends Controller
{       

    public function insertResultado(StoreResultadoRequest $request)
    {
        try {
            // Declarar la variable de salida
            $sql = "
                DECLARE @codigo_resultado INT;
                EXEC sp_Insert_t_resultados
                    @resultado_institucional = :resultado_institucional,
                    @indicador_resultado_institucional = :indicador_resultado_institucional,
                    @codigo_resultado = @codigo_resultado OUTPUT;
                SELECT @codigo_resultado AS codigo_resultado;
            ";
    
            // Ejecutar la declaración SQL
            $result = DB::select($sql, [
                'resultado_institucional' => $request->resultado_institucional,
                'indicador_resultado_institucional' => $request->indicador_resultado_institucional,
            ]);
    
            // Capturar el valor del parámetro de salida
            $codigo_resultado = $result[0]->codigo_resultado;
    
            return response()->json([
                'success' => true,
                'message' => 'Resultado creado con éxito',
                'codigo_resultado' => $codigo_resultado,
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el resultado: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
