<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\UpdateRoleStateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    public function getAllRoles(){
        try {
            $roles = DB::select('EXEC [roles].[sp_consultar_roles]');

            return response()->json([
                'success' => true,
                'data' => $roles,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los roles: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function createRole(StoreRoleRequest $request)
    {
        try {
            // Validar los datos del request
            $validated = $request->validated();
    
            // Ejecutar el procedimiento almacenado para crear el rol
            $result = DB::select('EXEC [roles].[sp_crear_rol] 
                @nombre_rol = :nombre_rol, 
                @descripcion_rol = :descripcion_rol, 
                @estado_rol = :estado_rol', [
                'nombre_rol' => $validated['nombre_rol'],
                'descripcion_rol' => $validated['descripcion_rol'] ?? null,
                'estado_rol' => $validated['estado_rol'] ?? 1, // Valor predeterminado: Activo
            ]);
    
            // Verificar si el rol fue creado y obtener el ID
            $id_nuevo_rol = $result[0]->id_nuevo_rol ?? null;
    
            if (!$id_nuevo_rol) {
                throw new \Exception('No se pudo crear el rol.');
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Rol creado exitosamente.',
                'data' => [
                    'id_nuevo_rol' => $id_nuevo_rol,
                    'nombre_rol' => $validated['nombre_rol'],
                    'descripcion_rol' => $validated['descripcion_rol'] ?? null,
                    'estado_rol' => $validated['estado_rol'] ?? 1,
                ],
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
                'message' => 'Error al crear el rol: ' . $e->getMessage(),
            ], 500);
        }
    }
    

    public function modificarEstadoRol(UpdateRoleStateRequest $request){
        try {
            // Construir la llamada al procedimiento almacenado
            $query = 'EXEC [roles].[sp_actualizar_rol] @codigo_rol = :codigo_rol, @estado_rol = :estado_rol';
            $params = [
                'codigo_rol' => $request['codigo_rol'],
                'estado_rol' => $request['estado_rol']
            ];

            // Ejecutar el procedimiento almacenado
            $result = DB::select($query, $params);

            // Manejar el resultado
            if (count($result) > 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Estado del rol actualizado correctamente.'
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
    
            // Ejecutar el procedimiento almacenado para actualizar el rol
            DB::select('EXEC [roles].[sp_actualizar_rol] 
                @codigo_rol = :codigo_rol, 
                @nombre_rol = :nombre_rol, 
                @descripcion_rol = :descripcion_rol, 
                @estado_rol = :estado_rol', [
                'codigo_rol' => $validated['codigo_rol'],
                'nombre_rol' => $validated['nombre_rol'] ?? null,
                'descripcion_rol' => $validated['descripcion_rol'] ?? null,
                'estado_rol' => $validated['estado_rol'] ?? null,
            ]);
    
            // Iterar sobre el listado de accesos y asignarlos
            foreach ($validated['listado_accesos'] as $acceso) {
                DB::statement('EXEC [roles].[sp_asignar_acceso_a_rol] 
                    @codigo_rol = :codigo_rol, 
                    @codigo_acceso_modulo = :codigo_acceso_modulo, 
                    @estado_rol_acceso = :estado_rol_acceso', [
                    'codigo_rol' => $validated['codigo_rol'],
                    'codigo_acceso_modulo' => $acceso['codigo_acceso_modulo'],
                    'estado_rol_acceso' => $acceso['estado_rol_acceso'],
                ]);
            }
    
            // Retornar respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Rol actualizado correctamente con los accesos asignados.',
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
                'message' => 'Error al obtener los accesos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getInfoRol($codigo_rol){
        try {
            $rol = DB::select('EXEC [roles].[sp_consultar_detalles_rol] @codigo_rol = :codigo_rol', [
                'codigo_rol' => $codigo_rol,
            ]);

            $jsonField = $rol[0]->detalles_rol ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron los detalles para el rol especificado.',
                ], 404); // Not Found
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los detalles del rol: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
