<?php

namespace App\Http\Controllers\Api;

use App\Models\Programa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProgramaRequest;

class ProgramaController extends Controller
{
    public function getProgramasList(){
        $programas = Programa::all();
        if ($programas->isEmpty()) {
            $data = [
                'status' => 204,
                'message' => 'No hay programas registrados',
            ];
            return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'programas' => $programas,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function getPrograma($codigo_programa){
        $programa = Programa::where('codigo_programa', $codigo_programa)->first();
        if ($programa == null) {
            $data = [
                'status' => 404,
                'message' => 'Programa no encontrado',
            ];
            return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'programa' => $programa,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function createPrograma(StoreProgramaRequest $request){
        $programa = Programa::create($request->validated());
        return response()->json($programa, 201, [], JSON_UNESCAPED_UNICODE);
    }
}
