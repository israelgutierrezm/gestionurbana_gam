<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('reporte');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

  $consulta_campus_administrador = arreglo(query('SELECT GROUP_CONCAT( ica.campus_id ) as campuses
            FROM '.$GLOBALS['db_controlEscolar'].'.tr_administrador ta
            JOIN '.$GLOBALS['db_controlEscolar'].'.inter_campus_administrador ica ON ta.administrador_id = ica.administrador_id
            WHERE ta.estatus = 1 AND ica.estatus = 1 AND ta.administrador_id = ' . $usuario_id));
            if ($consulta_campus_administrador['campuses'] != '') {
              $script =' ';
              $script2 =' ';
            }else{
              $campus_administrador = $consulta_campus_administrador['campuses'];
              $script = 'and icc.campus_id IN ('.$campus_administrador.')';
              $script2 = 'and iap.campus_id IN ('.$campus_administrador.')';
            }

  $arreglo_plan = arreglo(query('SELECT tpe.plan_estudio_id,plan_estudio, tpe.carrera_id, tc.carrera
  from '.$GLOBALS["db_datosGenerales"].'.tr_plan_estudios tpe
  join '.$GLOBALS["db_datosGenerales"].'.tr_carrera tc on tc.carrera_id = tpe.carrera_id
  join '.$GLOBALS["db_datosGenerales"].'.inter_carrera_campus icc on icc.carrera_id = tc.carrera_id
  where plan_estudio_id ='.$id_plan_estudios.' and tpe.estatus = 1 and tc.estatus = 1 '.$script.''));



      $query=query('select * from accesos_alumno aa
      join '.$GLOBALS['db_controlEscolar'].'.inter_alumno_plan iap on iap.alumno_id = aa.alumno_id
      join '.$GLOBALS['db_datosGenerales'].'.inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id
      join '.$GLOBALS['db_controlEscolar'].'.inter_alumno_grupo iag on iag.alumno_id = aa.alumno_id 
      join '.$GLOBALS['db_controlEscolar'].'.tr_grupo tg on tg.grupo_id = iag.grupo_id and ipo.plan_orden_id  = tg.plan_orden_id
      where ipo.plan_estudio_id= '.$id_plan_estudios.' and iap.estatus = 1 and ipo.estatus=1 and iag.estatus=1 and tg.estatus=1 '.$script2.'');
      
      
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
  
  $objPHPExcel->getActiveSheet()->setCellValue('A1','Plan de Estudios');
  $objPHPExcel->getActiveSheet()->setCellValue('A2',$arreglo_plan['plan_estudio']);
  $objPHPExcel->getActiveSheet()->setCellValue('C1','Carrera');
  $objPHPExcel->getActiveSheet()->setCellValue('C2',$arreglo_plan['carrera']);
	
   	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
  $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2");
	$objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($estiloTituloReporte);
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C2");

  $objPHPExcel->getActiveSheet()->getStyle('A4:L4')->applyFromArray($estiloTituloColumnas);
   $objPHPExcel->setActiveSheetIndex(0);
   $objPHPExcel->getActiveSheet()->setTitle('ReporteAcceso');
   
   $objPHPExcel->getActiveSheet()->setCellValue('A4','Matrícula');
   $objPHPExcel->getActiveSheet()->setCellValue('B4','Nombre');
   $objPHPExcel->getActiveSheet()->setCellValue('C4','Primer apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('D4','Segundo apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('E4','Curp');
   $objPHPExcel->getActiveSheet()->setCellValue('F4','Celular');
   $objPHPExcel->getActiveSheet()->setCellValue('G4','Email');
   $objPHPExcel->getActiveSheet()->setCellValue('H4','Sexo');
   $objPHPExcel->getActiveSheet()->setCellValue('I4','Clave grupo');
   $objPHPExcel->getActiveSheet()->setCellValue('J4','Grupo');
   $objPHPExcel->getActiveSheet()->setCellValue('K4','Fecha de nacimiento');
   $objPHPExcel->getActiveSheet()->setCellValue('L4','Último acceso');

   $fila=5;
    while($arreglo =  arreglo($query)){
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['matricula']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['nombre']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['curp']);
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['celular']);
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$arreglo['email']);
      $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$arreglo['sexo']);
      $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$arreglo['grupo']);
      $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$arreglo['nombre_grupo']);
      if($arreglo['fecha_nacimiento'] == '0000-00-00') $arreglo['fecha_nacimiento']= null;
      $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$arreglo['fecha_nacimiento']);
      $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$arreglo['ultimo_acceso']);

      $fila++;

      }

      	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:L".$fila);

      foreach(range('A','L') as $columnID) { 
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
   header('Content-Disposition: attachment;filename="reporteAcceso.xls"');
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
  

  