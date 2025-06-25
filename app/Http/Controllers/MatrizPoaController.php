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
use App\Http\Controllers\Api\ProductosFinalesController;
use App\Http\Controllers\Api\ProductosIntermediosController;
use App\Http\Controllers\Api\ActividadInsumoController;


class MatrizPoaController extends Controller
{

    public function generarExcel($codigo_poa)
    {
        $poaController = new PoaController();
        $impactoController = new ImpactoController();
        $resultadoController = new ResultadoController();
        $objetivosOperativosController = new ObjetivosOperativosController();
        $productosFinalesController = new ProductosFinalesController();
        $productosIntermediosController = new ProductosIntermediosController();
        $actividadInsumoController = new ActividadInsumoController();
        $poaResponse = $poaController->getAllDataPoa($codigo_poa);
        $datosPOA = $poaController->getPoa($codigo_poa);
        $datosImpactos = $impactoController->getImpactosByPoaId($codigo_poa);
        $datosResultados = $resultadoController->getResultadosByPoa($codigo_poa);
        $datosObjetivos = $objetivosOperativosController->getObjetivosOperativosByPoa($codigo_poa);
        $datosProductosFinales = $productosFinalesController->getProductosFinalesByPoa($codigo_poa);
        $datosProductosIntermedios = $productosIntermediosController->getProductosIntermediosByPoa($codigo_poa);
        $datosActividadInsumo = $actividadInsumoController->getActividadesInsumosByPoaId($codigo_poa);

        // Extraer JSON de la respuesta de Laravel
        $poaArray = $poaResponse->getData(true);
        $poaDatosArray = $datosPOA->getData(true);
        $impactosArray = $datosImpactos->getData(true);
        $resultadosArray = $datosResultados->getData(true);
        $objetivosArray = $datosObjetivos->getData(true);
        $productosFinalesArray = $datosProductosFinales->getData(true);
        $productosIntermediosArray = $datosProductosIntermedios->getData(true);
        $actividadInsumoArray = $datosActividadInsumo->getData(true);

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
        foreach ($listadoDatosPoa['Politicas'] ?? [] as $politica) {
            $hoja1->setCellValue($columns[$politicaRow - 13] . $politicaRow, $politica['nombre_politica_publica']);
            $politicaRow++;
        }

        $an_odsRow = 15;
        foreach ($listadoDatosPoa['An_ODs'] ?? [] as $an_ods) {
            $hoja1->setCellValue($columns[$an_odsRow - 15] . $an_odsRow, $an_ods['objetivo_an_ods']);
            $hoja1->setCellValue($columns[$an_odsRow - 15] . ($an_odsRow + 1), $an_ods['meta_an_ods']);
            $hoja1->setCellValue($columns[$an_odsRow - 15] . ($an_odsRow + 2), $an_ods['indicador_an_ods']);
            $an_odsRow++;
        }

        $vision_paisRow = 19;
        foreach ($listadoDatosPoa['Vision_Pais'] ?? [] as $vision_pais) {
            $hoja1->setCellValue($columns[$vision_paisRow - 19] . $vision_paisRow, $vision_pais['objetivo_vision_pais']);
            $hoja1->setCellValue($columns[$vision_paisRow - 19] . ($vision_paisRow + 1), $vision_pais['meta_vision_pais']);
            $vision_paisRow++;
        }

        $pegRow = 22;
        foreach ($listadoDatosPoa['PEG'] ?? [] as $peg) {
            $hoja1->setCellValue($columns[$pegRow - 22] . $pegRow, $peg['nombre_gabinete']);
            $hoja1->setCellValue($columns[$pegRow - 22] . ($pegRow + 1), $peg['nombre_eje_estrategico']);
            $hoja1->setCellValue($columns[$pegRow - 22] . ($pegRow + 2), $peg['nombre_objetivo_peg']);
            $hoja1->setCellValue($columns[$pegRow - 22] . ($pegRow + 3), $peg['nombre_resultado_peg']);
            $hoja1->setCellValue($columns[$pegRow - 22] . ($pegRow + 4), $peg['nombre_indicador_resultado_peg']);
            $pegRow++;
        }

        // validar primero el array de impactos
        if (!empty($impactosArray['impactos'])) {
            $impRow = 31;
            foreach ($impactosArray['impactos'] as $imp) {
                $hoja1->setCellValue('B' . $impRow, $imp['resultado_final']);
                $hoja1->setCellValue('C' . $impRow, $imp['indicador_resultado_final']);
                $impRow += 6;
            }
        }

        // validar primero el array de resultados
        if (!empty($resultadosArray['resultados'])) {
            $resRow = 31;
            foreach ($resultadosArray['resultados'] as $res) {
                $hoja1->setCellValue('D' . $resRow, $res['resultado_institucional']);
                $hoja1->setCellValue('E' . $resRow, $res['indicador_resultado_institucional']);
                $resRow += 6;
            }
        }

        // validar primero el array de objetivos operativos
        if (!empty($objetivosArray['objetivos'])) {
            $objRow = 31;
            foreach ($objetivosArray['objetivos'] as $obj) {
                $hoja1->setCellValue('G' . $objRow, $obj['objetivo_operativo']);
                $hoja1->setCellValue('H' . $objRow, $obj['subprograma_proyecto']);
                $objRow += 37;
            }
        }

        $prodRow = 31; // Fila inicial
        $contProd = 1;


        $colsFinal = ['I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R'];
        $colsIntermedio = ['T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC'];

        foreach ($productosFinalesArray['productos_finales'] ?? [] as $prod) {
            // --- 1. Calcula cuántas filas necesitas para este producto final
            $prodIntermedios = array_values(array_filter($productosIntermediosArray['productos_intermedios'], function ($prodInter) use ($prod) {
                return $prodInter['codigo_producto_final'] == $prod['codigo_producto_final'];
            }));
            $numFilasFinal = 0;
            $actividadesPorInter = [];
            foreach ($prodIntermedios as $prodInter) {
                $actividades = array_values(array_filter(
                    $actividadInsumoArray['actividades_insumos']['ActividadesInsumos'],
                    function ($act) use ($prodInter) {
                        return $act['CodigoProductoIntermedio'] == $prodInter['codigo_producto_intermedio'];
                    }
                ));
                $cantActs = max(count($actividades), 1); // si no tiene actividades, cuenta como 1
                $actividadesPorInter[] = ['prodInter' => $prodInter, 'actividades' => $actividades, 'count' => $cantActs];
                $numFilasFinal += $cantActs;
            }
            if ($numFilasFinal === 0) $numFilasFinal = 1; // por si no tiene intermedios

            // --- 2. Merge vertical de columnas de producto final
            $inicioFilaFinal = $prodRow;
            $finFilaFinal = $prodRow + $numFilasFinal - 1;
            foreach ($colsFinal as $col) {
                $hoja1->mergeCells($col . $inicioFilaFinal . ':' . $col . $finFilaFinal);
            }

            // --- 3. Imprimir solo una vez los datos del producto final (en la primera fila del bloque)
            $hoja1->setCellValue('I' . $inicioFilaFinal, $contProd);
            $hoja1->setCellValue('J' . $inicioFilaFinal, $prod['producto_final']);
            $hoja1->setCellValue('K' . $inicioFilaFinal, $prod['indicador_producto_final']);
            $hoja1->setCellValue('L' . $inicioFilaFinal, '');
            $hoja1->setCellValue('M' . $inicioFilaFinal, $prod['programa']);
            $hoja1->setCellValue('N' . $inicioFilaFinal, $prod['subprograma']);
            $hoja1->setCellValue('O' . $inicioFilaFinal, $prod['proyecto']);
            $hoja1->setCellValue('P' . $inicioFilaFinal, $prod['actividad']);
            $hoja1->setCellValue('Q' . $inicioFilaFinal, $prod['costo_total_aproximado']);
            $hoja1->setCellValue('R' . $inicioFilaFinal, $prod['nombre_obra']);
            $contProd++;

            // --- 4. Para cada producto intermedio, merge y datos
            $prodInterRow = $prodRow;
            foreach ($actividadesPorInter as $item) {
                $prodInter = $item['prodInter'];
                $actividades = $item['actividades'];
                $cantActs = $item['count'];
                $inicioFilaInter = $prodInterRow;
                $finFilaInter = $prodInterRow + $cantActs - 1;

                // Merge columnas del producto intermedio
                foreach ($colsIntermedio as $col) {
                    $hoja1->mergeCells($col . $inicioFilaInter . ':' . $col . $finFilaInter);
                }

                // Imprime datos del producto intermedio (en la primera fila de su bloque)
                $hoja1->setCellValue('T' . $inicioFilaInter, $prodInter['producto_intermedio']);
                $hoja1->setCellValue('U' . $inicioFilaInter, $prodInter['indicador_producto_intermedio']);
                $hoja1->setCellValue('V' . $inicioFilaInter, '');
                $hoja1->setCellValue('W' . $inicioFilaInter, $prodInter['programa']);
                $hoja1->setCellValue('X' . $inicioFilaInter, $prodInter['subprograma']);
                $hoja1->setCellValue('Y' . $inicioFilaInter, $prodInter['proyecto']);
                $hoja1->setCellValue('Z' . $inicioFilaInter, $prodInter['actividad']);
                $hoja1->setCellValue('AA' . $inicioFilaInter, $prodInter['fuente_financiamiento']);
                $hoja1->setCellValue('AB' . $inicioFilaInter, $prodInter['ente_de_financiamiento']);
                $hoja1->setCellValue('AC' . $inicioFilaInter, $prodInter['costro_aproximado']);

                // --- 5. Imprime cada actividad en su fila
                if (count($actividades) == 0) {

                    $prodInterRow++;
                } else {
                    foreach ($actividades as $i => $actInsumo) {
                        $filaActual = $inicioFilaInter + $i;
                        $hoja1->setCellValue('AD' . $filaActual, $i+1);
                        $hoja1->setCellValue('AE' . $filaActual, $actInsumo['Actividad']);
                        $hoja1->setCellValue('AF' . $filaActual, $actInsumo['InsumoPACC']);
                        $hoja1->setCellValue('AG' . $filaActual, $actInsumo['InsumoNoPACC']);
                    }
                    $prodInterRow += $cantActs;
                }
            }

            // Si un producto final no tiene productos intermedios, igual deja la fila (puedes personalizar esto)
            if (count($actividadesPorInter) == 0) {
                $prodRow++;
            } else {
                $prodRow += $numFilasFinal;
            }
        }


        // Guardar el archivo temporalmente
        $fileName = 'archivo_excel.xlsx';
        $tempPath = storage_path('app/' . $fileName);

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
        // return response(
        //     [
        //         'message' => 'Archivo generado exitosamente',
        //         'productos finales' => $productosFinalesArray['productos_finales'] ?? [],
        //         'productos intermedios' => $productosIntermediosArray['productos_intermedios'] ?? [],
        //         'actividades insumos' => $actividadInsumoArray['actividades_insumos'] ?? [],
        //     ]
        // );
    }
}
