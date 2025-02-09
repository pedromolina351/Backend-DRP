<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\InsertObjetivosOperativosRequest;
use Illuminate\Support\Facades\DB;

class ObjetivosOperativosController extends Controller
{
    public function getObjetivosOperativosByPoa($codigo_poa)
    {
        // Verificar si el codigo_poa existe en la tabla correspondiente
        $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $codigo_poa)->exists();

        if (!$poaExists) {
            return response()->json([
                'success' => false,
                'message' => 'El codigo_poa proporcionado no existe.',
            ], 400); // Bad Request
        }
        try {
            $objetivos = DB::select('EXEC sp_GetAll_t_objetivos_operativos_by_poa @codigo_poa = ?', [$codigo_poa]);

            return response()->json([
                'success' => true,
                'message' => 'Objetivos operativos obtenidos con éxito.',
                'objetivos' => $objetivos,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los objetivos operativos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function insertObjetivosOperativos(InsertObjetivosOperativosRequest $request)
    {
        try {
            // Verificar si el codigo_poa existe en la tabla correspondiente
            $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $request->codigo_poa)->exists();

            if (!$poaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe.',
                ], 400); // Bad Request
            }else{
                DB::statement('EXEC [v2].[sp_Delete_t_poa_objetivos_operativos] @codigo_poa = ?', [$request->codigo_poa]);
            }

            $insert = DB::statement('EXEC [v2].[sp_Insert_t_poa_objetivos_operativos]
                @objetivos_operativos = ?, 
                @subprogramas_proyectos = ?, 
                @codigo_poa = ?', [
                $request->objetivos_operativo,
                $request->subprogramas_proyecto,
                $request->codigo_poa
            ]);

            if (!$insert) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al insertar los objetivos operativos.',
                ], 500);
            }
            return response()->json([
                'success' => true,
                'message' => 'Objetivos procesados con éxito.',
                'objetivos_operativos' => $insert,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los objetivos: ' . $e->getMessage(),
            ], 500);
        }
    }
}
