<?php

namespace App\Http\Controllers\Api;

use App\Models\Rol;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRolRequest;

class RolController extends Controller
{
    public function getRolesList(){
        $roles = Rol::where('estado', 1)->get();
        if ($roles->isEmpty()) {
            $data = [
                'status' => 204,
                'message' => 'No hay roles registrados',
            ];
            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'roles' => $roles,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getRol($codigo_rol){
        $rol = Rol::where('codigo_rol', $codigo_rol)->where('estado', 1)->first();
        if ($rol == null) {
            $data = [
                'status' => 404,
                'message' => 'Rol no encontrado',
            ];
            return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'rol' => $rol,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function createRol(StoreRolRequest $request){
        $data = $request->validated();
        if(!isset($data['estado'])){
            $data['estado'] = 1;
        }
        $rol = Rol::create($data);
        $result = [
            'status' => 201,
            'message' => 'Rol creado con éxito',
            'rol' => $rol,
        ];
        return response()->json($result, 201, [], JSON_UNESCAPED_UNICODE);
    }
}
