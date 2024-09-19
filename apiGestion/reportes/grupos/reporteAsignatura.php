<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {

    db('controlEscolar');

    if ($_SERVER['REQUEST_METHOD'] == "GET") {

        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        $consulta_campus_administrador = arreglo(query('SELECT GROUP_CONCAT( ica.campus_id ) as campuses
        FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_administrador ta
        JOIN ' . $GLOBALS['db_controlEscolar'] . '.inter_campus_administrador ica ON ta.administrador_id = ica.administrador_id
        WHERE ta.estatus = 1 AND ica.estatus = 1 AND ta.administrador_id = ' . $usuario_id));
        if ($consulta_campus_administrador['campuses'] == '') {
            $script =' ';
        }else{
          $campus_administrador = $consulta_campus_administrador['campuses'];
          $script = 'and iap.campus_id IN ('.$campus_administrador.')';
        }

        $query_grupo = query(" select tg.grupo_id , ca.asignatura,tm.alumno_id,p.nombre, p.primer_apellido, p.segundo_apellido,
         tm.asignatura_grupo_id, tm.calificacion, ta.clave_alumno, ca.asignatura_clave, tg.nombre_grupo, descripcion_tipo_materia
        from tr_grupo tg 
        join inter_asignatura_grupo iag on tg.grupo_id = iag.grupo_id
        join ".$GLOBALS["db_learning"].".tr_materia tm on tm.asignatura_grupo_id = iag.asignatura_grupo_id
        join ".$GLOBALS["db_learning"].".cat_tipo_materia ctm on ctm.tipo_materia_id = tm.tipo_materia_id
        join tr_alumno ta on ta.alumno_id = tm.alumno_id 
        join ".$GLOBALS["db_datosGenerales"].".inter_orden_asignatura ioa on ioa.orden_asignatura_id = iag.orden_asignatura_id
        join ".$GLOBALS["db_datosGenerales"].".cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id
        join ".$GLOBALS["db_datosGenerales"].".personas p on p.persona_id = tm.alumno_id
        join ".$GLOBALS["db_controlEscolar"].".inter_alumno_plan iap on iap.alumno_id = ta.alumno_id
        where tg.estatus= 1 and iag.estatus=1 and tm.estatus=1 and ioa.estatus=1 and ioa.estatus=1 and tg.grupo_id=".$id_grupo." ".$script."
        order by ca.asignatura_id");


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

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte de asignatura');


        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2");

        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($estiloTituloReporte);
        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C2");
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Grupo');

        $objPHPExcel->getActiveSheet()->getStyle('A4:G4')->applyFromArray($estiloTituloColumnas);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('ReporteAsignatura');

        $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Alumno');
        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'Matrícula');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', 'Asignatura');
        $objPHPExcel->getActiveSheet()->setCellValue('D4', 'Clave');
        $objPHPExcel->getActiveSheet()->setCellValue('E4', 'Calificación');
        $objPHPExcel->getActiveSheet()->setCellValue('F4', 'Situación');
        $objPHPExcel->getActiveSheet()->setCellValue('G4', 'Tipo materia');
        $fila = 6;

        if (num($query_grupo)) {
            while ($arreglo_grupo = arreglo($query_grupo)){
                $objPHPExcel->getActiveSheet()->setCellValue('C2', $arreglo_grupo['nombre_grupo']);

                $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15); 
                $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo_grupo);
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, $arreglo_grupo['nombre'].' '.$arreglo_grupo['primer_apellido'].' '.$arreglo_grupo['segundo_apellido']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $arreglo_grupo['clave_alumno']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $arreglo_grupo['asignatura']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $arreglo_grupo['asignatura_clave']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $fila, $arreglo_grupo['calificacion']);
                if (floatval($arreglo_grupo['calificacion']) <= 7) {
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $fila, 'No aprobado');
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue('F' . $fila, 'Aprobado');
                }
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $fila, $arreglo_grupo['descripcion_tipo_materia']);
                $fila++;
            }
        }

        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:G" . $fila);

        foreach (range('A', 'G') as $columnID) {
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
        header('Content-Disposition: attachment;filename="reporteAsignatura.xls"');
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
