<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Politica;
use App\Http\Requests\StorePoliticaRequest;

class PoliticaController extends Controller
{
    public function getPoliticasList(){
        $politicas = Politica::where('estado_politica_publica', 1)->get();
        if ($politicas->isEmpty()) {
            $data = [
                'status' => 204,
                'message' => 'No hay políticas públicas registradas',
            ];
            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'politicas' => $politicas,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getPolitica($codigo_politica_publica){
        $politica = Politica::where('codigo_politica_publica', $codigo_politica_publica)->where('estado_politica_publica', 1)->first();
        if ($politica == null) {
            $data = [
                'status' => 404,
                'message' => 'Política pública no encontrada',
            ];
            return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'politica' => $politica,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function createPolitica(StorePoliticaRequest $request){
        $data = $request->validated();
        if(!isset($data['estado_politica_publica'])){
            $data['estado_politica_publica'] = 1;
        }
        $politica = Politica::create($data);
        $result = [
            'status' => 201,
            'message' => 'Política pública creada con éxito',
            'politica' => $politica,
        ];
        return response()->json($result, 201, [], JSON_UNESCAPED_UNICODE);
    }

}
