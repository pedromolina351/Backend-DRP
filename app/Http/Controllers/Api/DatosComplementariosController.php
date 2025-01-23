<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreDatosComplementariosRequest;

class DatosComplementariosController extends Controller
{
    public function getDatosComplementariosByPoa($codigo_poa)
    {
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
            // Extraer los datos generales de la solicitud
            $codigoPoa = $request->codigo_poa;
            $nombreUnidad = $request->NombreUnidad;
            $responsableUnidad = $request->ResponsableUnidad;
            $presupuestoTotal = $request->PresupuestoTotal;
            $inversionMujeres = $request->InversionMujeres;
            $inversionFamilia = $request->InversionFamilia;
            $inversionIgualdad = $request->InversionIgualdad;
            $cantidadTotalBeneficiarios = $request->CantidadTotalBeneficiarios;

            // Actualizar los datos generales (unidad organizativa y presupuesto) con el primer beneficiario por programa y pueblo
            $primerBeneficiarioPrograma = $request->Listado_Beneficiarios_Programa[0] ?? null;
            $primerBeneficiarioPueblo = $request->Listado_Beneficiarios_Pueblos[0] ?? null;

            DB::statement('EXEC mmr.sp_Actualizar_Datos_Complementarios_POA 
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
                'codigo_poa' => $codigoPoa,
                'GrupoEdadID' => $primerBeneficiarioPrograma['GrupoEdadID'] ?? null,
                'GeneroID' => $primerBeneficiarioPrograma['GeneroID'] ?? null,
                'CantidadBeneficiarios' => $primerBeneficiarioPrograma['CantidadBeneficiarios'] ?? null,
                'PuebloID' => $primerBeneficiarioPueblo['PuebloID'] ?? null,
                'CantidadPueblo' => $primerBeneficiarioPueblo['CantidadPueblo'] ?? null,
                'NombreUnidad' => $nombreUnidad,
                'ResponsableUnidad' => $responsableUnidad,
                'PresupuestoTotal' => $presupuestoTotal,
                'InversionMujeres' => $inversionMujeres,
                'InversionFamilia' => $inversionFamilia,
                'InversionIgualdad' => $inversionIgualdad,
                'CantidadTotalBeneficiarios' => $cantidadTotalBeneficiarios,
            ]);

            // Iterar sobre los demás beneficiarios por programa
            foreach (array_slice($request->Listado_Beneficiarios_Programa, 1) as $beneficiarioPrograma) {
                DB::statement('EXEC mmr.sp_Actualizar_Datos_Complementarios_POA 
                @codigo_poa = :codigo_poa,
                @GrupoEdadID = :GrupoEdadID,
                @GeneroID = :GeneroID,
                @CantidadBeneficiarios = :CantidadBeneficiarios', [
                    'codigo_poa' => $codigoPoa,
                    'GrupoEdadID' => $beneficiarioPrograma['GrupoEdadID'],
                    'GeneroID' => $beneficiarioPrograma['GeneroID'],
                    'CantidadBeneficiarios' => $beneficiarioPrograma['CantidadBeneficiarios'],
                ]);
            }

            // Iterar sobre los demás beneficiarios por pueblos
            foreach (array_slice($request->Listado_Beneficiarios_Pueblos, 1) as $beneficiarioPueblo) {
                DB::statement('EXEC mmr.sp_Actualizar_Datos_Complementarios_POA 
                @codigo_poa = :codigo_poa,
                @PuebloID = :PuebloID,
                @CantidadPueblo = :CantidadPueblo', [
                    'codigo_poa' => $codigoPoa,
                    'PuebloID' => $beneficiarioPueblo['PuebloID'],
                    'CantidadPueblo' => $beneficiarioPueblo['CantidadPueblo'],
                ]);
            }

            // Retornar respuesta exitosa
            return response()->json([
                'success' => true,
                'message' => 'Datos complementarios actualizados con éxito.'
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar los datos complementarios: ' . $e->getMessage(),
            ], 500);
        }
    }
}
