<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('controlEscolar');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

  $grupo_id = $_GET['grupo_id'];
      
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
  
  $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de Alumnos en Grupo');
  $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
  $arreglo_grupo =arreglo(query('select nombre_grupo from tr_grupo where estatus =1 and grupo_id ='.$grupo_id));
  $objPHPExcel->getActiveSheet()->setCellValue('A3','Nombre grupo:');  
  $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A3");
  $objPHPExcel->getActiveSheet()->setCellValue('B3',$arreglo_grupo['nombre_grupo']);
  $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "B3");


   $objPHPExcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($estiloTituloColumnas);
   $objPHPExcel->setActiveSheetIndex(0);
   $objPHPExcel->getActiveSheet()->setTitle('ReporteAcceso');
   
   $objPHPExcel->getActiveSheet()->setCellValue('A5','Nombre Alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('B5','Primer Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('C5','Segundo Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('D5','Clave Alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('E5','Curp');
   $objPHPExcel->getActiveSheet()->setCellValue('F5','Situación Alumno');

  
  $query = query('SELECT p.nombre, p.primer_apellido, p.segundo_apellido, ta.alumno_id, iag.grupo_id , ta.clave_alumno, p.curp
  , csa.situacion_alumno_descripcion
  from tr_alumno ta
  join '.$GLOBALS["db_datosGenerales"].'.personas p on p.persona_id=ta.alumno_id
  join inter_alumno_grupo iag on iag.alumno_id = ta.alumno_id
  join tr_grupo tg on tg.grupo_id = iag.grupo_id
  join cat_situacion_alumno csa on csa.situacion_alumno_id = ta.situacion_alumno_id 
  where tg.grupo_id ='.$grupo_id.' and ta.estatus = 1 and iag.estatus=1 and p.estatus = 1
  order by p.primer_apellido asc');


   $fila=6;
  while($arreglo = arreglo($query)){
   $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['nombre']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['clave_alumno']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['curp']);
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['situacion_alumno_descripcion']);

      $fila++;

      }

      	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A6:F".$fila);

      foreach(range('A','E') as $columnID) { 
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
   header('Content-Disposition: attachment;filename="reporteAlumnosReales.xls"');
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
  

  