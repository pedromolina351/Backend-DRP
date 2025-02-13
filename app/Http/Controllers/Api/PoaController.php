<?php

namespace App\Http\Controllers\Api;

use App\Models\Poa;
use App\Http\Controllers\Controller;
use App\Http\Requests\EditPoaRequest;
use App\Http\Requests\InsertPoaMainRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StorePoaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PoaController extends Controller
{
    public function getPoasList()
    {
        $poas = Poa::get();
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

    public function getPoa($codigo_poa)
    {
        try {
            // Obtener el codigo_institucion a partir de la tabla poa_t_poas
            $codigo_institucion = DB::table('poa_t_poas')
                ->where('codigo_poa', $codigo_poa)
                ->value('codigo_institucion');
            
            if (!$codigo_institucion) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe o no tiene una institución asociada.'
                ], 404);
            }

            // Inicializar arreglo para almacenar resultados
            $result = [];

            // Ejecutar cada procedimiento almacenado y almacenar los resultados en el arreglo
            $result['Vision_Pais'] = DB::select('EXEC sp_GetById_poa_t_poas_vision_paisXPoa @codigo_poa = :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);

            $result['Politicas'] = DB::select('EXEC sp_GetById_poa_t_poas_politicasXPoa @codigo_poa = :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);

            $result['An_ODs'] = DB::select('EXEC sp_GetById_poa_t_poas_an_odsXPoa @codigo_poa = :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);

            $result['PEG'] = DB::select('EXEC sp_GetById_poa_t_poas_pegXPoa @codigo_poa = :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);

            $result['Programas_Poa'] = DB::select('EXEC sp_GetById_poa_t_poas_programasXPoa @codigo_poa = :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);

            $result['Programas_Institucion'] = DB::select('EXEC sp_GetById_t_programasXInstitucion @codigo_institucion = :codigo_institucion', [
                'codigo_institucion' => $codigo_institucion
            ]);

            // Retornar la respuesta con los resultados
            return response()->json([
                'success' => true,
                'data' => $result
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createPoa(StorePoaRequest $request)
    {
        $poa = Poa::create($request->validated());
        $data = [
            'status' => 201,
            'message' => 'Poa creado con éxito',
            'poa' => $poa,
        ];
        return response()->json($data, 201, [], JSON_UNESCAPED_UNICODE);
    }

    public function insertPoaMain(InsertPoaMainRequest $request)
    {
        try {
            // Ejecutar el procedimiento almacenado principal para insertar el POA
            $codigo_poa = DB::transaction(function () use ($request) {
                $result = DB::select(
                    'EXEC sp_Insert_poa_t_poas_main
                    @codigo_institucion = :codigo_institucion,
                    @codigo_programa = :codigo_programa,
                    @codigo_usuario_creador = :codigo_usuario_creador,
                    @codigo_politica = NULL,
                    @codigo_objetivo_an_ods = NULL,
                    @codigo_meta_an_ods = NULL,
                    @codigo_indicador_an_ods = NULL,
                    @codigo_objetivo_vp = NULL,
                    @codigo_meta_vp = NULL,
                    @codigo_gabinete = NULL,
                    @codigo_eje_estrategico = NULL,
                    @codigo_objetivo_peg = NULL,
                    @codigo_resultado_peg = NULL,
                    @codigo_indicador_resultado_peg = NULL',
                    [
                        'codigo_institucion' => $request->codigo_institucion,
                        'codigo_programa' => $request->codigo_programa,
                        'codigo_usuario_creador' => $request->codigo_usuario_creador,
                    ]
                );

                $codigo_poa = $result[0]->codigo_poa ?? null;

                if (!$codigo_poa) {
                    throw new \Exception('No se pudo generar el código del POA.');
                }

                // Iterar sobre las políticas y agregar registros relacionados
                foreach ($request->listado_politicas as $politica) {
                    DB::statement('EXEC sp_Insert_poa_t_poas_politicas @codigo_poa = :codigo_poa, @codigo_politica_publica = :codigo_politica', [
                        'codigo_poa' => $codigo_poa,
                        'codigo_politica' => $politica['codigo_politica'],
                    ]);
                }

                // Iterar sobre los objetivos AN-ODS
                foreach ($request->listado_objetivos as $objetivo) {
                    DB::statement('EXEC sp_Insert_poa_t_poas_an_ods 
                        @codigo_poa = :codigo_poa,
                        @codigo_objetivo_an_ods = :codigo_objetivo_an_ods,
                        @codigo_meta_an_ods = :codigo_meta_an_ods,
                        @codigo_indicador_an_ods = :codigo_indicador_an_ods,
                        @estado_an_ods = 1', [
                        'codigo_poa' => $codigo_poa,
                        'codigo_objetivo_an_ods' => $objetivo['codigo_objetivo_an_ods'],
                        'codigo_meta_an_ods' => $objetivo['codigo_meta_an_ods'],
                        'codigo_indicador_an_ods' => $objetivo['codigo_indicador_an_ods'],
                    ]);
                }

                // Iterar sobre los objetivos de Visión País
                foreach ($request->listado_objetivos_vp as $vp) {
                    DB::statement('EXEC sp_Insert_poa_t_poas_vision_pais 
                        @codigo_poa = :codigo_poa,
                        @codigo_objetivo_vp = :codigo_objetivo_vp,
                        @codigo_meta_vp = :codigo_meta_vp,
                        @estado_vp = 1', [
                        'codigo_poa' => $codigo_poa,
                        'codigo_objetivo_vp' => $vp['codigo_objetivo_vp'],
                        'codigo_meta_vp' => $vp['codigo_meta_vp'],
                    ]);
                }

                // Iterar sobre el plan estratégico
                foreach ($request->listado_plan_estrategico as $peg) {
                    DB::statement('EXEC sp_Insert_poa_t_poas_peg 
                        @codigo_poa = :codigo_poa,
                        @codigo_gabinete = :codigo_gabinete,
                        @codigo_eje_estrategico = :codigo_eje_estrategico,
                        @codigo_objetivo_peg = :codigo_objetivo_peg,
                        @codigo_resultado_peg = :codigo_resultado_peg,
                        @codigo_indicador_resultado_peg = :codigo_indicador_resultado_peg,
                        @estado_poa_peg = 1', [
                        'codigo_poa' => $codigo_poa,
                        'codigo_gabinete' => $peg['codigo_gabinete'],
                        'codigo_eje_estrategico' => $peg['codigo_eje_estrategico'],
                        'codigo_objetivo_peg' => $peg['codigo_objetivo_peg'],
                        'codigo_resultado_peg' => $peg['codigo_resultado_peg'],
                        'codigo_indicador_resultado_peg' => $peg['codigo_indicador_resultado_peg'],
                    ]);
                }

                return $codigo_poa;
            });

            return response()->json([
                'success' => true,
                'message' => 'POA creado con éxito.',
                'codigo_poa' => $codigo_poa,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el POA: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deactivatePoa($codigo_poa)
    {
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

    public function editPoaMain(EditPoaRequest $request)
    {
        try {
            // Validar los datos del request
            $validated = $request->validated();
    
            // Validar permisos del usuario
            $user = DB::table('config_t_usuarios')
                ->where('codigo_usuario', $validated['codigo_usuario_modificador'])
                ->select('super_user', 'usuario_drp')
                ->first();
    
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'El código de usuario modificador es inválido.'
                ], 403);
            }
    
            if ($user->super_user == 0 && $user->usuario_drp == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene permisos para realizar esta operación.'
                ], 403);
            }
    
            DB::transaction(function () use ($validated) {
                // Actualizar la tabla principal y limpiar datos relacionados
                DB::statement('EXEC sp_Replace_poa_t_poas_main 
                    @codigo_poa = :codigo_poa,
                    @codigo_institucion = :codigo_institucion,
                    @codigo_programa = :codigo_programa,
                    @codigo_usuario_modificador = :codigo_usuario_modificador',
                    [
                        'codigo_poa' => $validated['codigo_poa'],
                        'codigo_institucion' => $validated['codigo_institucion'],
                        'codigo_programa' => $validated['codigo_programa'],
                        'codigo_usuario_modificador' => $validated['codigo_usuario_modificador'],
                    ]
                );
    
                // Insertar políticas
                if (!empty($validated['listado_politicas'])) {
                    foreach ($validated['listado_politicas'] as $politica) {
                        DB::statement('EXEC sp_Insert_poa_t_poas_politicas 
                            @codigo_poa = :codigo_poa, 
                            @codigo_politica_publica = :codigo_politica',
                            [
                                'codigo_poa' => $validated['codigo_poa'],
                                'codigo_politica' => $politica['codigo_politica'],
                            ]
                        );
                    }
                }
    
                // Insertar objetivos AN-ODS
                if (!empty($validated['listado_objetivos'])) {
                    foreach ($validated['listado_objetivos'] as $objetivo) {
                        DB::statement('EXEC sp_Insert_poa_t_poas_an_ods 
                            @codigo_poa = :codigo_poa,
                            @codigo_objetivo_an_ods = :codigo_objetivo_an_ods,
                            @codigo_meta_an_ods = :codigo_meta_an_ods,
                            @codigo_indicador_an_ods = :codigo_indicador_an_ods,
                            @estado_an_ods = 1',
                            [
                                'codigo_poa' => $validated['codigo_poa'],
                                'codigo_objetivo_an_ods' => $objetivo['codigo_objetivo_an_ods'],
                                'codigo_meta_an_ods' => $objetivo['codigo_meta_an_ods'],
                                'codigo_indicador_an_ods' => $objetivo['codigo_indicador_an_ods'],
                            ]
                        );
                    }
                }
    
                // Insertar objetivos de Visión País
                if (!empty($validated['listado_objetivos_vp'])) {
                    foreach ($validated['listado_objetivos_vp'] as $vp) {
                        DB::statement('EXEC sp_Insert_poa_t_poas_vision_pais 
                            @codigo_poa = :codigo_poa,
                            @codigo_objetivo_vp = :codigo_objetivo_vp,
                            @codigo_meta_vp = :codigo_meta_vp,
                            @estado_vp = 1',
                            [
                                'codigo_poa' => $validated['codigo_poa'],
                                'codigo_objetivo_vp' => $vp['codigo_objetivo_vp'],
                                'codigo_meta_vp' => $vp['codigo_meta_vp'],
                            ]
                        );
                    }
                }
    
                // Insertar plan estratégico
                if (!empty($validated['listado_plan_estrategico'])) {
                    foreach ($validated['listado_plan_estrategico'] as $peg) {
                        DB::statement('EXEC sp_Insert_poa_t_poas_peg 
                            @codigo_poa = :codigo_poa,
                            @codigo_gabinete = :codigo_gabinete,
                            @codigo_eje_estrategico = :codigo_eje_estrategico,
                            @codigo_objetivo_peg = :codigo_objetivo_peg,
                            @codigo_resultado_peg = :codigo_resultado_peg,
                            @codigo_indicador_resultado_peg = :codigo_indicador_resultado_peg,
                            @estado_poa_peg = 1',
                            [
                                'codigo_poa' => $validated['codigo_poa'],
                                'codigo_gabinete' => $peg['codigo_gabinete'],
                                'codigo_eje_estrategico' => $peg['codigo_eje_estrategico'],
                                'codigo_objetivo_peg' => $peg['codigo_objetivo_peg'],
                                'codigo_resultado_peg' => $peg['codigo_resultado_peg'],
                                'codigo_indicador_resultado_peg' => $peg['codigo_indicador_resultado_peg'],
                            ]
                        );
                    }
                }
            });
    
            return response()->json([
                'success' => true,
                'message' => 'POA modificado con éxito.'
            ], 200);
        } catch (\Exception $e) {
            // Capturar errores y retornar mensaje
            return response()->json([
                'success' => false,
                'message' => 'Error al modificar el POA: ' . $e->getMessage()
            ], 500);
        }
    }    

    public function getAllDataPoa($codigo_poa){
        try{
            $data = DB::select('EXEC sp_GetAllDataPoa @codigo_poa = :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);
            if(empty($data)){
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron datos para el POA especificado.'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPoliticasByPoa($codigo_poa){
        try{
            $data = DB::select('EXEC [dbo].[sp_GetById_poa_t_poas_politicasXPoa] @codigo_poa = :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);
            if(empty($data)){
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron políticas para el POA especificado.'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las políticas: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAnOdsByPoa($codigo_poa){
        try{
            $data = DB::select('EXEC [dbo].[sp_GetById_poa_t_poas_an_odsXPoa]  @codigo_poa = :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);
            if(empty($data)){
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron objetivos AN-ODS para el POA especificado.'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los objetivos AN-ODS: ' . $e->getMessage()
            ], 500);
        }
    }

    public function aprobarPOA($codigo_poa){
        try{
            DB::statement('EXEC [dbo].[sp_Aprobar_POA] @codigo_poa = :codigo_poa', [
                'codigo_poa' => $codigo_poa
            ]);
            return response()->json([
                'success' => true,
                'message' => 'POA aprobado con éxito.'
            ], 200);
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Error al aprobar el POA: ' . $e->getMessage()
            ], 500);
        }
    }
}
