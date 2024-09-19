<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {

    db('datosGenerales');

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
          $script = 'and icc.campus_id IN ('.$campus_administrador.')';
        }


        $query_grupo = query("select tg.grupo, tg.grupo_id, tg.nombre_grupo, tg.cupo,tg.situacion_grupo_id, coj.orden_jerarquico, tg.descripcion, tpe.plan_estudio,tc.carrera
        from ".$GLOBALS["db_controlEscolar"].".tr_grupo tg 
        join inter_plan_orden ipo on ipo.plan_orden_id = tg.plan_orden_id
        join cat_orden_jerarquico coj on coj.orden_jerarquico_id = ipo.orden_jerarquico_id
        join tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id 
        join tr_carrera tc on tc.carrera_id = tpe.carrera_id
        join inter_carrera_campus icc on icc.carrera_id = tc.carrera_id
        where tg.estatus !=0 and tg.situacion_grupo_id not in (5,6) ".$script."
        group by tg.grupo_id");


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

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte de grupos');


        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2");

        $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($estiloTituloColumnas);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('ReporteGrupos');

        $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Carrera');
        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'Grado');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', 'Grupo');
        $objPHPExcel->getActiveSheet()->setCellValue('D4', 'Descripción');
        $objPHPExcel->getActiveSheet()->setCellValue('E4', 'Plan de estudio');
        $objPHPExcel->getActiveSheet()->setCellValue('F4', 'Numero de Alumnos');

        $fila = 5;

        if (num($query_grupo)) {
            $json_grupos = array();
            while ($arreglo_grupo = arreglo($query_grupo)) {
                $query_alumnos = query('select count(iag.alumno_id) as num_alumnos from '.$GLOBALS["db_controlEscolar"].'.inter_alumno_grupo iag  join '.$GLOBALS["db_controlEscolar"].'.tr_alumno ta on ta.alumno_id = iag.alumno_id   where ta.estatus =1 and ta.situacion_alumno_id in (1) and iag.estatus=1 and grupo_id = ' . $arreglo_grupo['grupo_id'] . '');
                $arreglo_grupo['num_alumnos'] = arreglo($query_alumnos);
                array_push($json_grupos, $arreglo_grupo);
            }
        }

        foreach ($json_grupos as $json_tmp) {
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15); 
            $objPHPExcel->getDefaultStyle()->applyFromArray($json_tmp);
            // print_r($json_tmp['num_alumnos']);
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, $json_tmp['carrera']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $json_tmp['orden_jerarquico']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $json_tmp['nombre_grupo']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $json_tmp['descripcion']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $fila, $json_tmp['plan_estudio']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $fila, $json_tmp['num_alumnos']['num_alumnos']);
            $fila++;
        }


        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:F" . $fila);

        foreach (range('A', 'D') as $columnID) {
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
        header('Content-Disposition: attachment;filename="reporteGrupos.xls"');
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
