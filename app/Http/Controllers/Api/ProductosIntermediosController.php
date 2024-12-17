<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\InsertProductosIntermediosRequest;
use Illuminate\Support\Facades\DB;

class ProductosIntermediosController extends Controller
{
    public function insertProductosIntermedios(InsertProductosIntermediosRequest $request)
    {
        try {
            // Verificar si el codigo_poa existe en la tabla correspondiente
            $poaExists = DB::table('poa_t_poas')->where('codigo_poa', $request->codigo_poa)->exists();

            if (!$poaExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'El codigo_poa proporcionado no existe.',
                ], 400); // Bad Request
            }
            // Inicializar un arreglo para errores
            $errors = [];
    
            // Iterar sobre los productos_intermedios recibidos en el body
            foreach ($request->productos_intermedios as $producto) {
                try {
                    // Ejecutar el procedimiento almacenado para cada producto intermedio
                    DB::statement('EXEC sp_Insert_t_productos_intermedios 
                        @objetivo_operativo = ?, 
                        @producto_intermedio = ?, 
                        @codigo_producto_final = ?, 
                        @indicador_producto_intermedio = ?, 
                        @producto_intermedio_primario = ?, 
                        @programa = ?, 
                        @subprograma = ?, 
                        @proyecto = ?, 
                        @actividad = ?, 
                        @fuente_financiamiento = ?, 
                        @ente_de_financiamiento = ?, 
                        @costro_aproximado = ?, 
                        @estado = ?, 
                        @codigo_poa = ?', [
                        $producto['objetivo_operativo'],
                        $producto['producto_intermedio'],
                        $producto['codigo_producto_final'],
                        $producto['indicador_producto_intermedio'],
                        $producto['producto_intermedio_primario'],
                        $producto['programa'],
                        $producto['subprograma'],
                        $producto['proyecto'],
                        $producto['actividad'],
                        $producto['fuente_financiamiento'],
                        $producto['ente_de_financiamiento'],
                        $producto['costro_aproximado'],
                        $producto['estado'],
                        $request->codigo_poa
                    ]);
                } catch (\Exception $e) {
                    // Capturar el error y agregarlo al arreglo
                    $errors[] = [
                        'producto' => $producto,
                        'error' => $e->getMessage(),
                    ];
                }
            }
    
            // Verificar si hubo errores
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Algunos productos intermedios no se pudieron procesar.',
                    'errors' => $errors,
                ], 207); // 207 Multi-Status indica éxito parcial
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Productos intermedios procesados con éxito.',
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar los productos intermedios: ' . $e->getMessage(),
            ], 500);
        }
    }
    
}
