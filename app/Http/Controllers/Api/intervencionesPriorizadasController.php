<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAldeaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class intervencionesPriorizadasController extends Controller
{
    public function getAldeasPriorizadas()
    {
        try {
            $aldeas = DB::select('EXEC [intervensiones_priorizadas].[sp_get_aldeas_priorizadas]');
            $jsonField = $aldeas[0]->{'JSON_F52E2B61-18A1-11d1-B105-00805F49916B'} ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];
            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron aldeas priorizadas.',
                ], 404); // Not Found
            }
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos finales: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function insertAldeas(StoreAldeaRequest $request)
    {
        try {
            // Obtener los datos validados del request
            $validated = $request->validated();
    
            // Ejecutar el procedimiento almacenado
            DB::statement('EXEC intervensiones_priorizadas.sp_insert_aldea_priorizada 
                @codigo_intervension_priorizada = :codigo_intervension_priorizada,
                @cod_departamento = :cod_departamento,
                @cod_municipio = :cod_municipio,
                @cod_aldea = :cod_aldea,
                @estado = :estado', [
                'codigo_intervension_priorizada' => $validated['codigo_intervension_priorizada'],
                'cod_departamento' => $validated['cod_departamento'],
                'cod_municipio' => $validated['cod_municipio'],
                'cod_aldea' => $validated['cod_aldea'],
                'estado' => $validated['estado'],
            ]);
    
            // Respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Aldea priorizada insertada exitosamente.',
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejar errores de validación
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Manejar errores generales
            return response()->json([
                'success' => false,
                'message' => 'Error al insertar la aldea priorizada: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getIntervencionesPriorizadasByInstitucion($codigo_institucion){
        try {
            $intervenciones = DB::select('EXEC [intervensiones_priorizadas].[sp_GetById_intervenciones_por_institucion] @codigo_institucion = :codigo_institucion', [
                'codigo_institucion' => $codigo_institucion,
            ]);

            $jsonField = $intervenciones[0]->{'JSON_F52E2B61-18A1-11d1-B105-00805F49916B'} ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron intervenciones priorizadas para la institución.',
                ], 404); // Not Found
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las intervenciones priorizadas: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getAllDepartamentos(){
        try {
            $departamentos = DB::select('EXEC [intervensiones_priorizadas].[sp_GetAll_t_glo_departamentos]');

            $jsonField = $departamentos[0]->{'JSON_F52E2B61-18A1-11d1-B105-00805F49916B'} ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron departamentos.',
                ], 404); // Not Found
            }
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos finales: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getMunicipiosByDepartamento($codigo_departamento){
        try {
            $municipios = DB::select('EXEC [intervensiones_priorizadas].[sp_GetById_t_glo_municipiosXDepartamento] @cod_departamento = :cod_departamento', [
                'cod_departamento' => $codigo_departamento,
            ]);

            $jsonField = $municipios[0]->{'JSON_F52E2B61-18A1-11d1-B105-00805F49916B'} ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron municipios.',
                ], 404); // Not Found
            }
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos finales: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
