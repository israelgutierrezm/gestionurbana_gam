<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {

    db('datosGenerales');

    if ($_SERVER['REQUEST_METHOD'] == "GET") {

        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        $arreglo_ciclo = arreglo(query('select ciclo_desc from '.$GLOBALS["db_controlEscolar"].'.cat_ciclo where ciclo_id ='.$id_ciclo));

        $query= query('select orden_jerarquico_id,orden_jerarquico,carrera, tipo_plan_estudio as modalidad,
        "SEP" as "dependencia",
        rvoe,
        carrera_id,
        nivel_estudios, substring(rvoe,25) as fecha_expediencion_rvoe
        ,total_periodos,periodo, total_creditos,
        DATE_SUB(fecha_inicio, INTERVAL 15 DAY) as pre_inicio_ciclo,
        fecha_inicio as inicio_ciclo, fecha_fin as fin_ciclo 
        from (select @id_ciclo := '.$id_ciclo.' ciclo_id) as vistas,'.$GLOBALS["db_reporte"].'.informacion_ciclo 
        group by carrera_id, orden_jerarquico_id
        ');


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

        $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Reporte estadistico para 911');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Ciclo');
        $objPHPExcel->getActiveSheet()->setCellValue('C2', $arreglo_ciclo['ciclo_desc']);
        



        $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
        $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($estiloTituloReporte);

        $objPHPExcel->getActiveSheet()->getStyle('A4:AN4')->applyFromArray($estiloTituloColumnas);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('Reporte911');

        $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Carrera');
        $objPHPExcel->getActiveSheet()->setCellValue('B4', 'Modalidad');
        $objPHPExcel->getActiveSheet()->setCellValue('C4', 'Dependencia');
        $objPHPExcel->getActiveSheet()->setCellValue('D4', 'rvoe');
        $objPHPExcel->getActiveSheet()->setCellValue('E4', 'Fecha de expedición RVOE');
        $objPHPExcel->getActiveSheet()->setCellValue('F4', 'Nivel de estudios');
        $objPHPExcel->getActiveSheet()->setCellValue('G4', 'Total de periodos');
        $objPHPExcel->getActiveSheet()->setCellValue('H4', 'Tipo de periodo');
        $objPHPExcel->getActiveSheet()->setCellValue('I4', 'Creditos');
        $objPHPExcel->getActiveSheet()->setCellValue('J4', 'Inicio de ciclo');
        $objPHPExcel->getActiveSheet()->setCellValue('K4', 'Fin de ciclo');
        $objPHPExcel->getActiveSheet()->setCellValue('L4', 'Grado'); 
        $objPHPExcel->getActiveSheet()->setCellValue('M4', 'Total de alumnos');
        $objPHPExcel->getActiveSheet()->setCellValue('N4', 'Total de alumnos hombres');
        $objPHPExcel->getActiveSheet()->setCellValue('O4', 'Total de alumnos mujeres');
        $objPHPExcel->getActiveSheet()->setCellValue('P4', 'S/D');
        $objPHPExcel->getActiveSheet()->setCellValue('Q4', 'Alumnos menor a 21 años');
        $objPHPExcel->getActiveSheet()->setCellValue('R4', 'Alumnos de 22 años');
        $objPHPExcel->getActiveSheet()->setCellValue('S4', 'Alumnos de 23 años');
        $objPHPExcel->getActiveSheet()->setCellValue('T4', 'Alumnos de 24 años');
        $objPHPExcel->getActiveSheet()->setCellValue('U4', 'Alumnos de 25 años');
        $objPHPExcel->getActiveSheet()->setCellValue('V4', 'Alumnos entre 26 y 29 años');
        $objPHPExcel->getActiveSheet()->setCellValue('W4', 'Alumnos mayores a 30 años');
        $objPHPExcel->getActiveSheet()->setCellValue('X4', 'Hombres < 21 años');
        $objPHPExcel->getActiveSheet()->setCellValue('Y4', 'Hombres 22 años');
        $objPHPExcel->getActiveSheet()->setCellValue('Z4', 'Hombres 23 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AA4', 'Hombres 24 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AB4', 'Hombres 25 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AC4', 'Hombres 26 y 29 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AD4', 'Hombres >= 30 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AE4', 'Mujeres < 21 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AF4', 'Mujeres 22 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AG4', 'Mujeres 23 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AH4', 'Mujeres 24 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AI4', 'Mujeres 25 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AJ4', 'Mujeres 26 y 29 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AK4', 'Mujeres >= 30 años');
        $objPHPExcel->getActiveSheet()->setCellValue('AL4', 'S/D');
        $objPHPExcel->getActiveSheet()->setCellValue('AM4', 'Alumnos primer ingreso');
        $objPHPExcel->getActiveSheet()->setCellValue('AN4', 'Alumnos egresados');

        $fila = 5;

        $json=array();
        while($arreglo = arreglo($query)){
          

          
            $query_alumnos = query('SELECT ta.alumno_id, ta.situacion_alumno_id,curp, UPPER(substring(curp,11,1)) as sexo,
            YEAR(CURDATE()) - if (substring(curp,5,2) < 30,concat("20",substring(curp,5,2)), concat("19",substring(curp,5,2)))  as edad,
            ta.fecha_creacion
            from
           '.$GLOBALS["db_controlEscolar"].'.inter_alumno_plan iap
            join '.$GLOBALS["db_datosGenerales"].'.inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id
            join '.$GLOBALS["db_datosGenerales"].'.tr_plan_estudios tp on tp.plan_estudio_id = ipo.plan_estudio_id
            join '.$GLOBALS["db_controlEscolar"].'.tr_alumno ta on ta.alumno_id = iap.alumno_id
            join '.$GLOBALS["db_datosGenerales"].'.personas p on p.persona_id = ta.alumno_id
            where iap.estatus = 1 and ta.situacion_alumno_id in (1,4) and tp.carrera_id = '.$arreglo['carrera_id'].'
             and p.estatus =1 and ta.estatus = 1 AND ipo.orden_jerarquico_id ='.$arreglo['orden_jerarquico_id']);


            $total_alumnos = $alumnos_hombres = $alumnos_mujeres = $alumno_m_21 = $alumnos_22 = $alumnos_23 = 
            $alumnos_24 = $alumnos_25 = $alumnos_26_29 = $alumnos_M_30 = $alumnos_sin = $alumnos_egresados = $alumnos_primer_ingreso =
            $hombres_21 = $hombres_22 = $hombres_23 = $hombres_24 = $hombres_25 = $hombres_26_29 = $hombres_30 = 
            $mujeres_21 = $mujeres_22 = $mujeres_23 = $mujeres_24 = $mujeres_25 = $mujeres_26_29 = $mujeres_30 = 0;

            while($arreglo_alumnos = arreglo($query_alumnos)){

              $total_alumnos++;
              
              if($arreglo_alumnos['sexo'] == 'H') $alumnos_hombres++;
              else if($arreglo_alumnos['sexo'] == 'M') $alumnos_mujeres++;
              else {
                $alumnos_sin++;
              }

              if($arreglo_alumnos['edad'] > 1 &&  $arreglo_alumnos['edad'] <= 21) {
                $alumno_m_21++;
                if($arreglo_alumnos['sexo'] == 'H') $hombres_21++;
                else if($arreglo_alumnos['sexo'] == 'M') $mujeres_21++;
              }else if($arreglo_alumnos['edad'] ==  22) {
                $alumnos_22++;
                if($arreglo_alumnos['sexo'] == 'H') $hombres_22++;
                else if($arreglo_alumnos['sexo'] == 'M') $mujeres_22++;
              }else if($arreglo_alumnos['edad'] ==  23) {
                $alumnos_23++;
                if($arreglo_alumnos['sexo'] == 'H') $hombres_23++;
                else if($arreglo_alumnos['sexo'] == 'M') $mujeres_23++;
              }else if($arreglo_alumnos['edad'] ==  24) {
                $alumnos_24++;
                if($arreglo_alumnos['sexo'] == 'H') $hombres_24++;
                else if($arreglo_alumnos['sexo'] == 'M') $mujeres_24++;
              }else if($arreglo_alumnos['edad'] ==  25) {
                $alumnos_25++;
                if($arreglo_alumnos['sexo'] == 'H') $hombres_25++;
                else if($arreglo_alumnos['sexo'] == 'M') $mujeres_25++;
              }else if($arreglo_alumnos['edad'] >= 26 && $arreglo_alumnos['edad'] <= 30 )  {
                $alumnos_26_29++;
                if($arreglo_alumnos['sexo'] == 'H') $hombres_26_29++;
                else if($arreglo_alumnos['sexo'] == 'M') $mujeres_26_29++;
              }else if($arreglo_alumnos['edad'] >= 30 && $arreglo_alumnos['edad'] <= 100 )  {
                $alumnos_M_30++;
                if($arreglo_alumnos['sexo'] == 'H') $hombres_30++;
                else if($arreglo_alumnos['sexo'] == 'M') $mujeres_30++;
              }

            
              if($arreglo_alumnos['situacion_alumno_id'] == "4"){
                $alumnos_egresados++;
              }

              $inicio_ciclo = strtotime(date($arreglo['pre_inicio_ciclo']));  
              $fin_ciclo = strtotime(date($arreglo['fin_ciclo']));  
              $fecha_creacion = strtotime(date($arreglo_alumnos['fecha_creacion']));  

              if ($fecha_creacion > $inicio_ciclo && $fecha_creacion < $fin_ciclo){
                $alumnos_primer_ingreso++;
              }


              
            }
            

            $arreglo['total'] =$total_alumnos;
            $arreglo['alumnos_hombres'] =$alumnos_hombres;
            $arreglo['alumnos_mujeres'] =$alumnos_mujeres;
            $arreglo['alumnos_sin'] =$alumnos_sin;
            $arreglo['alumno_m_21'] =$alumno_m_21;
            $arreglo['alumnos_22'] =$alumnos_22;
            $arreglo['alumnos_23'] =$alumnos_23;
            $arreglo['alumnos_24'] =$alumnos_24;
            $arreglo['alumnos_25'] =$alumnos_25;
            $arreglo['alumnos_26_29'] =$alumnos_26_29;
            $arreglo['alumnos_M_30'] =$alumnos_M_30;
            $arreglo['hombres_m_21'] =$hombres_21;
            $arreglo['hombres_22'] =$hombres_22;
            $arreglo['hombres_23'] =$hombres_23;
            $arreglo['hombres_24'] =$hombres_24;
            $arreglo['hombres_25'] =$hombres_25;
            $arreglo['hombres_26_29'] =$hombres_26_29;
            $arreglo['hombres_M_30'] =$hombres_30;
            $arreglo['mujeres_m_21'] =$mujeres_21;
            $arreglo['mujeres_22'] =$mujeres_22;
            $arreglo['mujeres_23'] =$mujeres_23;
            $arreglo['mujeres_24'] =$mujeres_24;
            $arreglo['mujeres_25'] =$mujeres_25;
            $arreglo['mujeres_26_29'] =$mujeres_26_29;
            $arreglo['mujeres_M_30'] =$mujeres_30;
            $arreglo['alumnos_primer_ingreso'] =$alumnos_primer_ingreso;
            $arreglo['alumnos_egresados'] =$alumnos_egresados;
            

            if($total_alumnos != 0){  
                $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, $arreglo['carrera']);
                $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $arreglo['modalidad']);
                $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $arreglo['dependencia']);
                $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $arreglo['rvoe']);
                $objPHPExcel->getActiveSheet()->setCellValue('E' . $fila, $arreglo['fecha_expediencion_rvoe']);
                $objPHPExcel->getActiveSheet()->setCellValue('F' . $fila, $arreglo['nivel_estudios']);
                $objPHPExcel->getActiveSheet()->setCellValue('G' . $fila, $arreglo['total_periodos']);
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $fila, $arreglo['periodo']);
                $objPHPExcel->getActiveSheet()->setCellValue('I' . $fila, $arreglo['total_creditos']);
                $objPHPExcel->getActiveSheet()->setCellValue('J' . $fila, $arreglo['inicio_ciclo']);
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $fila, $arreglo['fin_ciclo']);
                $objPHPExcel->getActiveSheet()->setCellValue('L' . $fila, $arreglo['orden_jerarquico']);
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $fila, $arreglo['total']);
                $objPHPExcel->getActiveSheet()->setCellValue('N' . $fila, $arreglo['alumnos_hombres']);
                $objPHPExcel->getActiveSheet()->setCellValue('O' . $fila, $arreglo['alumnos_mujeres']);
                $objPHPExcel->getActiveSheet()->setCellValue('P' . $fila, $arreglo['alumnos_sin']);
                $objPHPExcel->getActiveSheet()->setCellValue('Q' . $fila, $arreglo['alumno_m_21']);
                $objPHPExcel->getActiveSheet()->setCellValue('R' . $fila, $arreglo['alumnos_22']);
                $objPHPExcel->getActiveSheet()->setCellValue('S' . $fila, $arreglo['alumnos_23']);
                $objPHPExcel->getActiveSheet()->setCellValue('T' . $fila, $arreglo['alumnos_24']);
                $objPHPExcel->getActiveSheet()->setCellValue('U' . $fila, $arreglo['alumnos_25']);
                $objPHPExcel->getActiveSheet()->setCellValue('V' . $fila, $arreglo['alumnos_26_29']);
                $objPHPExcel->getActiveSheet()->setCellValue('W' . $fila, $arreglo['alumnos_M_30']);
                $objPHPExcel->getActiveSheet()->setCellValue('X' . $fila, $arreglo['hombres_m_21']);
                $objPHPExcel->getActiveSheet()->setCellValue('Y' . $fila, $arreglo['hombres_22']);
                $objPHPExcel->getActiveSheet()->setCellValue('Z' . $fila, $arreglo['hombres_23']);
                $objPHPExcel->getActiveSheet()->setCellValue('AA' . $fila, $arreglo['hombres_24']);
                $objPHPExcel->getActiveSheet()->setCellValue('AB' . $fila, $arreglo['hombres_25']);
                $objPHPExcel->getActiveSheet()->setCellValue('AC' . $fila, $arreglo['hombres_26_29']);
                $objPHPExcel->getActiveSheet()->setCellValue('AD' . $fila, $arreglo['hombres_M_30']);
                $objPHPExcel->getActiveSheet()->setCellValue('AE' . $fila, $arreglo['mujeres_m_21']);
                $objPHPExcel->getActiveSheet()->setCellValue('AF' . $fila, $arreglo['mujeres_22']);
                $objPHPExcel->getActiveSheet()->setCellValue('AG' . $fila, $arreglo['mujeres_23']);
                $objPHPExcel->getActiveSheet()->setCellValue('AH' . $fila, $arreglo['mujeres_24']);
                $objPHPExcel->getActiveSheet()->setCellValue('AI' . $fila, $arreglo['mujeres_25']);
                $objPHPExcel->getActiveSheet()->setCellValue('AJ' . $fila, $arreglo['mujeres_26_29']);
                $objPHPExcel->getActiveSheet()->setCellValue('AK' . $fila, $arreglo['mujeres_M_30']);
                $objPHPExcel->getActiveSheet()->setCellValue('AL' . $fila, $arreglo['alumnos_sin']);
                $objPHPExcel->getActiveSheet()->setCellValue('AM' . $fila, $arreglo['alumnos_primer_ingreso']);
                $objPHPExcel->getActiveSheet()->setCellValue('AN' . $fila, $arreglo['alumnos_egresados']);
                
                $fila++;
            }
            

        }

      

        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:AN5" . $fila);
        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C2");

        foreach (range('A5', 'AN') as $columnID) {
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
