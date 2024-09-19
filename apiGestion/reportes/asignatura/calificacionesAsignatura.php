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
    //       $jwt  
    //   );
    $tipos_formacion = array(
      1 => 'Escolarizado',
      2 => 'Online',
      3 => 'Sabatino'
    );

    $query = query('select tm.materia_id,ta.clave_alumno,p.nombre,p.primer_apellido,p.segundo_apellido,p.email,
      ca.asignatura_clave,ca.asignatura_id,ca.asignatura,tm.calificacion
      from tr_materia tm
      join '.$GLOBALS['db_controlEscolar'].'.tr_alumno ta on ta.alumno_id = tm.alumno_id
      join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = ta.alumno_id
      join '.$GLOBALS['db_controlEscolar'].'.inter_asignatura_grupo iag on iag.asignatura_grupo_id = tm.asignatura_grupo_id
      join '.$GLOBALS['db_datosGenerales'].'.inter_orden_asignatura ioa on ioa.orden_asignatura_id = iag.orden_asignatura_id
      join '.$GLOBALS['db_datosGenerales'].'.cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id
      where tm.estatus = 1 and p.estatus = 1 and ta.estatus = 1 and iag.estatus=1 and ioa.estatus=1 and ca.estatus=1 and ca.asignatura_id ='.$id_asignatura);


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
    $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($estiloTituloColumnas);
    $objPHPExcel->getActiveSheet()->setTitle('Calificaciones');
    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Calificaciones');

    $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->setCellValue('A3', 'Nombre');
    $objPHPExcel->getActiveSheet()->setCellValue('B3', 'Primer_Apellido');
    $objPHPExcel->getActiveSheet()->setCellValue('C3', 'Segundo_Apellido');
    $objPHPExcel->getActiveSheet()->setCellValue('D3', 'Email');
    $objPHPExcel->getActiveSheet()->setCellValue('E3', 'Asignatura_Clave');
    $objPHPExcel->getActiveSheet()->setCellValue('F3', 'Asignatura_id');
    $objPHPExcel->getActiveSheet()->setCellValue('G3', 'Asignatura');
    $objPHPExcel->getActiveSheet()->setCellValue('H3', 'Calificacion Final');


    $fila = 4;
    while ($arreglo =  arreglo($query)) {
      $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15);
      $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo);

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, $arreglo['nombre']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $arreglo['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $arreglo['email']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $fila, $arreglo['asignatura_clave']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $fila, $arreglo['asignatura_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $fila, $arreglo['asignatura']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $fila, $arreglo['calificacion']);

      $query_actividades = query('select ta.actividad_id, tm.materia_id, ta.actividad_nombre, tmfa.calificacion as calificacion_actividad 
        from tr_materia_fecha_actividad tmfa 
        join tr_actividad ta on ta.actividad_id =tmfa.actividad_id
        join tr_materia tm on tm.materia_id = tmfa.materia_id
        where tmfa.estatus_actividad_id in (2,4) and tmfa.materia_id = ' . $arreglo['materia_id'] . ' and tmfa.estatus=1 order by actividad_id');
      $actividades = array();
          $column = 'H';
          $act=1;
      while ($arreglo_actividades = arreglo($query_actividades)) {
        $objPHPExcel->getActiveSheet()->getStyle($column.'3')->applyFromArray($estiloTituloColumnas);
        $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:".$column.$fila);
        $objPHPExcel->getActiveSheet()->setCellValue($column.'3', 'Actividad '.$act);

              $objPHPExcel->getActiveSheet()->setCellValue($column . $fila, $arreglo_actividades['calificacion_actividad']);
                  $column++;
                  $act++;

      }
      foreach (range('I', $column) as $columnID) {
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
      }
      $fila++;

    }


    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:I".$fila);

    foreach (range('A', 'H') as $columnID) {
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
    header('Content-Disposition: attachment;filename="Calificaciones_asignatura.xls"');
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
