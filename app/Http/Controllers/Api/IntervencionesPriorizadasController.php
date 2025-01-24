<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAldeaRequest;
use App\Http\Requests\StoreIntervencionesPriorizadasRequest;
use App\Http\Requests\UpdateIntervencionPriorizadaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntervencionesPriorizadasController extends Controller
{
    public function getAldeasPriorizadas(Request $request)
    {
        try {
            // Obtener los parámetros opcionales
            $codigo_aldea_intervenida = $request->query('codigo_aldea_intervenida');
            $codigo_intervension_priorizada = $request->query('codigo_intervension_priorizada');
    
            // Verificar si ambos parámetros están presentes
            if ($codigo_aldea_intervenida && $codigo_intervension_priorizada) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se puede proporcionar uno de los dos parámetros: codigo_aldea_intervenida o codigo_intervension_priorizada.',
                ], 400);
            }else if($codigo_aldea_intervenida){
                //Verificar que el codigo_aldea_intervenida exista
                $aldeaExists = DB::table('intervensiones_priorizadas.aldeas_intervenidas')->where('codigo_aldea_intervenida', $codigo_aldea_intervenida)->exists();
                if (!$aldeaExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El codigo_aldea_intervenida proporcionado no existe.',
                    ], 400); // Bad Request
                }
            }else if($codigo_intervension_priorizada){
                //Verificar que el codigo_intervension_priorizada
                $intervencionExists = DB::table('intervensiones_priorizadas.intervensiones_priorizadas')->where('codigo_intervension_priorizada', $codigo_intervension_priorizada)->exists();
                if (!$intervencionExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El codigo_intervension_priorizada proporcionado no existe.',
                    ], 400); // Bad Request
                }
            }
    
            // Ejecutar el procedimiento almacenado con los parámetros adecuados
            $aldeas = DB::select('EXEC [intervensiones_priorizadas].[sp_get_aldeas_priorizadas] @codigo_aldea_intervenida = ?, @codigo_intervension_priorizada = ?', [
                $codigo_aldea_intervenida,
                $codigo_intervension_priorizada,
            ]);
    
            // Validar si hay resultados
        // Verificar si el resultado contiene datos
        if (!empty($aldeas) && isset($aldeas[0]->aldeas_intervenidas)) {
            $jsonField = $aldeas[0]->aldeas_intervenidas;
            $data = json_decode($jsonField, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                return response()->json([
                    'success' => true,
                    'aldeas_priorizadas' => $data['aldeas_intervenidas'], // Acceder al nodo raíz del JSON
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al decodificar el JSON devuelto por el procedimiento almacenado.',
                    'json_error' => json_last_error_msg(),
                    'json_data' => $jsonField,
                ], 500);
            }
        }
    
            return response()->json([
                'success' => true,
                'aldeas_priorizadas' => $aldeas[0]->{'JSON_F52E2B61-18A1-11d1-B105-00805F49916B'},
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las aldeas priorizadas: ' . $e->getMessage(),
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

    public function getIntervencionesPriorizadasByInstitucion($codigo_institucion)
    {
        try {
            $intervenciones = DB::select('EXEC [intervensiones_priorizadas].[sp_GetById_intervenciones_por_institucion] @codigo_institucion = :codigo_institucion', [
                'codigo_institucion' => $codigo_institucion,
            ]);

            $jsonField = $intervenciones[0]->intervencion ?? null;
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

    public function getAllDepartamentos()
    {
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
                'message' => 'Error al obtener los departamentos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getMunicipiosByDepartamento($codigo_departamento)
    {
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
                'message' => 'Error al obtener los municipios: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getAldeasByMunicipio($codigo_municipio)
    {
        try {
            $aldeas = DB::select('EXEC [intervensiones_priorizadas].[sp_GetById_t_glo_aldeasXMunicipio] @cod_municipio = :cod_municipio', [
                'cod_municipio' => $codigo_municipio,
            ]);

            $jsonField = $aldeas[0]->{'JSON_F52E2B61-18A1-11d1-B105-00805F49916B'} ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron aldeas.',
                ], 404); // Not Found
            }
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las aldeas: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function insertIntervencionesPriorizadas(StoreIntervencionesPriorizadasRequest $request)
    {
        try {
            foreach ($request['listado_intervenciones'] as $intervencion) {
                // Insertar la intervención priorizada y obtener el ID generado
                $intervencionResult = DB::select('EXEC intervensiones_priorizadas.sp_Insert_intervension_priorizada 
                    @id_intervension_priorizada = NULL, 
                    @codigo_institucion = :codigo_institucion, 
                    @codigo_programa = :codigo_programa, 
                    @descripcion_paquete_priorizado = :descripcion_paquete_priorizado, 
                    @cantidad_anio = :cantidad_anio, 
                    @presupuesto_proyectado_anual_lempiras = :presupuesto_proyectado_anual_lempiras, 
                    @cantidad_ejecutada_primer_trimestre = :cantidad_ejecutada_primer_trimestre, 
                    @cantidad_ejecutada_segundo_trimestre = :cantidad_ejecutada_segundo_trimestre, 
                    @cantidad_ejecutada_tercer_trimestre = :cantidad_ejecutada_tercer_trimestre, 
                    @cantidad_ejecutada_cuarto_trimestre = :cantidad_ejecutada_cuarto_trimestre, 
                    @observaciones = :observaciones, 
                    @es_obra = :es_obra,
                    @es_beneficiario = :es_beneficiario,
                    @estado = 1', [
                    'codigo_institucion' => $intervencion['codigo_institucion'],
                    'codigo_programa' => $intervencion['codigo_programa'],
                    'descripcion_paquete_priorizado' => $intervencion['descripcion_paquete_priorizado'],
                    'cantidad_anio' => $intervencion['cantidad_anio'],
                    'presupuesto_proyectado_anual_lempiras' => $intervencion['presupuesto_proyectado_anual_lempiras'],
                    'cantidad_ejecutada_primer_trimestre' => $intervencion['cantidad_ejecutada_primer_trimestre'],
                    'cantidad_ejecutada_segundo_trimestre' => $intervencion['cantidad_ejecutada_segundo_trimestre'],
                    'cantidad_ejecutada_tercer_trimestre' => $intervencion['cantidad_ejecutada_tercer_trimestre'],
                    'cantidad_ejecutada_cuarto_trimestre' => $intervencion['cantidad_ejecutada_cuarto_trimestre'],
                    'observaciones' => $intervencion['observaciones'],
                    'es_obra' => $intervencion['es_obra'],
                    'es_beneficiario' => $intervencion['es_beneficiario']
                ]);

                // Obtener el ID de la intervención priorizada recién creada
                $codigoIntervencion = $intervencionResult[0]->codigo_intervension_priorizada ?? null;

                if (!$codigoIntervencion) {
                    throw new \Exception('Error al insertar la intervención priorizada.');
                }

                // Insertar las aldeas asociadas a la intervención priorizada
                foreach ($intervencion['listado_aldeas'] as $aldea) {
                    DB::statement('EXEC intervensiones_priorizadas.sp_insert_aldea_priorizada 
                        @codigo_intervension_priorizada = :codigo_intervension_priorizada, 
                        @cod_departamento = :cod_departamento, 
                        @cod_municipio = :cod_municipio, 
                        @cod_aldea = :cod_aldea, 
                        @estado = 1', [
                        'codigo_intervension_priorizada' => $codigoIntervencion,
                        'cod_departamento' => $aldea['cod_departamento'],
                        'cod_municipio' => $aldea['cod_municipio'],
                        'cod_aldea' => $aldea['cod_aldea']
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Intervenciones y aldeas asociadas insertadas correctamente.'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al insertar intervenciones priorizadas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateIntervencionPriorizada(UpdateIntervencionPriorizadaRequest $request)
    {
        try {
            // Validar los datos del request
            $validated = $request;
    
            // Actualizar la intervención priorizada
            $intervencionResult = DB::select('EXEC [intervensiones_priorizadas].[sp_Insert_intervension_priorizada] 
                @id_intervension_priorizada = :id_intervension_priorizada,
                @codigo_institucion = :codigo_institucion,
                @codigo_programa = :codigo_programa,
                @descripcion_paquete_priorizado = :descripcion_paquete_priorizado,
                @cantidad_anio = :cantidad_anio,
                @presupuesto_proyectado_anual_lempiras = :presupuesto_proyectado_anual_lempiras,
                @cantidad_ejecutada_primer_trimestre = :cantidad_ejecutada_primer_trimestre,
                @cantidad_ejecutada_segundo_trimestre = :cantidad_ejecutada_segundo_trimestre,
                @cantidad_ejecutada_tercer_trimestre = :cantidad_ejecutada_tercer_trimestre,
                @cantidad_ejecutada_cuarto_trimestre = :cantidad_ejecutada_cuarto_trimestre,
                @observaciones = :observaciones,
                @estado = :estado,
                @es_obra = :es_obra,
                @es_beneficiario = :es_beneficiario', [
                    'id_intervension_priorizada' => $validated['id_intervension_priorizada'],
                    'codigo_institucion' => $validated['codigo_institucion'],
                    'codigo_programa' => $validated['codigo_programa'],
                    'descripcion_paquete_priorizado' => $validated['descripcion_paquete_priorizado'],
                    'cantidad_anio' => $validated['cantidad_anio'],
                    'presupuesto_proyectado_anual_lempiras' => $validated['presupuesto_proyectado_anual_lempiras'],
                    'cantidad_ejecutada_primer_trimestre' => $validated['cantidad_ejecutada_primer_trimestre'],
                    'cantidad_ejecutada_segundo_trimestre' => $validated['cantidad_ejecutada_segundo_trimestre'],
                    'cantidad_ejecutada_tercer_trimestre' => $validated['cantidad_ejecutada_tercer_trimestre'],
                    'cantidad_ejecutada_cuarto_trimestre' => $validated['cantidad_ejecutada_cuarto_trimestre'],
                    'observaciones' => $validated['observaciones'],
                    'estado' => $validated['estado'] ?? 1,
                    'es_obra' => $validated['es_obra'],
                    'es_beneficiario' => $validated['es_beneficiario']
            ]);
    
            // Obtener el ID de la intervención actualizada
            $codigoIntervencion = $intervencionResult[0]->codigo_intervension_priorizada ?? null;
    
            if (!$codigoIntervencion) {
                throw new \Exception('Error al actualizar la intervención priorizada.');
            }
    
            try{
                // Eliminar las aldeas intervenidas relacionadas con la intervención priorizada
                DB::statement('EXEC [intervensiones_priorizadas].[sp_Eliminar_Aldeas_Intervenidas] 
                @codigo_intervension_priorizada = :codigo_intervension_priorizada', [
                'codigo_intervension_priorizada' => $validated['id_intervension_priorizada']
                ]);
            }catch(\Exception $e){
                return response()->json([
                    'success' => false,
                    'message' => 'Error al eliminar las aldeas intervenidas relacionadas con la intervención priorizada: ' . $e->getMessage(),
                ], 500);
            }

    
            // Insertar las nuevas aldeas asociadas a la intervención priorizada
            foreach ($validated['listado_aldeas'] as $aldea) {
                DB::statement('EXEC [intervensiones_priorizadas].[sp_insert_aldea_priorizada] 
                    @codigo_intervension_priorizada = :codigo_intervension_priorizada, 
                    @cod_departamento = :cod_departamento, 
                    @cod_municipio = :cod_municipio, 
                    @cod_aldea = :cod_aldea, 
                    @estado = 1', [
                    'codigo_intervension_priorizada' => $codigoIntervencion,
                    'cod_departamento' => $aldea['cod_departamento'],
                    'cod_municipio' => $aldea['cod_municipio'],
                    'cod_aldea' => $aldea['cod_aldea']
                ]);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Intervención priorizada actualizada correctamente.',
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la intervención priorizada: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function getAllIntervenciones()
    {
        try {
            $intervenciones = DB::select('EXEC [intervensiones_priorizadas].[sp_GetAll_intervenciones]');

            $jsonField = $intervenciones[0]->intervenciones ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron intervenciones.',
                ], 404); // Not Found
            }
            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las intervenciones: ' . $e->getMessage(),
            ], 500);
        }
    }

}



