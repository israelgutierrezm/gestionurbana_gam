<?php

include '../../jwt.php';
include '../../controlEscolar/class/alumno.class.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';

try {
    db('pagos');

    $alumno = new Alumno();
    $nivel_estudios_id = null;
    $carrera_id = null;
    $tipo_plan_estudio_id = null;
    $plan_estudio_id = null;
    $orden_jerarquico_id = null;
    $grupo_id = null;
    $campus_id = null;
    $situacion_alumno_id = null;
    $situacion_pago_id = null;
    $consulta_personas = ' '; //null;

    function num2alpha($n)
    {
        for ($r = ""; $n >= 0; $n = intval($n / 26) - 1) {
            $r = chr($n % 26 + 0x41) . $r;
        }

        return $r;
    }

    if ($_SERVER['REQUEST_METHOD'] == "GET") {

        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        // $usuario = Auth::GetData(
        //     $jwt
        // );
        if (isset($busqueda) && $busqueda != '' && $busqueda != 'undefined') {
            $consulta_personas = ' ';
            $arreglo_nombres = explode(' ', $busqueda);
            for ($i = 0; $i < sizeof($arreglo_nombres); $i++) {

                $consulta_personas .= ' and (p.nombre like "%' . $arreglo_nombres[$i] . '%" or p.primer_apellido like "%'
                    . $arreglo_nombres[$i] . '%" or p.segundo_apellido like "%' . $arreglo_nombres[$i] . '%") ';
            }
        } else {
            $consulta_personas = '';
        }
        $alumnos = $alumno::busquedaAlumnosPorFiltro($nivel_estudios_id, $carrera_id, $tipo_plan_estudio_id, $plan_estudio_id, $orden_jerarquico_id, $grupo_id, $campus_id, $situacion_alumno_id, $situacion_pago_id, $consulta_personas);
        $json_alumnos = array();
        while ($arreglo_alumno = arreglo($alumnos)) {
            $query_solicitud_pago = query('SELECT solicitud_pago_id
                FROM tr_solicitud_pago tsp
                join tr_pago tp on tp.pago_id = tsp.pago_id
                WHERE persona_id = ' . $arreglo_alumno['alumno_id'] . ' AND tp.estatus=1 and tsp.estatus = 1 AND tsp.estatus_solicitud_pago_id IN (1,2)');
            $arreglo_alumno['pendientes_pago'] = num($query_solicitud_pago);
            array_push($json_alumnos, $arreglo_alumno);
        }
        //print_r($json_alumnos);die;
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getProperties()

            ->setCreator("Temporaris")
            ->setLastModifiedBy("Temporaris")
            ->setTitle("Template Relevé des heures intérimaires")
            ->setSubject("Template excel")
            ->setDescription("Template excel permettant la création d'un ou plusieurs relevés d'heures")
            ->setKeywords("Template excel");

        $estiloTituloColumnas = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => true,
                'size' => 12,
                'color' => array(
                    'rgb' => 'FFFFFF',
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '548DD5'),
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $estiloInformacion = array(
            'font' => array(
                'name' => 'Arial',
                'size' => 12,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($estiloTituloColumnas);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('ReporteFiltros');

        $objPHPExcel->getActiveSheet()
            ->setCellValue('A4', 'LIST')
            ->setCellValue('B4', 'NOMBRE DEL ALUMNO')
            ->setCellValue('C4', 'MATRICULA')
            ->setCellValue('D4', 'MENSUALIDAD PAGADA')
            ->setCellValue('E4', 'MENSUALIDAD CON ADEUDO')
            ->setCellValue('F4', 'TOTAL DE ADEUDO POR GRADO Y GRUPO');

        $fila = 5;
        $num_alumnos = sizeof($json_alumnos);

        for ($i = 0; $i < $num_alumnos; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, $i);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $json_alumnos[$i]['clave_alumno']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $json_alumnos[$i]['nombre']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, '');
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $fila, '');
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $fila, '');

            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':F' . $fila . '')->applyFromArray($estiloInformacion);
            $fila++;
        }
        foreach ($objPHPExcel->getActiveSheet()->getColumnIterator() as $column) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }
        if ($GLOBALS['version'] == 5) {
            // include '../../extras/excel/crear5.php';
            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        } else {
            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel7');
            // include '../../extras/excel/crear7.php';

        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_pagos_' . date("Y-m-d") . '.xls"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

    } else {
        $json = array("status" => 0, "msg" => "Método no aceptado");
    }

    /* Output header */

    // echo json_encode($json);

} catch (Exception $e) {
    echo $e->getMessage();
}
