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
        $phpWord = new PhpWord();

        // Configurar el tamaño de la página como "Letter" sin márgenes
        $sectionStyle = [
            'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(8.5),
            'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(11),
        ];
        $section = $phpWord->addSection($sectionStyle);

        // Configurar el encabezado sin márgenes
        $header = $section->addHeader();

        // Agregar la imagen como fondo
        $header->addWatermark(
            public_path('images/fondo.jpg'), // Ruta completa a la imagen
            [
                'width' => 612,    // Ancho en puntos para cubrir toda la página Letter
                'height' => 792,   // Altura en puntos
                'marginTop' => -36, // Ajuste fino para eliminar el margen superior
                'marginLeft' => -70,  // Ajuste fino para centrar la imagen
                'posHorizontal' => 'absolute',
                'posVertical' => 'absolute',
            ]
        );

        // Agregar el texto de los primeros dos párrafos
        $section->addTextBreak(4); // Agregar espacio antes del contenido para evitar que se solape
        $section->addText(
            'Secretaría en el Despacho de Desarrollo Social (SEDESOL)',
            ['bold' => true, 'size' => 14],
            ['alignment' => 'center']
        );
        $section->addText(
            'Dirección de Regulación Programática',
            ['bold' => true, 'size' => 14],
            ['alignment' => 'center']
        );
        $section->addText(
            'Dictamen de Viabilidad Técnica POA- PPTO XXXX XXXXX',
            ['bold' => true, 'size' => 12],
            ['alignment' => 'center']
        );

        // Agregar texto adicional
        //$section->addTextBreak(2); // Espacio adicional
        $text = mb_convert_encoding(
            'Según el Decreto Ejecutivo PCM-19-2022 la Secretaría en el Despacho de Planificación Estratégica, como requisito previo a la aprobación de los Planes Operativos Anuales (POA) de las instituciones que conforman el Marco de Protección Social, requerirá un Dictamen Técnico emitido por la Secretaría de Desarrollo Social, a través de la Dirección de Regulación Programática, para determinar su viabilidad técnica.',
            'UTF-8',
            'auto'
        );
        
        $section->addText($text, ['size' => 11], ['alignment' => 'justify']);
        
        

        // Guardar el archivo
        $fileName = 'documento_con_fondos.docx';
        $tempPath = storage_path('App/' . $fileName);

        // Eliminar el archivo existente si ya existe
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        // Guardar el archivo
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        // Devolver el archivo
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}
