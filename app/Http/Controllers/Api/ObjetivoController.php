<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Objetivo;
use App\Http\Requests\StoreObjetivoRequest;

class ObjetivoController extends Controller
{
    public function getObjetivosList(){
        $objetivos = Objetivo::where('estado_objetivo_an_ods', 1)->get();
        if ($objetivos->isEmpty()) {
            $data = [
                'status' => 204,
                'message' => 'No hay objetivos registrados',
            ];
            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'objetivos' => $objetivos,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getObjetivo($codigo_objetivo){
        $objetivo = Objetivo::where('codigo_objetivo_an_ods', $codigo_objetivo)->where('estado_objetivo_an_ods', 1)->first();
        if ($objetivo == null) {
            $data = [
                'status' => 404,
                'message' => 'Objetivo no encontrado'
            ];
            return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'objetivo' => $objetivo,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function createObjetivo(StoreObjetivoRequest $request){
        $data = $request->validated();
        if(!isset($data['estado_objetivo_an_ods'])){
            $data['estado_objetivo_an_ods'] = 1;
        }
        $objetivo = Objetivo::create($data);
        $result = [
            'status' => 201,
            'message' => 'Objetivo creado con éxito',
            'objetivo' => $objetivo,
        ];
        return response()->json($result, 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function deactivateObjetivo($codigo_objetivo){
        $objetivo = Objetivo::where('codigo_objetivo_an_ods', $codigo_objetivo)->where('estado_objetivo_an_ods', 1)->first();
        if ($objetivo == null) {
            $data = [
                'status' => 404,
                'message' => 'Objetivo no encontrado'
            ];
            return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
        }
        $objetivo->estado_objetivo_an_ods = 0;
        $objetivo->save();
        $data = [
            'status' => 200,
            'message' => 'Objetivo eliminado con éxito',
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }
}
