<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Models\Institucion;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Indicador extends Controller
{
    public function getProgramasConInversionEnGenero()
    {
        try {
            $programas = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_programas_con_inversion_en_genero]');

            return response()->json([
                'success' => true,
                'programas' => $programas ?? [],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los programas: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getInstitucionesByVisionPais(Request $request)
    {
        try {

            // Extraer los parámetros del request
            $codigoInstitucion = $request->input('codigo_institucion', null);
            $anios = $request->input('anios', null);

            // Validar que existe la institución en la base de datos si se está enviando como parámetro
            if ($codigoInstitucion) {
                $institucion = Institucion::where('codigo_institucion', $codigoInstitucion)->where('estado_institucion', 1)->first();
                if ($institucion == null) {
                    $data = [
                        'status' => 404,
                        'message' => 'La institución proporcionada no existe.',
                    ];
                    return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
                }
            }

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_instituciones_por_vision_pais] 
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron resultados para la consulta.',
                ], 404);
            }

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

            // Validar que existe la institución en la base de datos si se está enviando como parámetro
            if ($codigoInstitucion) {
                $institucion = Institucion::where('codigo_institucion', $codigoInstitucion)->where('estado_institucion', 1)->first();
                if ($institucion == null) {
                    $data = [
                        'status' => 404,
                        'message' => 'La institución proporcionada no existe.',
                    ];
                    return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
                }
            }

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_instituciones_por_resultado_peg] 
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron resultados para la consulta.',
                ], 404);
            }

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

    public function getInstitucionesByPolitica(Request $request)
    {
        try {
            // Obtener los parámetros del request
            $codigoInstitucion = $request->input('codigo_institucion', null);
            $anios = $request->input('anios', null);

            // Validar que existe la institución en la base de datos si se está enviando como parámetro
            if ($codigoInstitucion != null) {
                $institucion = Institucion::where('codigo_institucion', $codigoInstitucion)->where('estado_institucion', 1)->first();
                if ($institucion == null) {
                    $data = [
                        'status' => 404,
                        'message' => 'La institución proporcionada no existe.',
                    ];
                    return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
                }
            }

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_instituciones_por_politica_publica]
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron resultados para la consulta.',
                ], 404);
            }

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
                'message' => 'Error al consultar las instituciones por política: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getInstitucionesByIndicadorResultado(Request $request)
    {
        try {
            // Obtener los parámetros del request
            $codigoInstitucion = $request->input('codigo_institucion', null);
            $anios = $request->input('anios', null);

            // Validar que existe la institución en la base de datos si se está enviando como parámetro
            if ($codigoInstitucion != null) {
                $institucion = Institucion::where('codigo_institucion', $codigoInstitucion)->where('estado_institucion', 1)->first();
                if ($institucion == null) {
                    $data = [
                        'status' => 404,
                        'message' => 'La institución proporcionada no existe.',
                    ];
                    return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
                }
            }

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_instituciones_por_indicador_resultado]
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron resultados para la consulta.',
                ], 404);
            }

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
                'message' => 'Error al consultar las instituciones por indicador: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getBeneficiariosByPueblos(Request $request)
    {
        try {
            // Obtener los parámetros del request
            $codigoInstitucion = $request->input('codigo_institucion', null);
            $anios = $request->input('anios', null);

            // Validar que existe la institución en la base de datos si se está enviando como parámetro
            if ($codigoInstitucion != null) {
                $institucion = Institucion::where('codigo_institucion', $codigoInstitucion)->where('estado_institucion', 1)->first();
                if ($institucion == null) {
                    $data = [
                        'status' => 404,
                        'message' => 'La institución proporcionada no existe.',
                    ];
                    return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
                }
            }

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_beneficiarios_por_pueblos]
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron resultados para la consulta.',
                ], 404);
            }

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
                'message' => 'Error al consultar los Beneficiarios por Pueblo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getBeneficiariosByGrupoEdad(Request $request){
        try {
            // Obtener los parámetros del request
            $codigoInstitucion = $request->input('codigo_institucion', null);
            $anios = $request->input('anios', null);

            // Validar que existe la institución en la base de datos si se está enviando como parámetro
            if ($codigoInstitucion != null) {
                $institucion = Institucion::where('codigo_institucion', $codigoInstitucion)->where('estado_institucion', 1)->first();
                if ($institucion == null) {
                    $data = [
                        'status' => 404,
                        'message' => 'La institución proporcionada no existe.',
                    ];
                    return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
                }
            }

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_beneficiarios_por_grupo_edad]
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron resultados para la consulta.',
                ], 404);
            }

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
                'message' => 'Error al consultar los Beneficiarios por Grupo de Edad: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getInstitucionesByEjeEstrategico(Request $request)
    {
        try {
            // Obtener los parámetros del request
            $codigoInstitucion = $request->input('codigo_institucion', null);
            $anios = $request->input('anios', null);

            // Validar que existe la institución en la base de datos si se está enviando como parámetro
            if ($codigoInstitucion != null) {
                $institucion = Institucion::where('codigo_institucion', $codigoInstitucion)->where('estado_institucion', 1)->first();
                if ($institucion == null) {
                    $data = [
                        'status' => 404,
                        'message' => 'La institución proporcionada no existe.',
                    ];
                    return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
                }
            }

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_instituciones_por_eje_estrategico]
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron resultados para la consulta.',
                ], 404);
            }

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
                'message' => 'Error al consultar las instituciones por eje estratégico: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getInstitucionesByGabinete(Request $request){
        try {
            // Obtener los parámetros del request
            $codigoInstitucion = $request->input('codigo_institucion', null);
            $anios = $request->input('anios', null);

            // Validar que existe la institución en la base de datos si se está enviando como parámetro
            if ($codigoInstitucion != null) {
                $institucion = Institucion::where('codigo_institucion', $codigoInstitucion)->where('estado_institucion', 1)->first();
                if ($institucion == null) {
                    $data = [
                        'status' => 404,
                        'message' => 'La institución proporcionada no existe.',
                    ];
                    return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
                }
            }

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_instituciones_por_gabinete]
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron resultados para la consulta.',
                ], 404);
            }

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
                'message' => 'Error al consultar las instituciones por gabinete: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function getInstitucionesByAnOds(Request $request)
    {
        try {
            // Obtener los parámetros del request
            $codigoInstitucion = $request->input('codigo_institucion', null);
            $anios = $request->input('anios', null);

            // Validar que existe la institución en la base de datos si se está enviando como parámetro
            if ($codigoInstitucion != null) {
                $institucion = Institucion::where('codigo_institucion', $codigoInstitucion)->where('estado_institucion', 1)->first();
                if ($institucion == null) {
                    $data = [
                        'status' => 404,
                        'message' => 'La institución proporcionada no existe.',
                    ];
                    return response()->json($data, 404, [], JSON_UNESCAPED_UNICODE);
                }
            }

            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicador_cadena_valor].[sp_consultar_instituciones_por_an_ods]
                @codigo_institucion = :codigo_institucion, 
                @anios = :anios', [
                'codigo_institucion' => $codigoInstitucion,
                'anios' => $anios
            ]);

            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron resultados para la consulta.',
                ], 404);
            }

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
                'message' => 'Error al consultar las instituciones por an ODS: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getInstitucionesByIntervencion(Request $request)
    {
        try {
            // Obtener el parámetro del request
            $agruparPor = $request->input('agrupar_por');
    
            // Validar que el valor del parámetro sea válido
            $valoresPermitidos = ['intervencion', 'departamento', 'municipio'];
            if (!in_array($agruparPor, $valoresPermitidos)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El parámetro agrupar_por es obligatorio y debe ser intervencion, departamento o municipio.'
                ], 400);
            }
    
            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [indicadores_interveciones_priorizadas].[sp_consultar_instituciones_por_intervencion] 
                @agrupar_por = :agrupar_por', [
                'agrupar_por' => $agruparPor
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
                'message' => 'Error al consultar las instituciones por intervención: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    public function getActividadesByProductoIntermedio(Request $request)
    {
        try {
            // Validar que el código del producto intermedio sea obligatorio
            $validator = Validator::make($request->all(), [
                'codigo_producto_intermedio' => 'required|integer',
                'codigo_producto_final' => 'nullable|integer',
            ]);
    
            // Si la validación falla, devolver un error
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación el producto intermedio es obligatorio.',
                    'errors' => $validator->errors(),
                ], 422);
            }
    
            // Obtener parámetros
            $codigoProductoIntermedio = $request->input('codigo_producto_intermedio');
            $codigoProductoFinal = $request->input('codigo_producto_final');
    
            // Ejecutar el procedimiento almacenado
            $result = DB::select('EXEC [dbo].[sp_getById_t_actividades_x_producto_intermedio] 
                @codigo_producto_intermedio = :codigo_producto_intermedio, 
                @codigo_producto_final = :codigo_producto_final', [
                'codigo_producto_intermedio' => $codigoProductoIntermedio,
                'codigo_producto_final' => $codigoProductoFinal ?? null,
            ]);
    
            // Si no se encuentran resultados, devolver mensaje
            if (empty($result)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron actividades para el producto intermedio especificado.',
                    'data' => [],
                ], 404);
            }
    
            // Retornar respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Actividades obtenidas exitosamente.',
                'data' => $result,
            ], 200);
    
        } catch (\Exception $e) {
            // Manejo de errores generales
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las actividades: ' . $e->getMessage(),
            ], 500);
        }
    }    
    
}
