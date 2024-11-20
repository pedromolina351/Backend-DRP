<?php

namespace App\Http\Controllers\Api;

use App\Models\Poa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StorePoaRequest;

class PoaController extends Controller
{
    public function getPoasList(){
        $poas = Poa::where('estado_poa', 1)->get();
        if ($poas->isEmpty()) {
            $data = [
                'status' => 204,
                'message' => 'No hay poas registrados',
            ];
            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'poas' => $poas,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getPoa($codigo_poa){
        $poa = Poa::where('codigo_poa', $codigo_poa)->where('estado_poa', 1)->first();
        if ($poa == null) {
            $data = [
                'status' => 404,
                'message' => 'Poa no encontrado',
            ];
            return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'poa' => $poa,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function createPoa(StorePoaRequest $request){
        $poa = Poa::create($request->validated());
        return response()->json($poa, 201, [], JSON_UNESCAPED_UNICODE);
    }
}
