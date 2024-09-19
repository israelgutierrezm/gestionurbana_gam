<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {

  db('learning');

  if ($_SERVER['REQUEST_METHOD'] == "GET") {

    foreach ($_GET as $clave => $valor) {
      ${$clave} = escape_cara($valor);
    }

    // $usuario = Auth::GetData(
    //         $jwt  
    //     );

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


    $arreglo_plan = arreglo(query('SELECT tpe.plan_estudio_id,plan_estudio, tpe.carrera_id, tc.carrera
      from ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe
      join ' . $GLOBALS["db_datosGenerales"] . '.tr_carrera tc on tc.carrera_id = tpe.carrera_id
      join ' . $GLOBALS["db_datosGenerales"] . '.inter_carrera_campus icc on icc.carrera_id = tc.carrera_id
      where plan_estudio_id =' . $id_plan_estudios . ' and tpe.estatus = 1 and tc.estatus = 1 '.$script.''));



    $query = query('SELECT nombre_grupo,grupo,clave_profesor, docente_nombre, docente_pa, docente_sp,
      ioa.asignatura_id,ca.asignatura,ca.asignatura_clave,clave_alumno,tav.nombre, primer_apellido,
      segundo_apellido,tav.fecha_creacion,tv.nombre as nombre_conferencia,tav.videoconferencia_id, tv.fecha_inicio, tv.fecha_fin
      from (
      select videoconferencia_id,tav.persona_id,ta.clave_alumno,p.nombre,p.primer_apellido, p.segundo_apellido, tav.fecha_creacion, tav.estatus 
      from tr_acceso_videoconferencia tav
      join ' . $GLOBALS["db_datosGenerales"] . '.inter_persona_usuario_rol ipur on ipur.persona_id = tav.persona_id
      join ' . $GLOBALS["db_controlEscolar"] . '.tr_alumno ta on ta.alumno_id = ipur.persona_id
      join ' . $GLOBALS["db_datosGenerales"] . '.personas p on ta.alumno_id = p.persona_id
      where rol_id = 2 and tav.estatus= 1 and ipur.estatus = 1) as tav
      join tr_videoconferencia tv on tv.videoconferencia_id = tav.videoconferencia_id
      join ' . $GLOBALS["db_controlEscolar"] . '.inter_asignatura_grupo iag on iag.asignatura_grupo_id = tv.asignatura_grupo_id
      join (
      select asignatura_grupo_id,idag.docente_id,clave_profesor, nombre as docente_nombre, primer_apellido as docente_pa, segundo_apellido as docente_sp
      from ' . $GLOBALS["db_controlEscolar"] . '.inter_docente_asignatura_grupo idag 
      join ' . $GLOBALS["db_controlEscolar"] . '.tr_docente td on td.docente_id = idag.docente_id
      join ' . $GLOBALS["db_datosGenerales"] . '.personas p on td.docente_id = p.persona_id 
      where p.estatus = 1 and idag.estatus = 1)idag on idag.asignatura_grupo_id = iag.asignatura_grupo_id
      join ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa on ioa.orden_asignatura_id = iag.orden_asignatura_id
      join ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id
      join ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg on tg.grupo_id = iag.grupo_id
      join ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo on ipo.plan_orden_id = tg.plan_orden_id
      where ipo.plan_estudio_id = ' . $arreglo_plan['plan_estudio_id'] . ' and ioa.estatus = 1 and ca.estatus=1 and tg.estatus=1 and ipo.estatus=1 and
      (tv.fecha_inicio >= "' . $fecha_inicio . '" and tv.fecha_fin <= "' . $fecha_fin . '")
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
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2");
    $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C2");

    $objPHPExcel->getActiveSheet()->getStyle('A4:P4')->applyFromArray($estiloTituloColumnas);


    $objPHPExcel->getActiveSheet()->setTitle('accesosVideoconferenciasAlu');


    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Plan de Estudios');
    $objPHPExcel->getActiveSheet()->setCellValue('A2', $arreglo_plan['plan_estudio']);
    $objPHPExcel->getActiveSheet()->setCellValue('C1', 'Carrera');
    $objPHPExcel->getActiveSheet()->setCellValue('C2', $arreglo_plan['carrera']);


    $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Nombre_Grupo');
    $objPHPExcel->getActiveSheet()->setCellValue('B4', 'Clave_grupo');
    $objPHPExcel->getActiveSheet()->setCellValue('C4', 'Matricula_docente');
    $objPHPExcel->getActiveSheet()->setCellValue('D4', 'Nombre_docente');
    $objPHPExcel->getActiveSheet()->setCellValue('E4', 'Paterno_docente');
    $objPHPExcel->getActiveSheet()->setCellValue('F4', 'Materno_docente');
    $objPHPExcel->getActiveSheet()->setCellValue('G4', 'Asignatura_clave');
    $objPHPExcel->getActiveSheet()->setCellValue('H4', 'Nombre_asignatura');
    $objPHPExcel->getActiveSheet()->setCellValue('I4', 'Matricula_alumno');
    $objPHPExcel->getActiveSheet()->setCellValue('J4', 'Nombre_alumno');
    $objPHPExcel->getActiveSheet()->setCellValue('K4', 'Paterno_alumno');
    $objPHPExcel->getActiveSheet()->setCellValue('L4', 'Materno_alumno');
    $objPHPExcel->getActiveSheet()->setCellValue('M4', 'Fecha_asistencia');
    $objPHPExcel->getActiveSheet()->setCellValue('N4', 'Videoconferencia');
    $objPHPExcel->getActiveSheet()->setCellValue('O4', 'Fecha_inicio');
    $objPHPExcel->getActiveSheet()->setCellValue('P4', 'Fecha_fin');

    $fila = 5;
    while ($arreglo =  arreglo($query)) {
      $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15);
      $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo);

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, $arreglo['nombre_grupo']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $arreglo['grupo']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $arreglo['clave_profesor']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $arreglo['docente_nombre']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $fila, $arreglo['docente_pa']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $fila, $arreglo['docente_sp']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $fila, $arreglo['asignatura_clave']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $fila, $arreglo['asignatura']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $fila, $arreglo['clave_alumno']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $fila, $arreglo['nombre']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $fila, $arreglo['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('L' . $fila, $arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('M' . $fila, $arreglo['fecha_creacion']);
      $objPHPExcel->getActiveSheet()->setCellValue('N' . $fila, $arreglo['nombre_conferencia']);
      $objPHPExcel->getActiveSheet()->setCellValue('O' . $fila, $arreglo['fecha_inicio']);
      $objPHPExcel->getActiveSheet()->setCellValue('P' . $fila, $arreglo['fecha_fin']);

      $fila++;
    }
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:P" . ($fila - 1));

    foreach (range('A', 'P') as $columnID) {
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
    header('Content-Disposition: attachment;filename="AccesosVideoconferenciasAlu.xls"');
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
