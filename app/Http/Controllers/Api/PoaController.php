<?php

namespace App\Http\Controllers\Api;

use App\Models\Poa;
use App\Http\Controllers\Controller;
use App\Http\Requests\InsertPoaMainRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePoaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function insertPoaMain(InsertPoaMainRequest $request){
        Log::info('insertPoaMain function called');
        try {
            $poaMain = DB::statement('EXEC sp_Insert_poa_t_poas_main
                @codigo_institucion = :codigo_institucion,
                @codigo_programa = :codigo_programa,
                @codigo_usuario_creador = :codigo_usuario_creador,
                @codigo_politica = :codigo_politica,
                @codigo_objetivo_an_ods = :codigo_objetivo_an_ods,
                @codigo_meta_an_ods = :codigo_meta_an_ods,
                @codigo_indicador_an_ods = :codigo_indicador_an_ods,
                @codigo_objetivo_vp = :codigo_objetivo_vp,
                @codigo_meta_vp = :codigo_meta_vp,
                @codigo_gabinete = :codigo_gabinete,
                @codigo_eje_estrategico = :codigo_eje_estrategico,
                @codigo_objetivo_peg = :codigo_objetivo_peg,
                @codigo_resultado_peg = :codigo_resultado_peg,
                @codigo_indicador_resultado_peg = :codigo_indicador_resultado_peg',
                [
                    'codigo_institucion' => $request->codigo_institucion,
                    'codigo_programa' => $request->codigo_programa,
                    'codigo_usuario_creador' => $request->codigo_usuario_creador,
                    'codigo_politica' => $request->codigo_politica,
                    'codigo_objetivo_an_ods' => $request->codigo_objetivo_an_ods,
                    'codigo_meta_an_ods' => $request->codigo_meta_an_ods,
                    'codigo_indicador_an_ods' => $request->codigo_indicador_an_ods,
                    'codigo_objetivo_vp' => $request->codigo_objetivo_vp,
                    'codigo_meta_vp' => $request->codigo_meta_vp,
                    'codigo_gabinete' => $request->codigo_gabinete,
                    'codigo_eje_estrategico' => $request->codigo_eje_estrategico,
                    'codigo_objetivo_peg' => $request->codigo_objetivo_peg,
                    'codigo_resultado_peg' => $request->codigo_resultado_peg,
                    'codigo_indicador_resultado_peg' => $request->codigo_indicador_resultado_peg,
                ]
            );
    
            $data = [
                'status' => 201,
                'message' => 'Poa creado con éxito',
                'poa' => $poaMain
            ];
            return response()->json($data, 201, [], JSON_UNESCAPED_UNICODE);
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
