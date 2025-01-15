<?php

namespace App\Http\Controllers\Api;

use App\Models\Usuario;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuarioRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\DB;

class usuarioController extends Controller
{
    public function getUsuariosList(){
        try {
            $roles = DB::select('EXEC [usuarios].[sp_consultar_todos_usuarios]');
            $jsonField = $roles[0]->lista_usuarios ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los usuarios: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getUsuario($codigo_usuario){
        try {
            $usuario = DB::select('EXEC [usuarios].[sp_consultar_detalles_usuario] @codigo_usuario = :codigo_usuario', [
                'codigo_usuario' => $codigo_usuario,
            ]);
 
            return response()->json([
                'success' => true,
                'data' => $usuario,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los usuarios: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function createUsuario(StoreUsuarioRequest $request)
    {
        try {
            // Validar datos del request
            $validated = $request->validated();
    
            // Ejecutar el procedimiento almacenado para crear el usuario
            DB::statement('EXEC [usuarios].[sp_crear_usuario] 
                @primer_nombre = :primer_nombre,
                @segundo_nombre = :segundo_nombre,
                @primer_apellido = :primer_apellido,
                @segundo_apellido = :segundo_apellido,
                @dni = :dni,
                @correo_electronico = :correo_electronico,
                @telefono = :telefono,
                @codigo_rol = :codigo_rol,
                @codigo_institucion = :codigo_institucion,
                @super_user = :super_user,
                @usuario_drp = :usuario_drp,
                @estado = :estado,
                @password_hash = :password_hash,
                @url_img_perfil = :url_img_perfil', [
                'primer_nombre' => $validated['primer_nombre'],
                'segundo_nombre' => $validated['segundo_nombre'],
                'primer_apellido' => $validated['primer_apellido'],
                'segundo_apellido' => $validated['segundo_apellido'],
                'dni' => $validated['dni'],
                'correo_electronico' => $validated['correo_electronico'],
                'telefono' => $validated['telefono'],
                'codigo_rol' => $validated['codigo_rol'],
                'codigo_institucion' => $validated['codigo_institucion'],
                'super_user' => $validated['super_user'] ?? 0,
                'usuario_drp' => $validated['usuario_drp'] ?? 0,
                'estado' => $validated['estado'] ?? 1,
                'password_hash' => bcrypt($validated['password']),
                'url_img_perfil' => $validated['url_img_perfil']
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Usuario creado exitosamente.',
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
                'message' => 'Error al crear el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function updateUser(UpdateUserRequest $request)
    {
        try {
            // Validar datos del request
            $validated = $request->validated();
    
            // Construir el parámetro para el hash de la contraseña si se proporciona
            $passwordHash = isset($validated['password']) ? bcrypt($validated['password']) : null;
    
            // Ejecutar el procedimiento almacenado para actualizar el usuario
            DB::statement('EXEC [usuarios].[sp_actualizar_usuario] 
                @codigo_usuario = :codigo_usuario,
                @primer_nombre = :primer_nombre,
                @segundo_nombre = :segundo_nombre,
                @primer_apellido = :primer_apellido,
                @segundo_apellido = :segundo_apellido,
                @dni = :dni,
                @correo_electronico = :correo_electronico,
                @telefono = :telefono,
                @codigo_rol = :codigo_rol,
                @codigo_institucion = :codigo_institucion,
                @super_user = :super_user,
                @usuario_drp = :usuario_drp,
                @estado = :estado,
                @password_hash = :password_hash,
                @url_img_perfil = :url_img_perfil', [
                'codigo_usuario' => $validated['codigo_usuario'],
                'primer_nombre' => $validated['primer_nombre'] ?? null,
                'segundo_nombre' => $validated['segundo_nombre'] ?? null,
                'primer_apellido' => $validated['primer_apellido'] ?? null,
                'segundo_apellido' => $validated['segundo_apellido'] ?? null,
                'dni' => $validated['dni'] ?? null,
                'correo_electronico' => $validated['correo_electronico'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'codigo_rol' => $validated['codigo_rol'] ?? null,
                'codigo_institucion' => $validated['codigo_institucion'] ?? null,
                'super_user' => $validated['super_user'] ?? null,
                'usuario_drp' => $validated['usuario_drp'] ?? null,
                'estado' => $validated['estado'] ?? null,
                'password_hash' => $passwordHash ?? null,
                'url_img_perfil' => $validated['url_img_perfil'] ?? null
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Usuario actualizado correctamente.',
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
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteUser($codigo_usuario)
    {
        try {
            // Validar que el usuario exista
            if (!Usuario::where('codigo_usuario', $codigo_usuario)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no existe.',
                ], 404);
            }

            // Ejecutar el procedimiento almacenado para eliminar el usuario
            DB::statement('EXEC [usuarios].[sp_eliminar_usuario] @codigo_usuario = :codigo_usuario', [
                'codigo_usuario' => $codigo_usuario,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Usuario eliminado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el usuario: ' . $e->getMessage(),
            ], 500);
        }
    }

}
