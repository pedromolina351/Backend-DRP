<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreDatosComplementariosRequest;

class DatosComplementariosController extends Controller
{
    public function getDatosComplementariosByPoa($codigo_poa){
        try {
            // Verificar si el codigo_poa existe en la tabla correspondiente
            $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $codigo_poa)->exists();

            if (!$poaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe.',
                ], 400); // Bad Request
            }

            $datosComplementarios = DB::select('EXEC [mmr].[sp_GetById_poa_datosComplementarios] ?', [$codigo_poa]);

            $jsonField = $datosComplementarios[0]->ResultadoJSON ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];

            return response()->json([
                'success' => true,
                'message' => 'Datos complementarios obtenidos correctamente.',
                'data' => $data,
            ], 200); // OK

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos complementarios.',
                'error' => $e->getMessage(),
            ], 500); // Internal Server Error
        }
    }

    public function insertDatosComplementarios(StoreDatosComplementariosRequest $request)
    {
        try {
            // Extraer el primer beneficiario por programa y por pueblo
            $primerBeneficiarioPrograma = $request->Listado_Beneficiarios_Programa[0];
            $primerBeneficiarioPueblo = $request->Listado_Beneficiarios_Pueblos[0];
    
            // Inserción inicial con el primer beneficiario por programa y pueblo
            DB::statement('EXEC mmr.sp_Insert_Datos_Complementarios_POA 
                @codigo_poa = :codigo_poa,
                @GrupoEdadID = :GrupoEdadID,
                @GeneroID = :GeneroID,
                @CantidadBeneficiarios = :CantidadBeneficiarios,
                @PuebloID = :PuebloID,
                @CantidadPueblo = :CantidadPueblo,
                @NombreUnidad = :NombreUnidad,
                @ResponsableUnidad = :ResponsableUnidad,
                @PresupuestoTotal = :PresupuestoTotal,
                @InversionMujeres = :InversionMujeres,
                @InversionFamilia = :InversionFamilia,
                @InversionIgualdad = :InversionIgualdad,
                @CantidadTotalBeneficiarios = :CantidadTotalBeneficiarios', [
                'codigo_poa' => $request->codigo_poa,
                'GrupoEdadID' => $primerBeneficiarioPrograma['GrupoEdadID'],
                'GeneroID' => $primerBeneficiarioPrograma['GeneroID'],
                'CantidadBeneficiarios' => $primerBeneficiarioPrograma['CantidadBeneficiarios'],
                'PuebloID' => $primerBeneficiarioPueblo['PuebloID'],
                'CantidadPueblo' => $primerBeneficiarioPueblo['CantidadPueblo'],
                'NombreUnidad' => $request->NombreUnidad,
                'ResponsableUnidad' => $request->ResponsableUnidad,
                'PresupuestoTotal' => $request->PresupuestoTotal,
                'InversionMujeres' => $request->InversionMujeres,
                'InversionFamilia' => $request->InversionFamilia,
                'InversionIgualdad' => $request->InversionIgualdad,
                'CantidadTotalBeneficiarios' => $request->CantidadTotalBeneficiarios,
            ]);
    
            // Iterar sobre los demás beneficiarios por programa
            foreach (array_slice($request->Listado_Beneficiarios_Programa, 1) as $beneficiarioPrograma) {
                DB::statement('EXEC mmr.sp_Insert_Datos_Complementarios_POA 
                    @codigo_poa = :codigo_poa,
                    @GrupoEdadID = :GrupoEdadID,
                    @GeneroID = :GeneroID,
                    @CantidadBeneficiarios = :CantidadBeneficiarios,
                    @PuebloID = NULL,
                    @CantidadPueblo = NULL,
                    @NombreUnidad = NULL,
                    @ResponsableUnidad = NULL,
                    @PresupuestoTotal = NULL,
                    @InversionMujeres = NULL,
                    @InversionFamilia = NULL,
                    @InversionIgualdad = NULL,
                    @CantidadTotalBeneficiarios = NULL', [
                    'codigo_poa' => $request->codigo_poa,
                    'GrupoEdadID' => $beneficiarioPrograma['GrupoEdadID'],
                    'GeneroID' => $beneficiarioPrograma['GeneroID'],
                    'CantidadBeneficiarios' => $beneficiarioPrograma['CantidadBeneficiarios'],
                ]);
            }
    
            // Iterar sobre los demás beneficiarios por pueblos
            foreach (array_slice($request->Listado_Beneficiarios_Pueblos, 1) as $beneficiarioPueblo) {
                DB::statement('EXEC mmr.sp_Insert_Datos_Complementarios_POA 
                    @codigo_poa = :codigo_poa,
                    @GrupoEdadID = NULL,
                    @GeneroID = NULL,
                    @CantidadBeneficiarios = NULL,
                    @PuebloID = :PuebloID,
                    @CantidadPueblo = :CantidadPueblo,
                    @NombreUnidad = NULL,
                    @ResponsableUnidad = NULL,
                    @PresupuestoTotal = NULL,
                    @InversionMujeres = NULL,
                    @InversionFamilia = NULL,
                    @InversionIgualdad = NULL,
                    @CantidadTotalBeneficiarios = NULL', [
                    'codigo_poa' => $request->codigo_poa,
                    'PuebloID' => $beneficiarioPueblo['PuebloID'],
                    'CantidadPueblo' => $beneficiarioPueblo['CantidadPueblo'],
                ]);
            }
    
            // Retornar respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Datos complementarios insertados con éxito.'
            ], 201);
    
        } catch (\Exception $e) {
            // Manejo de errores
            $errorMessage = $e->getMessage();
    
            if (str_contains($errorMessage, 'Ya existe un beneficiario con el mismo GrupoEdadID y GeneroID para este POA')) {
                return response()->json([
                    'success' => false,
                    'message' => 'El beneficiario con el mismo GrupoEdadID y GeneroID ya está registrado para este POA.',
                ], 400);
            }
    
            if (str_contains($errorMessage, 'Ya existe un beneficiario del mismo PuebloID para este POA')) {
                return response()->json([
                    'success' => false,
                    'message' => 'El beneficiario del mismo PuebloID ya está registrado para este POA.',
                ], 400);
            }
    
            // Manejo genérico para otros errores
            return response()->json([
                'success' => false,
                'message' => 'Error al insertar los datos complementarios: ' . $errorMessage,
            ], 500);
        }
    }    
    
}
