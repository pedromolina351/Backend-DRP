<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreComentarioFilRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FilController extends Controller
{
    public function getAllFil(Request $request)
    {
        // Obtener los parámetros de la solicitud, si existen
        $codigo_comentario = $request->query('codigo_comentario');
        $codigo_poa = $request->query('codigo_poa');

        try {
            // Construir la llamada al procedimiento almacenado
            $query = 'EXEC [FIL].[sp_obtener_comentarios_activos] @codigo_comentario = :codigo_comentario, @codigo_poa = :codigo_poa';
            $params = [
                'codigo_comentario' => $codigo_comentario,
                'codigo_poa' => $codigo_poa
            ];

            // Ejecutar el procedimiento almacenado
            $result = DB::select($query, $params);

            // Manejar el resultado
            if (count($result) > 0) {
                $jsonField = array_values((array)$result[0])[0];
                $data = json_decode($jsonField, true);

                return response()->json([
                    'success' => true,
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No se encontraron resultados para los parámetros proporcionados.'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function insertarComentarioFil(StoreComentarioFilRequest $request)
    {
        try {
            // Validar datos del request
            $validated = $request->validated();
    
            // Intentar ejecutar el procedimiento almacenado para eliminar los comentarios
            try {
                DB::statement('EXEC [FIL].[sp_eliminar_comentarios_poa] @codigo_poa = :codigo_poa', [
                    'codigo_poa' => $validated['codigo_poa']
                ]);
            } catch (\Exception $e) {
                // Si ocurre un error específico por "no tiene comentarios", continuar la ejecución
                if (str_contains($e->getMessage(), 'El código POA proporcionado no tiene comentarios')) {
                    // Log o manejar esta excepción si es necesario
                } else {
                    // Si es otro error, lanzar la excepción
                    throw $e;
                }
            }
    
            // Iterar sobre la lista de comentarios e insertarlos
            foreach ($validated['lista_comentarios'] as $comentario) {
                try {
                    DB::statement('EXEC [FIL].[sp_insertar_comentario]
                    @codigo_poa = :codigo_poa, 
                    @comentario = :comentario, 
                    @lineamientos = :lineamientos, 
                    @productos_intermedios = :productos_intermedios,
                    @grupos_vulnerables = :grupos_vulnerables', [
                    'codigo_poa' => $validated['codigo_poa'],
                    'comentario' => $comentario['comentario'],
                    'lineamientos' => $comentario['lineamientos'] ?? '',
                    'productos_intermedios' => $comentario['productos_intermedios'] ?? '',
                    'grupos_vulnerables' => $comentario['grupos_vulnerables'] ?? ''
                ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Error al insertar los comentarios: ' . $e->getMessage(),
                    ], 500);
                }
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Comentarios insertados correctamente.',
                'data' => $validated['lista_comentarios']
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al insertar los comentarios: ' . $e->getMessage(),
            ], 500);
        }
    }    
    
    public function getGruposVulnerables(){
        try {
            $query = 'EXEC [FIL].[sp_consultar_grupos_vulnerables]';
            $result = DB::select($query);

            return response()->json([
                'success' => true,
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }    
}
