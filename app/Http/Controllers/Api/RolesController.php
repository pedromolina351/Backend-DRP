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
    public function getAllRoles()
    {
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

    public function getAllModulos()
    {
        try {
            $modulos = DB::select('EXEC [roles].[sp_consultar_accesos_modulos_pantallas]');

            return response()->json([
                'success' => true,
                'data' => $modulos,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los módulos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function createRole(StoreRoleRequest $request)
    {
        try {
            $validated = $request->validated();

            //recorrer el string de codigos_acceso_modulo y validar que existan en la base de datos
            $codigos_acceso_modulo = explode(',', $validated['codigos_acceso_modulo']);
            foreach ($codigos_acceso_modulo as $codigo_acceso_modulo) {
                $accesoExists = DB::table('roles.t_accesos_modulos')->where('codigo_acceso_modulo', $codigo_acceso_modulo)->exists();
                if (!$accesoExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El módulo con id : ' . $codigo_acceso_modulo . ' no existe en la base de datos.',
                    ], 400); // Bad Request
                }
            }

            // Crear el rol
            $resultadoRol = DB::select('EXEC [roles].[sp_crear_rol] 
                @nombre_rol = :nombre_rol,
                @descripcion_rol = :descripcion_rol,
                @estado_rol = :estado_rol,
                @editar = :editar', [
                'nombre_rol' => $validated['nombre_rol'],
                'descripcion_rol' => $validated['descripcion_rol'] ?? null,
                'estado_rol' => $validated['estado_rol'] ?? 1,
                'editar' => $validated['editar'] ?? 0,
            ]);

            // Obtener el ID del nuevo rol
            $nuevoRolId = $resultadoRol[0]->id_nuevo_rol ?? null;

            if (!$nuevoRolId) {
                throw new \Exception('No se pudo obtener el ID del rol recién creado.');
            }

            // Asignar los accesos al rol
            DB::select('EXEC [roles].[sp_asignar_actualizar_eliminar_accesos_de_rol]
                @codigo_rol = :codigo_rol,
                @codigos_acceso_modulo = :codigos_acceso_modulo,
                @estado_rol_acceso = :estado_rol_acceso', [
                'codigo_rol' => $nuevoRolId,
                'codigos_acceso_modulo' => $validated['codigos_acceso_modulo'],
                'estado_rol_acceso' => $validated['estado_rol'], // Asumimos que es el mismo estado del rol
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rol creado y accesos asignados correctamente.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el rol: ' . $e->getMessage(),
            ], 500);
        }
    }



    public function modificarEstadoRol(UpdateRoleStateRequest $request)
    {
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
                @estado_rol = :estado_rol,
                @editar = :editar', [
                'codigo_rol' => $validated['codigo_rol'],
                'nombre_rol' => $validated['nombre_rol'] ?? null,
                'descripcion_rol' => $validated['descripcion_rol'] ?? null,
                'estado_rol' => $validated['estado_rol'] ?? null,
                'editar' => $validated['editar'] ?? 0,
            ]);

            //asignar los accesos al rol
            DB::select('EXEC [roles].[sp_asignar_actualizar_eliminar_accesos_de_rol]
            @codigo_rol = :codigo_rol,
            @codigos_acceso_modulo = :codigos_acceso_modulo', [
                'codigo_rol' => $validated['codigo_rol'],
                'codigos_acceso_modulo' => $validated['codigos_acceso_modulo']
            ]);

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

    public function getInfoRol($codigo_rol)
    {
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

    public function deleteRole($codigo_rol){
        try {
            //verificar si el rol es existe
            $rolExists = DB::table('roles.t_roles')->where('codigo_rol', $codigo_rol)->exists();

            if (!$rolExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El rol proporcionado no existe.',
                ], 400); // Bad Request
            }

            $rol = DB::select('EXEC [roles].[sp_eliminar_rol_permanente] @codigo_rol = :codigo_rol', [
                'codigo_rol' => $codigo_rol,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rol eliminado correctamente.',
                'data' => $rol,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el rol: ' . $e->getMessage(),
            ], 500);
        }
    }
}
