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


class MatrizPoaController extends Controller
{

    public function generarExcel($codigo_poa)
    {
        $poaController = new PoaController();
        $poaResponse = $poaController->getPoa($codigo_poa);

        // Extraer JSON de la respuesta de Laravel
        $poaArray = $poaResponse->getData(true);

        // Verificar si el JSON tiene éxito y la clave 'data' existe
        if (!isset($poaArray['success']) || !$poaArray['success'] || !isset($poaArray['data'])) {
            return response()->json([
                'success' => false,
                'message' => 'Error: No se pudieron obtener los datos correctamente.'
            ], 400);
        }

        // Extraer los datos del POA
        $data = $poaArray['data'];

        // **Cargar el archivo Excel**
        $filePath = public_path('docs/matriz_poa.xlsx');
        $spreadsheet = IOFactory::load($filePath);

        // **Modificar la primera hoja (Matriz de planificación)** 
        $spreadsheet->setActiveSheetIndex(0);
        $hoja1 = $spreadsheet->getActiveSheet();

        $row = 2; // Iniciar en la segunda fila (suponiendo encabezados en la fila 1)
        foreach ($data['Programas_Poa'] as $item) {
            $hoja1->setCellValue('C6', $item['nombre_institucion']);
            $hoja1->setCellValue('B', $item['codigo_poa']);
            $hoja1->setCellValue('C', $item['codigo_objetivo_vp']);
            $hoja1->setCellValue('D', $item['codigo_meta_vp']);
            $hoja1->setCellValue('E', $item['estado_vp']);
            $row++;
        }

        // Guardar el archivo temporalmente
        $fileName = 'archivo_excel.xlsx';
        $tempPath = storage_path('app/' . $fileName);

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempPath);

        return $data['Vision_Pais'];
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}
