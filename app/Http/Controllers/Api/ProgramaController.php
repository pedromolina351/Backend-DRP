<?php

namespace App\Http\Controllers\Api;

use App\Models\Programa;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProgramaRequest;
use Illuminate\Support\Facades\DB;

class ProgramaController extends Controller
{
    public function getProgramasList(){
        $programas = Programa::where('estado_programa', 1)->get();
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
        $data = [
            'status' => 201,
            'message' => 'Programa creado con éxito',
            'programa' => $programa,
        ];
        return response()->json($data, 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function deactivatePrograma($codigo_programa){
        try {
            // Validar si el POA existe y está activo
            $poa = DB::table('t_programas')->where('codigo_programa', $codigo_programa)->where('estado_programa', 1)->first();

            if (!$poa) {
                return response()->json([
                    'success' => false,
                    'message' => "El Programa con código $codigo_programa no existe o ya está desactivado."
                ], 404);
            }

            // Ejecutar el procedimiento almacenado
            DB::statement('EXEC sp_Delete_t_programas :codigo_programa', [
                'codigo_programa' => $codigo_programa
            ]);

            // Respuesta de éxito
            return response()->json([
                'success' => true,
                'message' => "El Programa con código $codigo_programa fue desactivado exitosamente."
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al intentar desactivar el Programa.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getProgramasByInstitucion($codigo_institucion)
    {
        try {
            $resultados = DB::select('EXEC sp_GetById_t_programasXInstitucion ?', [$codigo_institucion]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas: ' . $e->getMessage(),
            ], 500);
        }
    }
}
