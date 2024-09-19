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


    $consulta_campus_administrador = arreglo(query('SELECT GROUP_CONCAT( ica.campus_id ) as campuses
            FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_administrador ta
            JOIN ' . $GLOBALS['db_controlEscolar'] . '.inter_campus_administrador ica ON ta.administrador_id = ica.administrador_id
            WHERE ta.estatus = 1 AND ica.estatus = 1 AND ta.administrador_id = ' . $usuario_id));
            if ($consulta_campus_administrador['campuses'] == '') {
              $script =' ';
            }else{
              $campus_administrador = $consulta_campus_administrador['campuses'];
              $script = 'where iap.campus_id IN ('.$campus_administrador.')';
            }

    $arreglo_asignatura = arreglo(query('select asignatura from ' . $GLOBALS['db_datosGenerales'] . '.cat_asignaturas where asignatura_id = ' . $id_asignatura . ' and estatus = 1'));

    $query = query('SELECT p.nombre,p.primer_apellido,p.segundo_apellido,p.email,tal.clave_alumno,tm.materia_id,tg.nombre_grupo, tg.grupo, realizados,totales, (realizados/totales)*100 as avance 
      from (select * from (select @asignatura_id :=' . $id_asignatura . ' i)as vista, ' . $GLOBALS['db_reporte'] . '.asignaturas_totales) as t1
      join (select * from (select @asignatura_id :=' . $id_asignatura . ' i)as vista, ' . $GLOBALS['db_reporte'] . '.materia_asignatura_realizada) as t2 on t1.asignatura_id = t2.asignatura_id
      join ' . $GLOBALS['db_learning'] . '.tr_materia tm on tm.materia_id = t2.materia_id
      join ' . $GLOBALS['db_controlEscolar'] . '.tr_alumno tal on tal.alumno_id = tm.alumno_id
      join ' . $GLOBALS['db_datosGenerales'] . '.personas p on p.persona_id = tm.alumno_id
      join ' . $GLOBALS['db_controlEscolar'] . '.inter_asignatura_grupo iag on iag.asignatura_grupo_id = tm.asignatura_grupo_id 
      join ' . $GLOBALS['db_controlEscolar'] . '.tr_grupo tg on tg.grupo_id = iag.grupo_id
      join ' . $GLOBALS['db_controlEscolar'] . '.inter_alumno_plan iap ON iap.alumno_id = ta.alumno_id '.$script.'');




    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getProperties()


      ->setCreator("Temporaris")
      ->setLastModifiedBy("Temporaris")
      ->setTitle("Template Relevé des heures intér  ires")
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
    // $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($estiloTituloReporte);
    // $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C2");

    $objPHPExcel->getActiveSheet()->getStyle('A4:K4')->applyFromArray($estiloTituloColumnas);


    $objPHPExcel->getActiveSheet()->setTitle('ReporteGeneral');


    $objPHPExcel->getActiveSheet()->setCellValue('A1', 'Materia');
    $objPHPExcel->getActiveSheet()->setCellValue('A2', $arreglo_asignatura['asignatura']);
    //  $objPHPExcel->getActiveSheet()->setCellValue('C1','Carrera');
    //  $objPHPExcel->getActiveSheet()->setCellValue('C2',$arreglo_plan['carrera']);


    $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
    $objPHPExcel->getActiveSheet()->setCellValue('A4', 'Nombre_Alumno');
    $objPHPExcel->getActiveSheet()->setCellValue('B4', 'Primer_Apellido');
    $objPHPExcel->getActiveSheet()->setCellValue('C4', 'Segundo_Apellido');
    $objPHPExcel->getActiveSheet()->setCellValue('D4', 'Email');
    $objPHPExcel->getActiveSheet()->setCellValue('E4', 'Clave_Alumno');
    $objPHPExcel->getActiveSheet()->setCellValue('F4', 'Materia_id');
    $objPHPExcel->getActiveSheet()->setCellValue('G4', 'Grupo');
    $objPHPExcel->getActiveSheet()->setCellValue('H4', 'Clave grupo');
    $objPHPExcel->getActiveSheet()->setCellValue('I4', 'Realizados');
    $objPHPExcel->getActiveSheet()->setCellValue('J4', 'Totales');
    $objPHPExcel->getActiveSheet()->setCellValue('K4', 'Avance');


    $fila = 5;
    while ($arreglo =  arreglo($query)) {
      $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15);
      $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo);

      $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, $arreglo['nombre']);
      $objPHPExcel->getActiveSheet()->setCellValue('B' . $fila, $arreglo['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('C' . $fila, $arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $arreglo['email']);
      $objPHPExcel->getActiveSheet()->setCellValue('E' . $fila, $arreglo['clave_alumno']);
      $objPHPExcel->getActiveSheet()->setCellValue('F' . $fila, $arreglo['materia_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('G' . $fila, $arreglo['nombre_grupo']);
      $objPHPExcel->getActiveSheet()->setCellValue('H' . $fila, $arreglo['grupo']);
      $objPHPExcel->getActiveSheet()->setCellValue('I' . $fila, $arreglo['realizados']);
      $objPHPExcel->getActiveSheet()->setCellValue('J' . $fila, $arreglo['totales']);
      $objPHPExcel->getActiveSheet()->setCellValue('K' . $fila, $arreglo['avance']);
      $fila++;
    }
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:K" . $fila);

    foreach (range('A', 'K') as $columnID) {
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
    header('Content-Disposition: attachment;filename="Avance(' . $arreglo_asignatura['asignatura'] . ').xls"');
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
