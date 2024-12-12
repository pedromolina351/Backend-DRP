<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCommentRequest;

class ComentarioController extends Controller
{
    public function getCommentsByPoaId($poaId){
        try{
            $resultados = DB::select('EXEC sp_GetById_poa_t_comentarios ?', [$poaId]);
            return response()->json([
                'success' => true,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los comentarios: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function insertNewComment(StoreCommentRequest $request){
        try{
            $resultados = DB::statement('EXEC sp_Insert_poa_t_comentarios 
                @codigo_usuario =:codigo_usuario,
                @comentario_texto =:comentario_texto,
                @codigo_comentario_padre =:codigo_comentario_padre,
                @codigo_poa =:codigo_poa', [
                'codigo_usuario' => $request->codigo_usuario,
                'comentario_texto' => $request->comentario_texto,
                'codigo_comentario_padre' => $request->codigo_comentario_padre,
                'codigo_poa' => $request->codigo_poa
            ]);
            return response()->json([
                'message' => 'El comentario se creó exitosamente',
                'status' => 201,
                'data' => $resultados,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al insertar el comentario: ' . $e->getMessage(),
            ], 500);
        }
    }
}
