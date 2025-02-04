<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\Http\Requests\DictamenPoaRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;


class DictamenController extends Controller
{
    public function obtenerDatosPoa($codigo_poa)
    {
        try {
            $datosPoa = DB::select('EXEC [sp_GetById_Datos_Poa_Dictamen] @codigo_poa = ?', [$codigo_poa]);

            $jsonField = $datosPoa[0]->poa ?? null;
            $data = $jsonField ? json_decode($jsonField, true) : [];
            return $data;
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos del POA: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function generateDictamen($codigo_poa)
    {
        $data = $this->obtenerDatosPoa($codigo_poa);
        $phpWord = new PhpWord();

        // Configurar el tamaño de la página como "Letter" sin márgenes
        $sectionStyle = [
            'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(8.5),
            'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(11),
            //'background' => public_path('images/fondo.jpg'), // Ruta a la imagen de fondo
            'marginTop' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(1.5),
        ];
        $section = $phpWord->addSection($sectionStyle);

        // Configurar el encabezado sin márgenes
        $header = $section->addHeader();

        // Agregar la imagen como fondo
        $header->addWatermark(
            public_path('images/marca.png'), // Ruta completa a la imagen
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


        //--------------------------------------------------------------------------------------------------------------
        // --------------------------------------------------- PIE DE PÁGINA -------------------------------------------
        //--------------------------------------------------------------------------------------------------------------

        // Crear el pie de página
        $footer = $section->addFooter();

        // Agregar una tabla para el contenido del pie de página
        $footerTable = $footer->addTable();

        // Agregar fila para el contenido principal
        $footerTable->addRow();
        $footerTable->addCell(3000)->addText(
            'CÓDIGO: E04-FO-001',
            ['name' => 'Arial', 'size' => 10, 'color' => '808080'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $footerTable->addCell(3000)->addText(
            'VERSIÓN: 2.0',
            ['name' => 'Arial', 'size' => 10, 'color' => '808080'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $footerTable->addCell(3000)->addText(
            'FECHA: 17/06/2024',
            ['name' => 'Arial', 'size' => 10, 'color' => '808080'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $footerTable->addCell(3000)->addPreserveText(
            'Página {PAGE} de {NUMPAGES}',
            ['name' => 'Arial', 'size' => 10, 'color' => '808080'],
            ['alignment' => 'right', 'spaceAfter' => 0]
        );


        // --------------------------------------------------------------------------------------------------------------------------
        // ------------------------------------------    PARRAFOS INICIALES    ------------------------------------------------------
        // --------------------------------------------------------------------------------------------------------------------------

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
        $tablaInstitucion->addCell($column2Width)->addText($data[0]['nombre_institucion'], [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Código de la institución', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText($data[0]['codigo_oficial_institucion'], [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Misión', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText($data[0]['mision_institucion'], [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Visión', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText($data[0]['vision_institucion'], [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Presupuesto 2024', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Proyección de Presupuesto 2025', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaInstitucion->addRow();
        $tablaInstitucion->addCell($column1Width)->addText('Observación de PTTO.', [], $paragraphStyle);
        $tablaInstitucion->addCell($column2Width)->addText('', [], $paragraphStyle);
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
        $tablaDatosGenerales->addCell($column2Width)->addText($data[0]['nombre_programa'], [], $paragraphStyle);

        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell($column1Width)->addText('Código SEFIN ', [], $paragraphStyle);
        $tablaDatosGenerales->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell($column1Width)->addText('Descripción del programa', [], $paragraphStyle);
        $tablaDatosGenerales->addCell($column2Width)->addText($data[0]['descripcion_programa'], [], $paragraphStyle);

        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell($column1Width)->addText('Problema o necesidad que pretende atender', [], $paragraphStyle);
        $tablaDatosGenerales->addCell($column2Width)->addText('', [], $paragraphStyle);

        $tablaDatosGenerales->addRow();
        $tablaDatosGenerales->addCell($column1Width)->addText('Gabinete - PEG', [], $paragraphStyle);
        $tablaDatosGenerales->addCell($column2Width)->addText($data[0]['nombre_gabinete'], [], $paragraphStyle);

        $section->addTextBreak(1);

        // --------------------------------------------------------------------------------------------------------------------------
        // ----------------------------------    TABLA CONTINUACION DE DATOS GENERALES    -------------------------------------------
        // --------------------------------------------------------------------------------------------------------------------------
        // Configurar estilos de tabla
        $table3Style = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 50,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'tblWidth' => 100 * 50, // Forzar ancho completo de la página
        ];
        // Registrar el estilo de la tabla
        $phpWord->addTableStyle('generalDataTable2', $table3Style, $firstRowStyle);

        // Crear la tabla
        $tablaDatosGenerales2 = $section->addTable('generalDataTable2');

        // Primera fila: Encabezado
        $tablaDatosGenerales2->addRow();
        $tablaDatosGenerales2->addCell(10000, ['gridSpan' => 6, 'bgColor' => '47B6C5'])->addText(
            'DATOS GENERALES CADENA DE VALOR PÚBLICO ',
            ['bold' => true, 'size' => 12, 'color' => 'FFFFFF', 'name' => 'Arial Narrow'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        $tablaDatosGenerales2->addRow();
        $tablaDatosGenerales2->addCell(10000, ['gridSpan' => 6, 'bgColor' => '47B6C5'])->addText(
            'GESTIÓN: FORMULACIÓN POA- PPTO AÑO xxxx',
            ['bold' => true, 'size' => 12, 'color' => 'FFFFFF', 'name' => 'Arial Narrow'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        // Segunda fila: Indicador PEG y detalle narrativo
        $tablaDatosGenerales2->addRow();
        $tablaDatosGenerales2->addCell(3000)->addText(
            'Indicador PEG',
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(7000, ['gridSpan' => 5])->addText(
            $data[0]['nombre_indicador_resultado_peg'],
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );

        // Tercera fila: Participantes beneficiados
        $tablaDatosGenerales2->addRow();
        $tablaDatosGenerales2->addCell(3000)->addText(
            'Participantes beneficiados (as):',
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(2000)->addText(
            '# Personas',
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(2000)->addText(
            '', // Espacio en blanco después de # Personas
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(2000)->addText(
            '# Familias',
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(2000)->addText(
            '', // Espacio en blanco después de # Familias
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(2000)->addText(
            '# Hogares',
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        // Cuarta fila: Grupo vulnerable
        $tablaDatosGenerales2->addRow();
        $tablaDatosGenerales2->addCell(3000)->addText(
            'Grupo Vulnerable Priorizado por el Programa/ Proyecto:',
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tablaDatosGenerales2->addCell(7000, ['gridSpan' => 5])->addText(
            '',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $section->addTextBreak(2);

        // --------------------------------------------------------------------------------------------------------------------------
        // ------------------------------------    TABLA DE CRITERIOS DE PROTECCIÓN SOCIAL    ---------------------------------------
        // --------------------------------------------------------------------------------------------------------------------------
        $t4 = mb_convert_encoding(
            'III.	VALIDACIÓN DE LINEAMIENTOS TÉCNICOS',
            'UTF-8',
            'auto'
        );
        $section->addText($t4, ['bold' => true, 'size' => 12, 'name' => 'Calibri'], ['alignment' => 'left']);
        $section->addTextBreak(1);
        // Configurar estilos de la tabla
        $table4Style = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 50,
            'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER,
            'tblWidth' => 100 * 50, // 100% del ancho disponible
        ];
        // Registrar el estilo de la tabla
        $phpWord->addTableStyle('protectionPolicyTable', $table4Style, $firstRowStyle);

        // Crear la tabla
        $tabla4 = $section->addTable('protectionPolicyTable');

        // Fila del encabezado
        $tabla4->addRow();
        $tabla4->addCell(1000, ['bgColor' => '47B6C5'])->addText(
            'N°',
            ['bold' => true, 'size' => 10, 'color' => 'FFFFFF'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000, ['bgColor' => '47B6C5'])->addText(
            'Criterios Política de Protección Social',
            ['bold' => true, 'size' => 10, 'color' => 'FFFFFF'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500, ['bgColor' => '47B6C5'])->addText(
            'VIGENTE',
            ['bold' => true, 'size' => 10, 'color' => 'FFFFFF'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500, ['bgColor' => '47B6C5'])->addText(
            'N/A',
            ['bold' => true, 'size' => 10, 'color' => 'FFFFFF'],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );

        // Agregar las filas de contenido
        // Primera fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '1',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(9000, ['gridSpan' => 5])->addText(
            'Cumplimiento del Plan Estratégico de Gobierno (PEG) 2022-2026',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );


        // Subfilas de la primera fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '1.1',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Vinculación al PEG 2022-2026',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '1.2',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'PEG 2022-2026 Indicadores del Gabinete Social',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        // Segunda fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '2',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Oferta Programática vinculada con la priorización de aldeas en pobreza y pobreza extrema focalizadas por el Programa Red Solidaria',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        // Tercera fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '3',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(9000, ['gridSpan' => 5])->addText(
            'Ley de Equidad y Desarrollo Integral a Personas con Discapacidad',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );

        // Subfilas de la tercera fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '3.1',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Comunicación inclusiva (Sistema BRAILE, intérpretes LESHO, documentación en versión de audio)',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '3.2',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Accesibilidad y empleabilidad',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        // Cuarta fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '4',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(9000, ['gridSpan' => 5])->addText(
            'Ley Integral de Protección al Adulto Mayor y Jubilados',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );

        // Subfilas de la cuarta fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '4.1',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Formación de personal en gerontología y geriatría (área de salud y previsión social)',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '4.2',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Accesibilidad y empleabilidad ',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '4.3',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Promoción de espacios recreativos (clubs, actividades sociales, culturales, emprendimientos, deportivas, etc.)',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '4.4',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Coordinación con DIGAM- SEDESOL',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        // Quinta fila: Acciones para el desarrollo integral
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '5',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(9000, ['gridSpan' => 5])->addText(
            'Acciones para el desarrollo integral de los pueblos originarios y afro hondureños',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );


        // Subfilas para la quinta fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '5.1',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Registro de datos de POAH',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '5.2',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Accesibilidad y empleabilidad',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '5.3',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Formación del personal en abordaje intercultural',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '5.4',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Coordinación con CONAPOA - SEDESOL',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        // Sexta fila: Transversalización del Enfoque de Derechos Humanos
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '6',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(9000, ['gridSpan' => 5])->addText(
            'Transversalización del Enfoque de Derechos Humanos',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );

        // Subfilas para la sexta fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '6.1',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Población meta definida',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '6.2',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'El programa cuenta con información sistematizada que permite conocer la demanda total de apoyos, pero no las características de las y los solicitantes',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '6.3',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'El programa cuenta con información sistematizada que permite conocer la demanda total de apoyos y las características de las y los solicitantes',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '6.4',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Oferta Programática Diferenciada según las necesidades de las y los solicitantes',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        // Séptima fila: Transversalización del Enfoque de Género
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '7',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(9000, ['gridSpan' => 5])->addText(
            'Transversalización del Enfoque de Género',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );

        // Subfilas para la séptima fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '7.1',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Presupuesto etiquetado en género',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '7.2',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Unidad de Genero',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '7.3',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Política Institucional de Genero ',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '7.4',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Coordinación con SEMUJER',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');



        // Octava fila: Transversalización del Enfoque de Desarrollo Territorial 
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '8',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(9000, ['gridSpan' => 5])->addText(
            'Transversalización del Enfoque de Desarrollo Territorial ',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );

        // Subfilas para la octava fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '8.1',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Procesos de consulta popular',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '8.2',
            [],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Procesos de socialización',
            [],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        // Novena Fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '9',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Matriz de Indicadores para Resultados (MIR) DMP- SEDESOL',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');

        // Décima Fila
        $tabla4->addRow();
        $tabla4->addCell(1000)->addText(
            '10',
            ['bold' => true],
            ['alignment' => 'center', 'spaceAfter' => 0]
        );
        $tabla4->addCell(7000)->addText(
            'Registro de la Oferta Programática en ROPI- ODS- SEDESOL',
            ['bold' => true],
            ['alignment' => 'left', 'spaceAfter' => 0]
        );
        $tabla4->addCell(1500)->addText('');
        $tabla4->addCell(1500)->addText('');
        $section->addTextBreak(3);
        //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // --------------------------------------------------- ÚLTIMA PÁGINA -----------------------------------------------------------------------
        //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        $t5 = mb_convert_encoding(
            'IV.	RECOMENDACIONES',
            'UTF-8',
            'auto'
        );
        $section->addText($t5, ['bold' => true, 'size' => 12, 'name' => 'Calibri'/*, 'color' => '47B6C5'*/], ['alignment' => 'left']);

        $section->addTextBreak(1);

        $t6 = mb_convert_encoding(
            'Acciones sugeridas en el marco de desarrollo y bienestar, orientadas a fortalecer la gestión institucional. ',
            'UTF-8',
            'auto'
        );
        $section->addText($t6, ['size' => 11, 'name' => 'Arial Narrow'], ['alignment' => 'left']);

        $section->addTextBreak(1);

        $t7 = mb_convert_encoding(
            'V.	CONCLUSIÓN ',
            'UTF-8',
            'auto'
        );
        $section->addText($t7, ['bold' => true, 'size' => 12, 'name' => 'Calibri'], ['alignment' => 'left']);

        $section->addTextBreak(1);

        $t8 = mb_convert_encoding(
            'La Secretaría de Desarrollo Social, a través de la Dirección de Monitoreo Programático según PCM- brindara asistencia técnica para el monitoreo y evaluación de los programas y proyectos en el Marco de Protección Social. ',
            'UTF-8',
            'auto'
        );
        $t9 = mb_convert_encoding(
            'La Secretaría de Desarrollo Social, a través de la Dirección de Regulación Programática, según PCM 019-2022 en su inciso (o), emite el presente dictamen de viabilidad técnica de las cadenas de valor pública correspondientes al Plan Operativo Anual para la gestión del año xxxx de la Institución XXXXX por encontrarse que cumple y se compromete con los lineamientos técnicos establecidos en el Marco de Protección Social.',
            'UTF-8',
            'auto'
        );
        $t10 = mb_convert_encoding(
            'Dado en la ciudad de Tegucigalpa, a los ** días, del mes de xxxxx, año  xxxx.',
            'UTF-8',
            'auto'
        );
        $section->addText($t8, ['size' => 11, 'name' => 'Arial Narrow'], ['alignment' => 'left']);
        $section->addText($t9, ['size' => 11, 'name' => 'Arial Narrow'], ['alignment' => 'left']);
        $section->addText($t10, ['size' => 11, 'name' => 'Arial Narrow'], ['alignment' => 'left']);
        $section->addTextBreak(3);
        //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // --------------------------------------------------- FIRMAS -----------------------------------------------------------------------
        //--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
        // Crear una nueva tabla para las firmas
        $firmaTable = $section->addTable();

        // Fila única con dos columnas
        $firmaTable->addRow();

        // Primera celda: Aprobado
        $firmaTable->addCell(5000)->addText(
            '__________________________', // Línea superior
            ['name' => 'Arial', 'size' => 10, 'color' => '000000'],
            ['alignment' => 'center']
        );
        $firmaTable->addCell(5000)->addText(
            '__________________________', // Línea superior para la segunda columna
            ['name' => 'Arial', 'size' => 10, 'color' => '000000'],
            ['alignment' => 'center']
        );

        // Segunda fila con el texto "(Nombre, firma y sello)" en ambas celdas
        $firmaTable->addRow();
        $firmaTable->addCell(5000)->addText(
            '(Nombre, firma y sello)', // Texto gris
            ['name' => 'Arial', 'size' => 9, 'italic' => true, 'color' => '808080'],
            ['alignment' => 'center']
        );
        $firmaTable->addCell(5000)->addText(
            '(Nombre, firma y sello)', // Texto gris
            ['name' => 'Arial', 'size' => 9, 'italic' => true, 'color' => '808080'],
            ['alignment' => 'center']
        );

        // Tercera fila con "Aprobado" y "Autorizado"
        $firmaTable->addRow();
        $firmaTable->addCell(5000)->addText(
            'Aprobado', // Título en negrita
            ['name' => 'Arial', 'size' => 10, 'bold' => true],
            ['alignment' => 'center']
        );
        $firmaTable->addCell(5000)->addText(
            'Autorizado', // Título en negrita
            ['name' => 'Arial', 'size' => 10, 'bold' => true],
            ['alignment' => 'center']
        );

        // Cuarta fila con los títulos de los cargos
        $firmaTable->addRow();
        $firmaTable->addCell(5000)->addText(
            'Director (a) de Regulación Programática', // Cargo para "Aprobado"
            ['name' => 'Arial', 'size' => 10],
            ['alignment' => 'center']
        );
        $firmaTable->addCell(5000)->addText(
            'Secretario (a) de Estado en el Despacho de Desarrollo Social', // Cargo para "Autorizado"
            ['name' => 'Arial', 'size' => 10],
            ['alignment' => 'center']
        );

        // Guardar el archivo
        $fileName = 'dictamen_viabilidad.docx';
        $directory = storage_path('app/dictamenes'); // Directorio corregido

        // Crear directorio si no existe
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $tempPath = $directory . '/' . $fileName;

        // Eliminar archivo existente si es necesario
        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        // Guardar el archivo
        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempPath);

        // Descargar el archivo
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}
