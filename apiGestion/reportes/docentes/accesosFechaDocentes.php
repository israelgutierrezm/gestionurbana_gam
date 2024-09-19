<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('datosGenerales');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

    //   $usuario = Auth::GetData(
    //         $jwt  
    //     );
      
        $query_consulta_accesos = query('SELECT td.clave_profesor, p.nombre, p.primer_apellido, p.segundo_apellido, td.clave_profesor,
        DATE_FORMAT(tbs.fecha_inicio, "%d/%m/%Y") as fecha_inicio, COUNT(tbs.usuario_id) AS accesos
        FROM inter_persona_usuario_rol ipur 
        JOIN tr_bitacora_sesion tbs ON tbs.usuario_id = ipur.usuario_id
        JOIN personas p on p.persona_id = ipur.persona_id
        JOIN ceca_estudyce.tr_docente td on td.docente_id = p.persona_id
        WHERE ipur.rol_id = 3 AND tbs.fecha_inicio 
        BETWEEN "'.$fecha_inicio.'" AND "'.$fecha_fin.'" GROUP BY DAY(tbs.fecha_inicio)');

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

  $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($estiloTituloColumnas);

       
   $objPHPExcel->getActiveSheet()->setTitle('Accesos de docentes por fecha');
      
   $objPHPExcel->getActiveSheet()->setCellValue('A1','Fechas');
   $objPHPExcel->getActiveSheet()->setCellValue('A2',date('d/m/Y', strtotime($fecha_inicio)) .' - '. date('d/m/Y', strtotime($fecha_fin)));
  

   $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
   $objPHPExcel->getActiveSheet()->setCellValue('A4','Nombre');
   $objPHPExcel->getActiveSheet()->setCellValue('B4','Primer_Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('C4','Segundo_Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('D4','Clave');
   $objPHPExcel->getActiveSheet()->setCellValue('E4','Fecha de acceso');
   $objPHPExcel->getActiveSheet()->setCellValue('F4','Número de accesos');

   $fila=5;
    while($arreglo =  arreglo($query_consulta_accesos)){
      $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15); 
      $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo);
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['nombre']); 
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['primer_apellido']); 
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['clave_profesor']); 
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['fecha_inicio']); 
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['accesos']); 
      $fila++;
      }	
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:F".$fila);
	
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
   header('Content-Disposition: attachment;filename="accesos_docentes.xls"');
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
  

  