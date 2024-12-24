<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\Http\Requests\DictamenPoaRequest;

class DictamenController extends Controller
{
    public function generateDictamen(DictamenPoaRequest $request)
    {
        // Crea una nueva instancia de PHPWord
        $phpWord = new PhpWord();

        // Agrega una sección al documento
        $section = $phpWord->addSection();

        // Agrega contenido dinámico basado en los datos recibidos
        $section->addText('Este es un documento Word generado dinámicamente.');
        $section->addText('Datos recibidos:');
        // Define el nombre del archivo
        $fileName = 'documento_generado.docx';

        // Genera el archivo y guárdalo temporalmente
        $tempFile = storage_path($fileName);
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        // Devuelve el archivo como respuesta descargable
        return response()->download($tempFile)->deleteFileAfterSend(true);
    }
}
