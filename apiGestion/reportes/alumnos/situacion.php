<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {

    db('seguimiento');

    if ($_SERVER['REQUEST_METHOD'] == "GET") {

        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        /*$usuario = Auth::GetData(
            $jwt  
        );*/

        // $arreglo_plan = arreglo(query('SELECT tpe.plan_estudio_id,plan_estudio, tpe.carrera_id, tc.carrera
        // from '.$GLOBALS["db_datosGenerales"].'.tr_plan_estudios tpe
        // join '.$GLOBALS["db_datosGenerales"].'.tr_carrera tc on tc.carrera_id = tpe.carrera_id
        // where plan_estudio_id ='.$id_plan_estudios.' and tpe.estatus = 1 and tc.estatus = 1'));



        //   $query=query('
        //   select nombre as nombre_alumno, p.primer_apellido, p.segundo_apellido, cd.nombre_documento as documento,
        //   concat ("https://plataformaestudy.com.mx/uc/estudy",ie.url) as url
        //   from inter_expediente ie 
        //   join cat_documento cd on cd.documento_id = ie.documento_id
        //   join '.$GLOBALS['db_datosGenerales'].'.personas p on ie.aspirante_id = p.persona_id
        //   where ie.estatus=1 and cd.estatus =1 and p.estatus=1 order by nombre, primer_apellido, segundo_apellido');

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

        $query = query('
        select tc.carrera, COUNT(csa.situacion_alumno_id) as numero_alumnos, csa.situacion_alumno_descripcion,csa.situacion_alumno_id  from '.$GLOBALS["db_controlEscolar"].'.tr_alumno ta
        join '.$GLOBALS["db_controlEscolar"].'.cat_situacion_alumno csa on csa.situacion_alumno_id = ta.situacion_alumno_id 
        join '.$GLOBALS["db_controlEscolar"].'.inter_alumno_plan iap on iap.alumno_id = ta.alumno_id
        join '.$GLOBALS["db_datosGenerales"].'.inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id 
        join '.$GLOBALS["db_datosGenerales"].'.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id 
        join '.$GLOBALS["db_datosGenerales"].'.tr_carrera tc on tc.carrera_id = tpe.carrera_id 
        where ta.estatus = 1 and iap.estatus = 1 and ipo.estatus=1 and tpe.estatus=1 and tc.estatus=1 '.$script.' 
        group by tc.carrera, csa.situacion_alumno_id');



        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getProperties()


            ->setCreator("Temporaris")
            ->setLastModifiedBy("Temporaris")
            ->setTitle("Template Relevé des heures intérimaires")
            ->setSubject("Template excel")
            ->setDescription("Template excel permettant la création d'un ou plusieurs relevés d'heures")
            ->setKeywords("Template excel");

        $objPHPExcel->setActiveSheetIndex(0);

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

        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);

        $objPHPExcel->getActiveSheet()->getStyle('A3:C3')->applyFromArray($estiloTituloColumnas);


        $objPHPExcel->getActiveSheet()->setTitle('SituacionAlumnos');


        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Situación de alumnos.');



        $objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Carrera');
        $objPHPExcel->getActiveSheet()->setCellValue('B3', 'Situación');
        $objPHPExcel->getActiveSheet()->setCellValue('C3', 'Número de alumnos');


        $fila = 4;
        while ($arreglo =  arreglo($query)) {
            $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15);
            $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo);

            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, $arreglo['carrera']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $arreglo['situacion_alumno_descripcion']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $arreglo['numero_alumnos']);
            $fila++;
        }
        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:C" . $fila);

        foreach (range('A', 'C') as $columnID) {
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
        header('Content-Disposition: attachment;filename="SituacionAlumnos.xls"');
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
