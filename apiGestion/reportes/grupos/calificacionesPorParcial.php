<?php

include '../../jwt.php';
include '../../controlEscolar/class/alumno.class.php';
include '../../controlEscolar/class/asignaturagrupo.class.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';

try {
    db('controlEscolar');

    $grupo = new AsignaturaGrupo();

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
        $arrayMateriaDocente = array();
        $arrayAlumnoCalificacion = array();
        $alumnosTotal = array();

        $query = query('SELECT iag.alumno_id , ta.clave_alumno, p.nombre ,p.primer_apellido, p.segundo_apellido
                    FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg
                    JOIN ' . $GLOBALS["db_controlEscolar"] . '.inter_alumno_grupo iag ON iag.grupo_id = tg.grupo_id
                    JOIN ' . $GLOBALS['db_datosGenerales'] . '.personas p ON p.persona_id = iag.alumno_id
                    JOIN ' . $GLOBALS["db_controlEscolar"] . '.tr_alumno ta ON ta.alumno_id = iag.alumno_id
                    WHERE tg.estatus = 1 AND iag.estatus = 1 AND ta.estatus = 1 AND iag.grupo_id =' . $id_grupo . ' ORDER BY p.primer_apellido, p.segundo_apellido, p.nombre asc');

        $query_materias_grupo = query('  SELECT p.nombre, p.primer_apellido, p.segundo_apellido , ca.asignatura , ca.asignatura_id
                    FROM ' . $GLOBALS["db_controlEscolar"] . '.inter_asignatura_grupo iag
                    JOIN ' . $GLOBALS['db_datosGenerales'] . '.inter_orden_asignatura ioa ON iag.orden_asignatura_id = ioa.orden_asignatura_id
                    JOIN ' . $GLOBALS['db_datosGenerales'] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
                    LEFT JOIN ' . $GLOBALS["db_learning"] . '.tr_materia tm ON iag.asignatura_grupo_id = tm.asignatura_grupo_id
                    LEFT JOIN ' . $GLOBALS["db_controlEscolar"] . '.inter_docente_asignatura_grupo idag ON idag.asignatura_grupo_id = iag.asignatura_grupo_id
                    LEFT JOIN ' . $GLOBALS["db_controlEscolar"] . '.tr_docente td ON td.docente_id = idag.docente_id
                    LEFT JOIN ' . $GLOBALS['db_datosGenerales'] . '.personas p ON p.persona_id = idag.docente_id
                    WHERE iag.estatus = 1 AND ioa.estatus = 1 AND ca.estatus = 1 AND iag.grupo_id = ' . $id_grupo . ' GROUP BY ca.asignatura ORDER BY ca.asignatura_id ');

        $num_materias = num($query_materias_grupo);
        $materias_cabecera = array();

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
                'size' => 11,
                'color' => array(
                    'rgb' => 'FFFFFF',
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'EE4B2B'),
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

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(3);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(45);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:A5');
        $objPHPExcel->getActiveSheet()->mergeCells('B1:B5');
        $objPHPExcel->getActiveSheet()->mergeCells('C1:C5');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', 'Mat');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Nombre');
        $objPHPExcel->getActiveSheet()->getStyle('A1:' . 'C6')->applyFromArray($estiloTituloColumnas);
        $num = 3;
        while ($arreglo_mg = arreglo($query_materias_grupo)) {
            $objPHPExcel->getActiveSheet()->mergeCells(num2alpha($num) . '1:' . num2alpha($num + 4) . '1');
            if (isset($arreglo_mg['nombre'])) {
                $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($num) . '1', $arreglo_mg['nombre']);
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($num) . '1', '');
            }
            $objPHPExcel->getActiveSheet()->mergeCells(num2alpha($num) . '2:' . num2alpha($num + 4) . '5');
            if (isset($arreglo_mg['asignatura'])) {
                $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($num) . '2', $arreglo_mg['asignatura']);
            } else {
                $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($num) . '1', 'alguna asignatura');
            }
            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($num) . '6', 'P1');
            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($num + 1) . '6', 'P2');
            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($num + 2) . '6', 'PP');
            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($num + 3) . '6', 'EO');
            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($num + 4) . '6', 'EF');

            $objPHPExcel->getActiveSheet()->getStyle(num2alpha($num) . '1:' . num2alpha($num + 4) . '6')->applyFromArray($estiloTituloColumnas);
            $num = $num + 5;
        }

        for ($i = 3; $i < (3 + ($num_materias * 5)); $i++) {
            $alfa = num2alpha($i);
            $objPHPExcel->getActiveSheet()->getColumnDimension($alfa)->setWidth(5);
        }

        $num = 1;
        while ($alumnos = arreglo($query)) {
            $fila_actual = $num + 6;
            $alumnos['nombre_completo'] = $alumnos['primer_apellido'].' '.$alumnos['segundo_apellido'].' '.$alumnos['nombre'];
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila_actual, $num);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila_actual, $alumnos['clave_alumno']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila_actual, $alumnos['nombre_completo']);

            $query_materias = query('SELECT ca.asignatura , ca.asignatura_id , tm.materia_id
                    FROM ' . $GLOBALS["db_controlEscolar"] . '.inter_asignatura_grupo iag
                    JOIN ' . $GLOBALS['db_datosGenerales'] . '.inter_orden_asignatura ioa ON iag.orden_asignatura_id = ioa.orden_asignatura_id
                    JOIN ' . $GLOBALS['db_datosGenerales'] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
                    LEFT JOIN ' . $GLOBALS["db_learning"] . '.tr_materia tm ON iag.asignatura_grupo_id = tm.asignatura_grupo_id
                    WHERE iag.estatus = 1 AND ioa.estatus = 1 AND ca.estatus = 1 AND iag.grupo_id = ' . $id_grupo . ' and tm.alumno_id =' . $alumnos['alumno_id'] . ' ORDER BY ca.asignatura_id');

            $col = 3;
            while ($materias = arreglo($query_materias)) {
                $query_calificaciones = query('SELECT
                tmfa.materia_id, tmfa.actividad_id, ta.actividad_nombre, tmfa.calificacion, tmfa.materia_fecha_actividad_id, tmfa.estatus_actividad_id,cea.estatus_actividad,cea.icono
                     from ' . $GLOBALS["db_learning"] . '.tr_materia_fecha_actividad tmfa
                     join ' . $GLOBALS["db_learning"] . '.tr_materia tm ON tm.materia_id = tmfa.materia_id
                     join ' . $GLOBALS["db_learning"] . '.tr_actividad ta ON tmfa.actividad_id = ta.actividad_id
                     join ' . $GLOBALS["db_learning"] . '.cat_estatus_actividad cea on cea.estatus_actividad_id = tmfa.estatus_actividad_id
                     where ta.estatus = 1 AND tmfa.materia_id = ' . $materias['materia_id'] . ' and ta.tipo_actividad_id = 5 and tmfa.estatus = 1 and tmfa.estatus_actividad_id !=8
                     order by tmfa.actividad_id');
                while ($calificaciones = arreglo($query_calificaciones)) {
                    $alfab0 = num2alpha($col);
                    if ($calificaciones['actividad_nombre'] === 'Calificación Parcial 1') {
                        if (isset($calificaciones['calificacion'])) {
                            $P1 = $calificaciones['calificacion'];
                            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($col) . $fila_actual, $calificaciones['calificacion']);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($col) . $fila_actual, '-');
                        }
                    } elseif ($calificaciones['actividad_nombre'] === 'Calificación Parcial 2') {
                        if (isset($calificaciones['calificacion'])) {
                            $P2 = $calificaciones['calificacion'];
                            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($col + 1) . $fila_actual, $calificaciones['calificacion']);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($col + 1) . $fila_actual, '-');
                        }
                    } elseif ($calificaciones['actividad_nombre'] === 'Calificación Examen Ordinario') {
                        if (isset($calificaciones['calificacion'])) {
                            $EO = $calificaciones['calificacion'];
                            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($col + 3) . $fila_actual, $calificaciones['calificacion']);
                        } else {
                            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($col + 3) . $fila_actual, '-');
                        }
                    }
                    //sacar suma de pp y cf
                    if (isset($P1) && isset($P2)) {
                        $PP = ($P1 + $P2) / 2;
                        $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($col + 2) . $fila_actual, $PP);
                        if (isset($EO)) {
                            $CF = ($PP + $EO) / 2;
                            $objPHPExcel->getActiveSheet()->setCellValue(num2alpha($col + 4) . $fila_actual, $EO);
                        }
                    }
                }
                $objPHPExcel->getActiveSheet()->getStyle('A' . $fila_actual . ':' . num2alpha($col + 4) . $fila_actual)->applyFromArray($estiloInformacion);
                $col = $col + 5;
            }
            $num++;
        }
        if ($GLOBALS['version'] == 5) {
            // include '../../extras/excel/crear5.php';
            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        } else {
            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel7');
            // include '../../extras/excel/crear7.php';

        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_calificaciones_grupo_' . $id_grupo . '_' . date("Y-m-d") . '.xls"');
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
