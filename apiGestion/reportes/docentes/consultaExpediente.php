<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('controlEscolar');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

      /*$usuario = Auth::GetData(
            $jwt  
        );*/
      


      $query=query('SELECT p.nombre AS nombre_alumno, p.primer_apellido , p.segundo_apellido,ced.nombre_documento,cedd.nombre_documento as estado_documento, ie.url
      from inter_expediente_docente ie 
      join cat_documento_docente ced on ced.documento_id = ie.documento_id
      join cat_estado_documento_docente cedd on cedd.estado_documento_id = ie.estado_documento_id
      join '.$GLOBALS['db_datosGenerales'].'.personas p on ie.docente_id = p.persona_id
      where ie.estatus= 1 and ced.estatus = 1 and cedd.estatus=1 and p.estatus=1 order by nombre, primer_apellido, segundo_apellido');
    

  
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
  // $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2");
	// $objPHPExcel->getActiveSheet()->getStyle('C1')->applyFromArray($estiloTituloReporte);
	// $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C2");

  $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($estiloTituloColumnas);

       
   $objPHPExcel->getActiveSheet()->setTitle('ExpedientesDocentes');
      
   
   $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de Expedientes Docentes.');
  //  $objPHPExcel->getActiveSheet()->setCellValue('A2',$arreglo_plan['plan_estudio']);
  //  $objPHPExcel->getActiveSheet()->setCellValue('C1','Carrera');
  //  $objPHPExcel->getActiveSheet()->setCellValue('C2',$arreglo_plan['carrera']);
  

   $objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setWrapText(true);
   $objPHPExcel->getActiveSheet()->setCellValue('A3','Nombre_docente');
   $objPHPExcel->getActiveSheet()->setCellValue('B3','Primer_apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('C3','Segundo_Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('D3','Tipo_documento');
   $objPHPExcel->getActiveSheet()->setCellValue('E3','Estado_documento');
   $objPHPExcel->getActiveSheet()->setCellValue('F3','Url_documento');

   $fila=4;
    while($arreglo =  arreglo($query)){
      $arreglo['url']=$GLOBALS['url_front'].substr($arreglo['url'], 1);
      $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15); 
      $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo);
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['nombre_alumno']); 
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['segundo_apellido']); 
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['nombre_documento']); 
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['estado_documento']); 
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['url']); 

      $fila++;

      }	
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:F".$fila);
	
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
   header('Content-Disposition: attachment;filename="ExpedientesDocentes.xls"');
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
  

  