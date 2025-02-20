<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Http\Controllers\Api\PoaController;
use App\Http\Controllers\Api\ImpactoController;
use App\Http\Controllers\Api\ResultadoController;
use App\Http\Controllers\Api\ObjetivosOperativosController;


class MatrizPoaController extends Controller
{

    public function generarExcel($codigo_poa)
    {
        $poaController = new PoaController();
        $impactoController = new ImpactoController();
        $resultadoController = new ResultadoController();
        $objetivosOperativosController = new ObjetivosOperativosController();
        $poaResponse = $poaController->getAllDataPoa($codigo_poa);
        $datosPOA = $poaController->getPoa($codigo_poa);
        $datosImpactos = $impactoController->getImpactosByPoaId($codigo_poa);
        $datosResultados = $resultadoController->getResultadosByPoa($codigo_poa);
        $datosObjetivos = $objetivosOperativosController->getObjetivosOperativosByPoa($codigo_poa);

        // Extraer JSON de la respuesta de Laravel
        $poaArray = $poaResponse->getData(true);
        $poaDatosArray = $datosPOA->getData(true);
        $impactosArray = $datosImpactos->getData(true);
        $resultadosArray = $datosResultados->getData(true);
        $objetivosArray = $datosObjetivos->getData(true);

        // Verificar si el JSON tiene éxito y la clave 'data' existe
        if (!isset($poaArray['success']) || !$poaArray['success'] || !isset($poaArray['data'])) {
            return response()->json([
                'success' => false,
                'message' => 'Error: No se encontraron datos para el poa proporcionado.'
            ], 400);
        }

        // Extraer los datos del POA
        $dataPOA = $poaArray['data'];
        $listadoDatosPoa = $poaDatosArray['data'];

        // **Cargar el archivo Excel**
        $filePath = public_path('docs/matriz_poa.xlsx');
        $spreadsheet = IOFactory::load($filePath);

        // **Modificar la primera hoja (Matriz de planificación)** 
        $spreadsheet->setActiveSheetIndex(0);
        $hoja1 = $spreadsheet->getActiveSheet();

        $columns = ['D', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];

        foreach ($dataPOA as $item) {
            $hoja1->setCellValue('C6', $item['nombre_institucion']);
            $hoja1->setCellValue('C7', $item['mision_institucion']);
            $hoja1->setCellValue('C8', $item['vision_institucion']);
            $hoja1->setCellValue('C9', $item['nombre_programa']);
            $hoja1->setCellValue('C10', $item['descripcion_programa']);
            $hoja1->setCellValue('C11', $item['objetivo_programa']);
        }

        $politicaRow = 13;
        foreach($listadoDatosPoa['Politicas'] as $politica){
            $hoja1->setCellValue($columns[$politicaRow - 13] . $politicaRow, $politica['nombre_politica_publica']);
            $politicaRow++;
        }
        
        $an_odsRow = 15;
        foreach($listadoDatosPoa['An_ODs'] as $an_ods){
            $hoja1->setCellValue($columns[$an_odsRow - 15] . $an_odsRow, $an_ods['objetivo_an_ods']);
            $hoja1->setCellValue($columns[$an_odsRow - 15] . ($an_odsRow + 1), $an_ods['meta_an_ods']);
            $hoja1->setCellValue($columns[$an_odsRow - 15] . ($an_odsRow + 2), $an_ods['indicador_an_ods']);
            $an_odsRow++;
        }

        $vision_paisRow = 19;
        foreach($listadoDatosPoa['Vision_Pais'] as $vision_pais){
            $hoja1->setCellValue($columns[$vision_paisRow - 19] . $vision_paisRow, $vision_pais['objetivo_vision_pais']);
            $hoja1->setCellValue($columns[$vision_paisRow - 19] . ($vision_paisRow + 1), $vision_pais['meta_vision_pais']);
            $vision_paisRow++;
        }

        $pegRow = 22;
        foreach($listadoDatosPoa['PEG'] as $peg){
            $hoja1->setCellValue($columns[$pegRow - 22] . $pegRow, $peg['nombre_gabinete']);
            $hoja1->setCellValue($columns[$pegRow - 22] . ($pegRow + 1), $peg['nombre_eje_estrategico']);
            $hoja1->setCellValue($columns[$pegRow - 22] . ($pegRow + 2), $peg['nombre_objetivo_peg']);
            $hoja1->setCellValue($columns[$pegRow - 22] . ($pegRow + 3), $peg['nombre_resultado_peg']);
            $hoja1->setCellValue($columns[$pegRow - 22] . ($pegRow + 4), $peg['nombre_indicador_resultado_peg']);
            $pegRow++;
        }
        
        $impRow = 31;
        foreach($impactosArray['impactos'] as $imp){
            $hoja1->setCellValue('B' . $impRow, $imp['resultado_final']);
            $hoja1->setCellValue('C' . $impRow, $imp['indicador_resultado_final']);
            $impRow+=6;
        }

        $resRow = 31;
        foreach($resultadosArray['resultados'] as $res){
            $hoja1->setCellValue('D' . $resRow, $res['resultado_institucional']);
            $hoja1->setCellValue('E' . $resRow, $res['indicador_resultado_institucional']);
            $resRow+=6;
        }

        $objRow = 31;
        foreach($objetivosArray['objetivos'] as $obj){
            $hoja1->setCellValue('G' . $objRow, $obj['objetivo_operativo']);
            $hoja1->setCellValue('H' . $objRow, $obj['subprograma_proyecto']);
            $objRow+=37;
        }

        // Guardar el archivo temporalmente
        $fileName = 'archivo_excel.xlsx';
        $tempPath = storage_path('app/' . $fileName);

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempPath);

        //return $poaArray;
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}
