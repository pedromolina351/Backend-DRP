<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\Http\Requests\DictamenPoaRequest;

class DictamenController extends Controller
{
    public function generateDictamen()
    {
        // Crear una instancia de PHPWord
        $phpWord = new PhpWord();
    
        // Agregar una sección
        $section = $phpWord->addSection();
    
        // Agregar contenido dinámico
        $section->addText('Este es un documento Word generado dinámicamente.');
        $section->addText('Datos recibidos:');
        $section->addText('Nombre: No especificado');
        $section->addText('Fecha: No especificada');
    
        // Crear un archivo temporal
        $fileName = 'dictamen_generado.docx';
        $tempPath = storage_path('app/resourses' . $fileName);
    
        // Escribir el archivo
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);
    
        // Devolver el archivo como respuesta descargable
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
    
}
