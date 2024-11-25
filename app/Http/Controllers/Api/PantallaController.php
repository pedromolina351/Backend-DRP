<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pantalla;
use App\Http\Requests\StorePantallaRequest;

class PantallaController extends Controller
{
    public function getAllPantallas(){
        $pantallas = Pantalla::where('estado', 1)->get();
        if ($pantallas->isEmpty()) {
            $data = [
                'status' => 204,
                'message' => 'No hay pantallas registradas',
            ];
            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getPantalla($id){
        $pantalla = Pantalla::where('codigo_pantalla', $id)->where('estado', 1)->first();
        if ($pantalla == null) {
            $data = [
                'status' => 404,
                'message' => 'Pantalla no encontrada',
            ];
            return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'pantalla' => $pantalla,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function createPantalla(StorePantallaRequest $request){
        $data = $request->validated();
        if(!isset($data['estado'])){
            $data['estado'] = 1;
        }
        $pantalla = Pantalla::create($data);
        $result = [
            'status' => 201,
            'message' => 'Pantalla creada con éxito',
            'pantalla' => $pantalla,
        ];
        return response()->json($result, 201, [], JSON_UNESCAPED_UNICODE);
    }

}
