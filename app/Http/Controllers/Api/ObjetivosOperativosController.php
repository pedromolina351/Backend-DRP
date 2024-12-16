<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\InsertObjetivosOperativosRequest;
use Illuminate\Support\Facades\DB;

class ObjetivosOperativosController extends Controller
{
    public function getObjetivosOperativosByPoa($codigo_poa){
        // Verificar si el codigo_poa existe en la tabla correspondiente
        $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $codigo_poa)->exists();

        if (!$poaExists) {
            return response()->json([
                'success' => false,
                'message' => 'El codigo_poa proporcionado no existe.',
            ], 400); // Bad Request
        }
        try {
            $objetivos = DB::select('EXEC sp_GetAll_t_objetivos_operativos_by_poa @codigo_poa = ?', [$codigo_poa]);

            return response()->json([
                'success' => true,
                'message' => 'Objetivos operativos obtenidos con éxito.',
                'objetivos' => $objetivos,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los objetivos operativos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function insertObjetivosOperativos(InsertObjetivosOperativosRequest $request)
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
    
            // Iterar sobre los objetivos recibidos en el body
            foreach ($request->listado_objetivos as $objetivo) {
                try {
                    // Ejecutar el procedimiento almacenado para cada objetivo
                    DB::statement('EXEC sp_Insert_t_objetivos_operativos 
                        @objetivo_operativo = ?, 
                        @subprograma_proyecto = ?, 
                        @codigo_poa = ?', [
                        $objetivo['objetivo_operativo'],
                        $objetivo['subprograma_proyecto'],
                        $request->codigo_poa
                    ]);
                } catch (\Exception $e) {
                    // Capturar el error y agregarlo al arreglo
                    $errors[] = [
                        'objetivo' => $objetivo,
                        'error' => $e->getMessage(),
                    ];
                }
            }
    
            // Verificar si hubo errores
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos objetivos no se pudieron procesar.',
                    'errors' => $errors,
                ], 207); // 207 Multi-Status indica éxito parcial
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Objetivos procesados con éxito.',
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los objetivos: ' . $e->getMessage(),
            ], 500);
        }
    }
}
