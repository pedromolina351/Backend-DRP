<?php

namespace App\Http\Controllers\Api;

use App\Models\Usuario;
use App\Http\Controllers\Controller;
use App\Http\Requests\changePasswordRequest;
use App\Http\Requests\LoginRequest;
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

    public function userLogin(LoginRequest $request)
    {
        try {
            // Validar los datos del request
            $validated = $request->validated();
    
            // Ejecutar el procedimiento almacenado para validar el inicio de sesión
            $result = DB::select('EXEC [usuarios].[sp_inicio_sesion] 
                @correo_electronico = :correo_electronico,
                @password_hash = :password_hash', [
                'correo_electronico' => $validated['correo_electronico'],
                'password_hash' => $validated['password'] ?? null, // Se envía la contraseña en texto plano ya que el SP maneja la validación
            ]);
    
            // Verificar si la consulta devolvió un resultado
            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no encontrado o credenciales inválidas.',
                ], 401);
            }
    
            // Extraer los datos del usuario
            $userData = $result[0];
    
            // Verificar si el estado es 'error'
            if ($userData->Estado === 'error') {
                return response()->json([
                    'success' => false,
                    'message' => $userData->Mensaje,
                ], 401);
            }
    
            // Retornar respuesta exitosa con los datos del usuario
            return response()->json([
                'success' => true,
                'message' => $userData->Mensaje,
                'data' => [
                    'codigo_usuario' => $userData->codigo_usuario,
                    'nombres' => $userData->Nombres,
                    'apellidos' => $userData->Apellidos,
                    'codigo_rol' => $userData->codigo_rol,
                    'codigo_institucion' => $userData->codigo_institucion,
                    'super_user' => $userData->super_user,
                    'usuario_drp' => $userData->usuario_drp,
                    'firstLogin' => $userData->firstLogin,
                    'rol_editar' => $userData->rol_editar,
                    'url_img_perfil' => html_entity_decode($userData->url_img_perfil),
                    'password_hash' => $userData->password_hash
                ]
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
                'message' => 'Error al iniciar sesión: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            // Validar los datos del request
            $validated = $request->validated();


            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC usuarios.sp_reiniciar_contrasenia 
                @correo_electronico = :correo_electronico,
                @nuevo_password_hash = :nuevo_password_hash', [
                'correo_electronico' => $validated['correo_electronico'],
                'nuevo_password_hash' => $validated['nuevo_password'],
            ]);

            // Validar respuesta del procedimiento almacenado
            if (!isset($result[0]->Estado) || $result[0]->Estado !== 'success') {
                return response()->json([
                    'success' => false,
                    'message' => $result[0]->Mensaje ?? 'Error al actualizar la contraseña.'
                ], 400);
            }

            // Retornar respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Contraseña actualizada exitosamente.',
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
                'message' => 'Error al cambiar la contraseña: ' . $e->getMessage(),
            ], 500);
        }
    }
}
