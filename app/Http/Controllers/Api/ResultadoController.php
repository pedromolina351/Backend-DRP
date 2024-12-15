<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreResultadoRequest;
use App\Http\Requests\InsertPoaResultadosImpactosRequest;

class ResultadoController extends Controller
{       

    public function insertResultado(StoreResultadoRequest $request)
    {
        try {
            $sql = "
                DECLARE @codigo_resultado INT;
                EXEC sp_Insert_t_resultados
                    @resultado_institucional = :resultado_institucional,
                    @indicador_resultado_institucional = :indicador_resultado_institucional,
                    @codigo_resultado = @codigo_resultado OUTPUT;
                SELECT @codigo_resultado AS codigo_resultado;
            ";
    
            $result = DB::select($sql, [
                'resultado_institucional' => $request->resultado_institucional,
                'indicador_resultado_institucional' => $request->indicador_resultado_institucional,
            ]);
    
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

    public function insertPoaResultadosImpactos(InsertPoaResultadosImpactosRequest $request)
    {
        try {
            // Verificar si el codigo_poa existe en la tabla correspondiente
            $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $request->codigo_poa)->exists();
    
            if (!$poaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe.',
                ], 400); // Bad Request
            }
    
            // Inicializar un arreglo para errores
            $errors = [];
    
            // Iterar sobre los impactos y resultados recibidos en el body
            foreach ($request->impactos as $impacto) {
                foreach ($request->resultados as $resultado) {
                    try {
                        // Ejecutar el procedimiento almacenado para cada combinación de impacto y resultado
                        DB::statement('EXEC sp_Insert_t_poa_t_poas_impactos_resultados 
                            @codigo_poa = ?, 
                            @codigo_resultado_final = ?, 
                            @codigo_indicador_resultado_final = ?, 
                            @codigo_resultado = ?', [
                            $request->codigo_poa,
                            $impacto['codigo_resultado_final'],
                            $impacto['codigo_indicador_resultado_final'],
                            $resultado['codigo_resultado']
                        ]);
                    } catch (\Exception $e) {
                        // Capturar el error y agregarlo al arreglo
                        $errors[] = [
                            'impacto' => $impacto,
                            'resultado' => $resultado,
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }
    
            // Verificar si hubo errores
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos impactos o resultados no se pudieron procesar.',
                    'errors' => $errors,
                ], 207); // 207 Multi-Status indica éxito parcial
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Impactos y resultados procesados con éxito.',
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los impactos y resultados: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    
    
}
