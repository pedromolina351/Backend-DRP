<?php

namespace App\Http\Controllers\Api;

use App\Models\Institucion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreInstitucionRequest;

class InstitucionController extends Controller
{
    public function getInstitucionesList(){
        $instituciones = Institucion::where('estado_institucion', 1)
        ->orderBy('nombre_institucion', 'asc')->get();
        if ($instituciones->isEmpty()) {
            $data = [
                'status' => 204,
                'message' => 'No hay instituciones registradas',
            ];
            return response()->json($data, 200);
        }
        $data = [
            'status' => 200,
            'instituciones' => $instituciones,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    } 

    public function getInstitucion($codigo_institucion){
        $institucion = Institucion::where('codigo_institucion', $codigo_institucion)->where('estado_institucion', 1)->first();
        if ($institucion == null) {
            $data = [
                'status' => 404,
                'message' => 'Institucion no encontrada',
            ];
            return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
        }
        $data = [
            'status' => 200,
            'institucion' => $institucion,
        ];
        return response()->json($data, 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function createInstitucion(StoreInstitucionRequest $request){
        $institucion = Institucion::create($request->validated());
        $data = [
            "status" => 201,
            "message" => "Institución creada con éxito",
            "institucion" => $institucion,
        ];
        return response()->json($data, 201, [], JSON_UNESCAPED_UNICODE);
    }
}
