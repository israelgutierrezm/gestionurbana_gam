<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('pagos');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

 $datos = query('select p.nombre,p.primer_apellido,p.segundo_apellido,tsp.monto,tsp.fecha_pago,p.persona_id ,cs.suscripcion_id from tr_solicitud_pago tsp
 join cat_porcentaje_fecha_pago cpfp on cpfp.porcentaje_pago_id = tsp.porcentaje_pago_id
 join cat_suscripcion cs on cs.suscripcion_id = cpfp.suscripcion_id
 join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = tsp.persona_id
 where tsp.estatus=1 and cpfp.estatus=1 and cs.estatus=1 ');     
      
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
  
  $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de Suscriptores');
//   $objPHPExcel->getActiveSheet()->setCellValue('A3','Ciclo');
//   $arreglo_ciclo = arreglo(query('select ciclo_desc from cat_ciclo where ciclo_id ='.$id_ciclo));   
//   $objPHPExcel->getActiveSheet()->setCellValue('B3',$arreglo_ciclo['ciclo_desc']);
  
  $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
//   $objPHPExcel->getActiveSheet()->getStyle('A3')->applyFromArray($estiloTituloReporte);  
//   $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "B3");

  $objPHPExcel->getActiveSheet()->getStyle('A3:G3')->applyFromArray($estiloTituloColumnas);
   $objPHPExcel->setActiveSheetIndex(0);
   $objPHPExcel->getActiveSheet()->setTitle('ReporteSuscriptores');
   
   $objPHPExcel->getActiveSheet()->setCellValue('A3','nombre');
   $objPHPExcel->getActiveSheet()->setCellValue('B3','primer_apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('C3','segundo_apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('D3','monto');
   $objPHPExcel->getActiveSheet()->setCellValue('E3','fecha de pago');
   $objPHPExcel->getActiveSheet()->setCellValue('F3','Id Cliente');
   $objPHPExcel->getActiveSheet()->setCellValue('G3','Id Suscripción');
   $fila=4;
      while($datos_tmp = arreglo($datos)){
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$datos_tmp['nombre']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$datos_tmp['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$datos_tmp['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$datos_tmp['monto']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$datos_tmp['fecha_pago']);
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$datos_tmp['persona_id']);
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$datos_tmp['suscripcion_id']);

      $fila++;
      }

      	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:G".$fila);

      foreach(range('A','G') as $columnID) { 
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
  

  