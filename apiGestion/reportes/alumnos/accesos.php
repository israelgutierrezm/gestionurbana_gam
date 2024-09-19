<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {

    db('datosGenerales');

    if ($_SERVER['REQUEST_METHOD'] == "GET") {

        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        $query_accesos = query('SELECT unix_timestamp(tbs.fecha_inicio) as fecha_inicio, unix_timestamp(tbs.fecha_fin) as fecha_fin, 
        tbs.dispositivo, tbs.ip, tbs.navegador
             FROM tr_bitacora_sesion tbs 
             WHERE tbs.usuario_id = ' . $id_persona . ' order by fecha_inicio desc');

        $arreglo_nombre = arreglo(query('SELECT p.nombre, p.primer_apellido, p.segundo_apellido 
             FROM personas p 
             WHERE p.persona_id = ' . $id_persona . ''));

        $nombre =$arreglo_nombre['nombre'].' '.$arreglo_nombre['primer_apellido'].' '.$arreglo_nombre['segundo_apellido'];

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getProperties()

            ->setCreator("Temporaris")
            ->setLastModifiedBy("Temporaris")
            ->setTitle("Template Relevé des heures intérimaires")
            ->setSubject("Template excel")
            ->setDescription("Template excel permettant la création d'un ou plusieurs relevés d'heures")
            ->setKeywords("Template excel");

        $estiloTituloReporte = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,
                'size' => 13
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F9FD00')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $estiloTituloColumnas = array(
            'font' => array(
                'name'  => 'Arial',
                'bold'  => true,
                'size' => 11,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '538DD5')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $estiloTituloGrado = array(
            'font' => array(
                'name'  => 'Arial',
                'bold'  => true,
                'size' => 11,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '538DD5')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $estiloInformacion = new PHPExcel_Style();
        $estiloInformacion->applyFromArray(array(
            'font' => array(
                'name'  => 'Arial',
                'size' => 10,
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ));

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte de accesos');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Nombre');
        $objPHPExcel->getActiveSheet()->setCellValue('C2', $nombre);




        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($estiloTituloReporte);

        $objPHPExcel->getActiveSheet()->getStyle('A4:E4')->applyFromArray($estiloTituloColumnas);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('Accesos');

        $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Dispositivo');
        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'Fecha de entrada');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', 'Fecha de salida');
        $objPHPExcel->getActiveSheet()->setCellValue('D4', 'IP');
        $objPHPExcel->getActiveSheet()->setCellValue('E4', 'Navegador');
        $fila = 5;
        date_default_timezone_set('America/Mexico_City');

        $json = array();
        while ($arreglo = arreglo($query_accesos)) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, $arreglo['dispositivo']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, date("d/m/Y H:i:s", $arreglo['fecha_inicio']));
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, date("d/m/Y H:i:s", $arreglo['fecha_fin']));
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $arreglo['ip']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $fila, $arreglo['navegador']);
                $fila++;
        }

        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:E4" . $fila);
        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C2");

        foreach (range('A5', 'E') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
        }


        if ($GLOBALS['version'] == 5) {
            // include '../../extras/excel/crear5.php';  
            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        } else {
            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel7');
            // include '../../extras/excel/crear7.php';  

        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="reporteAccesosAlumno.xls"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    } else {
        $json = array("status" => 0, "msg" => "Método no aceptado");
    }

    /* Output header */

    // echo json_encode($json);

} catch (Exception $e) {
    echo  $e->getMessage();
}
