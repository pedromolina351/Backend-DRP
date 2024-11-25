<?php

namespace App\Http\Controllers\Api;

use App\Models\Modulo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreModuloRequest;

class moduloController extends Controller
{
    public function getModulosList(){
        $modulos = Modulo::where('estado', 1)->get();
        if ($modulos->isEmpty()) {
            $data = [
                'status' => 204,
                'message' => 'No hay modulos registrados',
            ];
            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'modulos' => $modulos,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getModulo($codigo_modulo){
        $modulo = Modulo::where('codigo_modulo', $codigo_modulo)->where('estado', 1)->first();
        if ($modulo == null) {
            $data = [
                'status' => 404,
                'message' => 'Modulo no encontrado',
            ];
            return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'modulo' => $modulo,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function createModulo(StoreModuloRequest $request){
        $modulo = Modulo::create($request->validated());
        $data = [
            'status' => 201,
            'message' => 'Modulo creado con éxito',
            'modulo' => $modulo,
        ];
        return response()->json($data, 201, [], JSON_UNESCAPED_UNICODE);
    }
   
}
