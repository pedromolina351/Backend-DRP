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

        // Configurar estilos de tabla
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 50,
            'width' => 100 * 50, // Ancho de la tabla en puntos
        ];
        $firstRowStyle = ['bgColor' => '6CC5D1']; // Fondo azul claro
        $paragraphStyle = ['spaceAfter' => 0, 'alignment' => 'left'];

        // --------------------------------------------------------------------------------------------------------------------------
        // ------------------------------------------    PARRAFOS INICIALES    ------------------------------------------------------
        // --------------------------------------------------------------------------------------------------------------------------

        $section->addTextBreak(2); // Agregar espacio antes del contenido para evitar que se solape
        $section->addText(
            'Secretaría en el Despacho de Desarrollo Social (SEDESOL)',
            ['bold' => true, 'size' => 14, 'name' => 'Pluto Cond Medium', 'color' => 'A6A6A6'],
            ['alignment' => 'center']
        );
        $section->addText(
            'Dirección de Regulación Programática',
            ['bold' => true, 'size' => 14, 'name' => 'Pluto Cond Medium', 'color' => 'A6A6A6'],
            ['alignment' => 'center']
        );
        $section->addText(
            'Dictamen de Viabilidad Técnica POA- PPTO XXXX XXXXX',
            ['bold' => true, 'size' => 12, 'name' => 'Pluto Cond Medium', 'color' => 'A6A6A6'],
            ['alignment' => 'center']
        );
        $p1 = mb_convert_encoding(
            'Según el Decreto Ejecutivo PCM-19-2022 la Secretaría en el Despacho de Planificación Estratégica, como requisito previo a la aprobación de los Planes Operativos Anuales (POA) de las instituciones que conforman el Marco de Protección Social, requerirá un Dictamen Técnico emitido por la Secretaría de Desarrollo Social, a través de la Dirección de Regulación Programática, para determinar su viabilidad técnica.',
            'UTF-8',
            'auto'
        );
        $section->addText($p1, ['size' => 11, 'name' => 'Arial Narrow'], ['alignment' => 'left']);
        $p2 = mb_convert_encoding(
            'Considerando, el Proceso de Formulación de los Planes Operativos Anuales y Presupuesto, correspondiente al año fiscal xxxx, se procede a elaborar el Dictamen de Viabilidad Técnica xxxx. ',
            'UTF-8',
            'auto'
        );
        $section->addText($p2, ['size' => 11, 'name' => 'Arial Narrow'], ['alignment' => 'left']);
        $t1 = mb_convert_encoding(
            'I.	DATOS DE LA INSTITUCIÓN',
            'UTF-8',
            'auto'
        );
        $section->addText($t1, ['bold' => true, 'size' => 12, 'name' => 'Calibri'], ['alignment' => 'left']);



        // --------------------------------------------------------------------------------------------------------------------------
        // -------------------------------------    TABLA DE DATOS DE LA INSTITUCION    ---------------------------------------------
        // --------------------------------------------------------------------------------------------------------------------------

        $phpWord->addTableStyle('InstitutionTable', $tableStyle, $firstRowStyle);
        // Agregar la tabla al documento
        $tablaInstitucion = $section->addTable('InstitutionTable');

        // Primera fila: título de la tabla
        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell(10000, ['gridSpan' => 2, 'bgColor' => '47B6C5'])->addText(
            'DATOS DE LA INSTITUCIÓN',
            ['bold' => true, 'size' => 12, 'color' => 'FFFFFF', 'name' => 'Arial Narrow'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        // Filas de datos con anchos específicos para las columnas
        $column1Width = 3000; // Ancho de la primera columna
        $column2Width = 7000; // Ancho de la segunda columna

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Nombre de la institución', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Código de la institución', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Misión', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Visión', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Presupuesto 2024', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Proyección de Presupuesto 2025', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Observación de PTTO.', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText('Pruebas', [], $paragraphStyle);
        $section->addTextBreak(1);


        // --------------------------------------------------------------------------------------------------------------------------
        // ---------------------------------------------    TABLA DE DATOS GENERALES    ---------------------------------------------
        // --------------------------------------------------------------------------------------------------------------------------
        $t2 = mb_convert_encoding(
            'II.	DATOS GENERALES CADENA DE VALOR PÚBLICO',
            'UTF-8',
            'auto'
        );
        $section->addText($t2, ['bold' => true, 'size' => 12, 'name' => 'Calibri'], ['alignment' => 'left']);

        $phpWord->addTableStyle('generalDataTable', $tableStyle, $firstRowStyle);

        $tablaDatosGenerales = $section->addTable('generalDataTable');

        // Primera fila: título de la tabla
        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell(10000, ['gridSpan' => 2, 'bgColor' => '47B6C5'])->addText(
            'DATOS GENERALES CADENA DE VALOR PÚBLICO ',
            ['bold' => true, 'size' => 12, 'color' => 'FFFFFF', 'name' => 'Arial Narrow'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell(10000, ['gridSpan' => 2, 'bgColor' => '47B6C5'])->addText(
            'GESTIÓN: FORMULACIÓN POA- PPTO AÑO xxxx',
            ['bold' => true, 'size' => 12, 'color' => 'FFFFFF', 'name' => 'Arial Narrow'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        // Filas de datos con anchos específicos para las columnas
        $column1Width = 3000; // Ancho de la primera columna
        $column2Width = 7000; // Ancho de la segunda columna

        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell($column1Width)->addText('Nombre del Programa o Proyecto:', [], $paragraphStyle);
        $tablaDatosGenerales->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell($column1Width)->addText('Código SEFIN ', [], $paragraphStyle);
        $tablaDatosGenerales->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell($column1Width)->addText('Descripción del programa', [], $paragraphStyle);
        $tablaDatosGenerales->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell($column1Width)->addText('Problema o necesidad que pretende atender', [], $paragraphStyle);
        $tablaDatosGenerales->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell($column1Width)->addText('Gabinete - PEG', [], $paragraphStyle);
        $tablaDatosGenerales->addCell($column2Width)->addText('', [], $paragraphStyle);

        $section->addTextBreak(3);

        // --------------------------------------------------------------------------------------------------------------------------
        // ----------------------------------    TABLA CONTINUACION DE DATOS GENERALES    -------------------------------------------
        // --------------------------------------------------------------------------------------------------------------------------

        $tablaDatosGenerales2 = $section->addTable('generalDataTable2');
        $phpWord->addTableStyle('generalDataTable2', $tableStyle, $firstRowStyle);
        // Primera fila: Encabezado
        $tablaDatosGenerales2->addRow();
        $tablaDatosGenerales2->addCell(10000, ['gridSpan' => 2, 'bgColor' => '47B6C5'])->addText(
            'DATOS GENERALES CADENA DE VALOR PÚBLICO ',
            ['bold' => true, 'size' => 12, 'color' => 'FFFFFF', 'name' => 'Arial Narrow'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        $tablaDatosGenerales2->addRow();
        $tablaDatosGenerales2->addCell(10000, ['gridSpan' => 2, 'bgColor' => '47B6C5'])->addText(
            'GESTIÓN: FORMULACIÓN POA- PPTO AÑO xxxx',
            ['bold' => true, 'size' => 12, 'color' => 'FFFFFF', 'name' => 'Arial Narrow'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        // Segunda fila: Indicador PEG y detalle narrativo
        $tablaDatosGenerales2->addRow();
        $tablaDatosGenerales2->addCell(3000)->addText(
            'Indicador PEG',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(6000, ['gridSpan' => 4])->addText(
            'Detalle narrativo del indicador',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );

        // Tercera fila: Participantes beneficiados
        $tablaDatosGenerales2->addRow();
        $tablaDatosGenerales2->addCell(3000)->addText(
            'Participantes beneficiados (as):',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(2000)->addText(
            '# Personas',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(2000)->addText(
            '', // Espacio en blanco después de # Personas
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(2000)->addText(
            '# Familias',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(2000)->addText(
            '', // Espacio en blanco después de # Familias
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(2000)->addText(
            '# Hogares',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        // Cuarta fila: Grupo vulnerable
        $tablaDatosGenerales2->addRow();
        $tablaDatosGenerales2->addCell(3000)->addText(
            'Grupo Vulnerable Priorizado por el Programa/ Proyecto:',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(6000, ['gridSpan' => 4])->addText(
            '',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );


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
