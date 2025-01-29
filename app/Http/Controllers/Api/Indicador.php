<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Indicador extends Controller
{
    public function getProgramasConInversionEnGenero(){
        try{
            $programas = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_programas_con_inversion_en_genero]');

            return response()->json([
                'success' => true,
                'programas' => $programas ?? [],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos finales: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getInstitucionesByVisionPais(Request $request)
    {
        try {
            // Extraer los parámetros del request
            $codigoInstitucion = $request->input('codigo_institucion', null);
            $anios = $request->input('anios', null);
    
            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_instituciones_por_vision_pais] 
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);
    
            // Retornar la respuesta
            return response()->json([
                'success' => true,
                'message' => 'Consulta realizada exitosamente.',
                'data' => $result,
            ], 200);
    
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar las instituciones por visión país: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getInstitucionesByResultadoPEG(Request $request)
    {
        try {
            // Obtener los parámetros del request
            $codigoInstitucion = $request->input('codigo_institucion', null);
            $anios = $request->input('anios', null);

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_instituciones_por_resultado_peg] 
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);

            // Retornar la respuesta en JSON
            return response()->json([
                'success' => true,
                'message' => 'Consulta realizada exitosamente.',
                'data' => $result,
            ], 200);

        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'success' => false,
                'message' => 'Error al consultar las instituciones por resultado PEG: ' . $e->getMessage(),
            ], 500);
        }
    }

    
}

