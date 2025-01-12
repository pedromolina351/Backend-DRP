<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function activateRole($codigo_rol){
        try {
            // Construir la llamada al procedimiento almacenado
            $query = 'EXEC [roles].[sp_activar_rol_y_accesos] @codigo_rol = :codigo_rol';
            $params = [
                'codigo_rol' => $codigo_rol
            ];

            // Ejecutar el procedimiento almacenado
            $result = DB::select($query, $params);

            // Manejar el resultado
            if (count($result) > 0) {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
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

    public function updateRole(UpdateRoleRequest $request)
    {
        try {
            // Validar datos del request
            $validated = $request->validated();
    
            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [roles].[sp_actualizar_rol] 
                @codigo_rol = :codigo_rol, 
                @nombre_rol = :nombre_rol, 
                @descripcion_rol = :descripcion_rol, 
                @estado_rol = :estado_rol', [
                'codigo_rol' => $validated['codigo_rol'],
                'nombre_rol' => $validated['nombre_rol'] ?? null,
                'descripcion_rol' => $validated['descripcion_rol'] ?? null,
                'estado_rol' => $validated['estado_rol'] ?? null,
            ]);
    
            // Retornar respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado correctamente.',
                'data' => $result[0] ?? null,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el rol: ' . $e->getMessage(),
            ], 500);
        }
    }

    
    public function getAccesosRol($codigo_rol)
    {
        try {
            $accesos = DB::select('EXEC [roles].[sp_consultar_accesos_de_rol] @codigo_rol = :codigo_rol', [
                'codigo_rol' => $codigo_rol,
            ]);

            $jsonField = $accesos[0]->accesos_rol ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron los accesos para el rol especificado.',
                ], 404); // Not Found
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los acceos: ' . $e->getMessage(),
            ], 500);
        }
    }

    
}
