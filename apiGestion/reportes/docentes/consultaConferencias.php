<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('learning');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
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
              $script = 'and icc.campus_id IN ('.$campus_administrador.')';
            }


      $arreglo_plan = arreglo(query('select plan_estudio_id, plan_estudio from '.$GLOBALS['db_datosGenerales'].'.tr_plan_estudios tpe
      join '.$GLOBALS['db_datosGenerales'].'.tr_carrera tc on tc.carrera_id = tpe.carrera_id 
      join '.$GLOBALS['db_datosGenerales'].'.inter_carrera_campus icc on icc.carrera_id = tc.carrera_id
      where plan_estudio_id = '.$id_plan_estudios.' and tpe.estatus = 1 '.$script.''));

      $query = query('SELECT p.nombre,p.primer_apellido,p.segundo_apellido,tv.nombre as nombre_conferencia,p.email,date(tv.fecha_inicio) as fecha_inicio,time(tv.fecha_inicio) as hora_inicio,date(tv.fecha_fin) as fecha_fin,time(tv.fecha_fin) as hora_fin , alumnos
      from tr_videoconferencia tv
      join '.$GLOBALS['db_controlEscolar'].'.inter_asignatura_grupo iag on iag.asignatura_grupo_id = tv.asignatura_grupo_id
      join '.$GLOBALS['db_datosGenerales'].'.inter_orden_asignatura ioa on ioa.orden_asignatura_id = iag.orden_asignatura_id
      join '.$GLOBALS['db_datosGenerales'].'.inter_plan_orden ipo on ioa.plan_orden_id = ipo.plan_orden_id
      join '.$GLOBALS['db_controlEscolar'].'.inter_docente_asignatura_grupo idag on idag.asignatura_grupo_id = iag.asignatura_grupo_id
      join '.$GLOBALS['db_controlEscolar'].'.tr_docente td on td.docente_id = idag.docente_id
      join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = td.docente_id
      left join (select videoconferencia_id, count(*) as alumnos from tr_acceso_videoconferencia group by videoconferencia_id) t1 on t1.videoconferencia_id = tv.videoconferencia_id
      where iag.estatus=1 and ioa.estatus=1 and ipo.estatus=1 and idag.estatus=1 
      and td.estatus=1 and p.estatus=1 and tv.estatus in (1,2) and ipo.plan_estudio_id = '.$id_plan_estudios.'');
      


  
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
	'size' =>13
    ),
    'fill' => array(
  'type'  => PHPExcel_Style_Fill::FILL_SOLID,
  'color' => array('rgb' =>'F9FD00')
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
	'size' =>11,
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
	'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
	);
	
	$estiloInformacion = new PHPExcel_Style();
	$estiloInformacion->applyFromArray( array(
    'font' => array(
	'name'  => 'Arial',
	'size' =>10,
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
	'horizontal'=> PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
    )
	));
	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
  $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2");

  $objPHPExcel->getActiveSheet()->getStyle('A4:J4')->applyFromArray($estiloTituloColumnas);

       
   $objPHPExcel->getActiveSheet()->setTitle('ReporteGeneral');
      
   
   $objPHPExcel->getActiveSheet()->setCellValue('A1','Plan de Estudio');
   $objPHPExcel->getActiveSheet()->setCellValue('A2',$arreglo_plan['plan_estudio']);
  

   $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
   $objPHPExcel->getActiveSheet()->setCellValue('A4','Nombre');
   $objPHPExcel->getActiveSheet()->setCellValue('B4','Primer_Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('C4','Segundo_Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('D4','Email');
   $objPHPExcel->getActiveSheet()->setCellValue('E4','Nombre de la clase');
   $objPHPExcel->getActiveSheet()->setCellValue('F4','Fecha_Inicio');
   $objPHPExcel->getActiveSheet()->setCellValue('G4','Hora_Inicio');
   $objPHPExcel->getActiveSheet()->setCellValue('H4','Hora_Fin');   
   $objPHPExcel->getActiveSheet()->setCellValue('I4','Alumnos');

   $fila=5;
    while($arreglo =  arreglo($query)){
      $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15); 
      $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo);

      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['nombre']); 
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['primer_apellido']); 
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['email']); 
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['nombre_conferencia']); 
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['fecha_inicio']); 
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$arreglo['hora_inicio']); 
      $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$arreglo['hora_fin']); 
      $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$arreglo['alumnos']); 

      $fila++;

      }	
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:J".$fila);
	
      foreach(range('A','J') as $columnID) { 
        $objPHPExcel->getActiveSheet()->getColumnDimension($columnID) ->setAutoSize(true); 
    } 


if($GLOBALS['version']==5){
  // include '../../extras/excel/crear5.php';  
  $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
}else{
  $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel7');  
  // include '../../extras/excel/crear7.php';  

}

   header('Content-Type: application/vnd.ms-excel');
   header('Content-Disposition: attachment;filename="ConsultaConferencias.xls"');
   header('Cache-Control: max-age=0');
   $writer->save('php://output');


    }else{
      $json = array("status" => 0, "msg" => "Método no aceptado");
    }
  
    /* Output header */
  
    // echo json_encode($json);
  
  } catch (Exception $e) {
      echo  $e->getMessage();
  }
  

  