<?php

namespace App\Http\Controllers\Api;

use App\Models\Usuario;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUsuarioRequest;


class usuarioController extends Controller
{
    public function getUsuariosList(){
        $usuarios = Usuario::where('estado', 1)->get();
        if ($usuarios->isEmpty()) {
            $data = [
                'status' => 204,
                'message' => 'No hay usuarios registrados',
            ];
            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'usuarios' => $usuarios,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getUsuario($codigo_usuario){
        $usuario = Usuario::where('codigo_usuario', $codigo_usuario)->where('estado', 1)->first();
        if ($usuario == null) {
            $data = [
                'status' => 404,
                'message' => 'Usuario no encontrado',
            ];
            return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'usuario' => $usuario,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function createUsuario(StoreUsuarioRequest $request){
        $usuario = Usuario::create($request->validated());
        $data = [
            'status' => 201,
            'message' => 'Usuario creado con éxito',
            'usuario' => $usuario,
        ];
        return response()->json($data, 201, [], JSON_UNESCAPED_UNICODE);
    }


}
