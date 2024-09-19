<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';

try {
  
  db('controlEscolar');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

  $consulta_campus_administrador = arreglo(query('SELECT GROUP_CONCAT( ica.campus_id ) as campuses
  FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_administrador ta
  JOIN ' . $GLOBALS['db_controlEscolar'] . '.inter_campus_administrador ica ON ta.administrador_id = ica.administrador_id
  WHERE ta.estatus = 1 AND ica.estatus = 1 AND ta.administrador_id = ' . $usuario_id));
  if ($consulta_campus_administrador['campuses'] == '') {
    $script =' ';
    $script2 =' ';
  }else{
    $campus_administrador = $consulta_campus_administrador['campuses'];
    $script = 'and icc.campus_id IN ('.$campus_administrador.')';
    $script2 = 'and iap.campus_id IN ('.$campus_administrador.')';
  }

  $query = query('SELECT tc.carrera_id, tc.carrera_clave, tc.carrera from '.$GLOBALS['db_datosGenerales'].'.tr_carrera tc
  join ' . $GLOBALS["db_datosGenerales"] . '.inter_carrera_campus icc on icc.carrera_id = tc.carrera_id
  where tc.estatus = 1 '.$script.'');
      
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

  
  $estiloTituloCarrera = array(
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

  
  $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de Alumnos en Oferta Eductiva');
	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);

  //   $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2");
// 	$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($estiloTituloReporte);
// 	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C2");

//    $objPHPExcel->setActiveSheetIndex(0);
//    $objPHPExcel->getActiveSheet()->setTitle('ReporteAlumnos');

   $fila_carrera=3;
   $fila_valores = 4;
   $fila_alumno =5;

    while($arreglo =  arreglo($query)){

      $objPHPExcel->getActiveSheet()->getStyle('A'.$fila_carrera.':D'.$fila_carrera.'')->applyFromArray($estiloTituloColumnas);
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila_carrera,$arreglo['carrera']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila_carrera,$arreglo['carrera_clave']);   
      
      
      $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A".$fila_valores);
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila_valores,'Activo');
      $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "B".$fila_valores);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila_valores,'Baja temporal'); 
      $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C".$fila_valores);           
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila_valores,'Baja definitiva');
      $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "D".$fila_valores);            
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila_valores,'Egresado');            
          $fila_carrera += 4;
          $fila_valores += 4;

  

          $query_alumnos = query('SELECT csa.situacion_alumno_id ,csa.situacion_alumno_descripcion, if (t1.num_alumnos is not null,t1.num_alumnos,0) as num_alumnos from 
          (select count(ta.alumno_id) as num_alumnos, ta.situacion_alumno_id  from tr_alumno ta 
          join inter_alumno_plan iap on iap.alumno_id = ta.alumno_id
          join '.$GLOBALS['db_datosGenerales'].'.inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id
          join '.$GLOBALS['db_datosGenerales'].'.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
          where tpe.carrera_id = '.$arreglo['carrera_id'].' and ta.estatus = 1 '.$script2.' group by ta.situacion_alumno_id) t1
          right join cat_situacion_alumno csa on csa.situacion_alumno_id = t1.situacion_alumno_id
          where csa.estatus=1');
    

    while($arreglo_alumno = arreglo($query_alumnos)){

     if($arreglo_alumno['situacion_alumno_id'] == 1){
      $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A".$fila_alumno);            
        $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila_alumno,$arreglo_alumno['num_alumnos']);

    }elseif($arreglo_alumno['situacion_alumno_id'] == 2 ){
      $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "B".$fila_alumno);            
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila_alumno,$arreglo_alumno['num_alumnos']);

    }elseif($arreglo_alumno['situacion_alumno_id'] == 3 ){
      $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C".$fila_alumno);            
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila_alumno,$arreglo_alumno['num_alumnos']);

    }elseif($arreglo_alumno['situacion_alumno_id'] == 4){
      $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "D".$fila_alumno);            
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila_alumno,$arreglo_alumno['num_alumnos']);
    }

    }
    $fila_alumno += 4;


    }

      	// $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, 'A'.$fila_alumno.':D'.$fila_alumno.'');

      foreach(range('A','D') as $columnID) { 
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
   header('Content-Disposition: attachment;filename="AlumnosOferta.xls"');
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
  

  