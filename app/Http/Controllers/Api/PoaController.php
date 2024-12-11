<?php

namespace App\Http\Controllers\Api;

use App\Models\Poa;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePoaRequest;
use Illuminate\Http\Request;

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
        $data = [
            'status' => 201,
            'message' => 'Poa creado con éxito',
            'poa' => $poa,
        ];
        return response()->json($data, 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function deactivatePoa($codigo_poa){
        try {
            // Validar si el POA existe y está activo
            $poa = DB::table('poa_t_poas')->where('codigo_poa', $codigo_poa)->where('estado_poa', 1)->first();

            if (!$poa) {
                return response()->json([
                    'success' => false,
                    'message' => "El POA con código $codigo_poa no existe o ya está desactivado."
                ], 404);
            }

            // Ejecutar el procedimiento almacenado
            DB::statement('EXEC sp_Delete_poa_t_poas :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);

            // Respuesta de éxito
            return response()->json([
                'success' => true,
                'message' => "El POA con código $codigo_poa fue desactivado exitosamente."
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al intentar desactivar el POA.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function getPoasByInstitution(Request $request)
    {
        $validatedData = $request->validate([
            'codigo_institucion' => 'required|integer',
            'codigo_usuario' => 'required|integer',
        ]);

        $codigoInstitucion = $validatedData['codigo_institucion'];
        $codigoUsuario = $validatedData['codigo_usuario'];

        try {
            // Ejecutar el procedimiento almacenado
            $poas = DB::select(
                'EXEC sp_GetById_poa_t_poasXinstitucion @codigo_institucion = ?, @codigo_usuario = ?',
                [$codigoInstitucion, $codigoUsuario]
            );

            if (empty($poas)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron POAs para la institución especificada.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $poas,
            ]);
        } catch (\Exception $e) {
            // Manejar errores
            return response()->json([
                'success' => false,
                'message' => 'Error al ejecutar el procedimiento almacenado.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
