<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('datosGenerales');

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
            }else{
              $campus_administrador = $consulta_campus_administrador['campuses'];
              $script = 'and iap.campus_id IN ('.$campus_administrador.')';
            }

      $query=query('SELECT tc.carrera,  p.nombre, p.primer_apellido, p.segundo_apellido,tal.clave_alumno,ipo.orden_jerarquico_id as grado, ca.asignatura, th.calificacion from '.$GLOBALS["db_controlEscolar"].'.tr_historial th 
      join personas p on p.persona_id = th.alumno_id
      join '.$GLOBALS["db_controlEscolar"].'.tr_alumno tal on tal.alumno_id = th.alumno_id
      join inter_orden_asignatura ioa on ioa.orden_asignatura_id = th.orden_asignatura_id
      join cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id
      join inter_plan_orden ipo on ipo.plan_orden_id = ioa.plan_orden_id
      join tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
      join tr_carrera tc on tc.carrera_id = tpe.carrera_id
      join '.$GLOBALS["db_controlEscolar"].'.inter_alumno_plan iap on iap.alumno_id = tal.alumno_id
      where p.estatus=1 and th.estatus=1 and ioa.estatus= 1 and ca.estatus=1 and ipo.estatus= 1 
      and calificacion <= 6 '.$script.' ORDER by p.nombre');
      
      
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
  
  $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de alumnos reprobados');
	
   	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2");

  $objPHPExcel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($estiloTituloColumnas);
   $objPHPExcel->setActiveSheetIndex(0);
   $objPHPExcel->getActiveSheet()->setTitle('ReporteReprobados');
   
   $objPHPExcel->getActiveSheet()->setCellValue('A4','Nombre');
   $objPHPExcel->getActiveSheet()->setCellValue('B4','Primer apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('C4','Segundo apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('D4','Matrícula');
   $objPHPExcel->getActiveSheet()->setCellValue('E4','Carrera');
   $objPHPExcel->getActiveSheet()->setCellValue('F4','Grado');
   $objPHPExcel->getActiveSheet()->setCellValue('G4','Asignatura');
   $objPHPExcel->getActiveSheet()->setCellValue('H4','Calificación');

   $fila=5;
    while($arreglo =  arreglo($query)){
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['nombre']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['clave_alumno']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['carrera']);
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['grado']);
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$arreglo['asignatura']);
      $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$arreglo['calificacion']);
      $fila++;

      }

 
      foreach(range('A','H') as $columnID) { 
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
   header('Content-Disposition: attachment;filename="AlumnosReprobados.xls"');
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
  

  