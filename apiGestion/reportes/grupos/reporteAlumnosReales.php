<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
include '../../controlEscolar/class/ciclo.class.php';


try {
  
  db('controlEscolar');
  $ciclo = new Ciclo();

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

$id_ciclo = 1;
 $datos = $ciclo::consultaAlumnosReales($usuario_id);     
      
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
  // $objPHPExcel->getActiveSheet()->setCellValue('A3','Ciclo');
  $arreglo_ciclo = arreglo(query('select ciclo_desc from cat_ciclo where ciclo_id ='.$id_ciclo));   
  $objPHPExcel->getActiveSheet()->setCellValue('B3',$arreglo_ciclo['ciclo_desc']);
  
  $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
  // $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($estiloTituloReporte);  
  $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "B3");

  $objPHPExcel->getActiveSheet()->getStyle('A5:F5')->applyFromArray($estiloTituloColumnas);
   $objPHPExcel->setActiveSheetIndex(0);
   $objPHPExcel->getActiveSheet()->setTitle('ReporteAcceso');
   
   $objPHPExcel->getActiveSheet()->setCellValue('A5','plan_estudios');
   $objPHPExcel->getActiveSheet()->setCellValue('B5','grado');
   $objPHPExcel->getActiveSheet()->setCellValue('C5','nombre_grupo');
   $objPHPExcel->getActiveSheet()->setCellValue('D5','total_alumnos');
   $objPHPExcel->getActiveSheet()->setCellValue('E5','alumnos_reales');
   $objPHPExcel->getActiveSheet()->setCellValue('F5','alumnos_incompletos');

   $fila=6;
      foreach($datos as $datos_tmp){
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos_tmp['plan_estudios']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos_tmp['grado']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos_tmp['nombre_grupo']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos_tmp['total_alumnos']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos_tmp['alumnos_reales']);
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos_tmp['alumnos_incompletos']);

      $fila++;

      }

      	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A6:F".$fila);

      foreach(range('A','F') as $columnID) { 
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
  

  