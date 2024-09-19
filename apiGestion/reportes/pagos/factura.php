<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('pagos');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }
      
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
    $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de Factura');
    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);

   $objPHPExcel->getActiveSheet()->getStyle('A5:L5')->applyFromArray($estiloTituloColumnas);
   $objPHPExcel->setActiveSheetIndex(0);

   $objPHPExcel->getActiveSheet()->setCellValue('A5','Carrera');
   $objPHPExcel->getActiveSheet()->setCellValue('B5','Plan_estudio');
   $objPHPExcel->getActiveSheet()->setCellValue('C5','Tipo_docto');
   $objPHPExcel->getActiveSheet()->setCellValue('D5','Folio_factura');
   $objPHPExcel->getActiveSheet()->setCellValue('E5','Estatus');
   $objPHPExcel->getActiveSheet()->setCellValue('F5','Fecha_creacion');
   $objPHPExcel->getActiveSheet()->setCellValue('G5','Nombre');
   $objPHPExcel->getActiveSheet()->setCellValue('H5','RFC');
   $objPHPExcel->getActiveSheet()->setCellValue('I5','Razon_social');
   $objPHPExcel->getActiveSheet()->setCellValue('J5','Fecha_actual');
   $objPHPExcel->getActiveSheet()->setCellValue('K5','UUID');
   $objPHPExcel->getActiveSheet()->setCellValue('L5','Monto_final');

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
  
  $query = query('SELECT tf.factura_id, tc.carrera, tpe.plan_estudio,"FAC" as tipo_docto, folio_factura, tf.estatus, tf.fecha_creacion, ica.respuesta as rfc,
  ica1.respuesta as razon_social, now() as fecha_actual, tf.uuid,monto_final,p.nombre, p.primer_apellido, p.segundo_apellido 
  FROM '.$GLOBALS['db_factura'].'.tr_factura tf
  left join '.$GLOBALS['db_seguimiento'].'.tr_aspirante ta on ta.aspirante_id = tf.alumno_id  
  left join '.$GLOBALS['db_seguimiento'].'.inter_campo_aspirante ica on ica.aspirante_id = ta.aspirante_id and ica.campo_formulario_id = 20  
  left join '.$GLOBALS['db_seguimiento'].'.inter_campo_aspirante ica1 on ica1.aspirante_id = ta.aspirante_id and ica1.campo_formulario_id = 22
  join '.$GLOBALS['db_controlEscolar'].'.inter_alumno_plan iap on iap.alumno_id = tf.alumno_id
  join '.$GLOBALS['db_datosGenerales'].'.inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id
  join '.$GLOBALS['db_datosGenerales'].'.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
  join '.$GLOBALS['db_datosGenerales'].'.tr_carrera tc on tc.carrera_id = tpe.carrera_id 
  join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = tf.alumno_id
  where tc.estatus = 1 and tpe.estatus = 1 and ipo.estatus = 1 and iap.estatus = 1 group by tf.factura_id '.$script.'
  UNION 	
  select factura_id,"sin carrera" as carrera,"sin plan" as plan_estudio, "FAC GEN" as tipo_docto,folio_factura , estatus , fecha_creacion, 
  "RFC en Factura" as rfc, "RZN" as razon_social, now() as fecha_actual, uuid , monto_final , "general" as nombre,"general" as primer_apellido,
  "general"  from '.$GLOBALS['db_factura'].'.tr_factura where alumno_id =0');

   $fila=6;
  while($arreglo = arreglo($query)){
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['carrera']);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['plan_estudio']);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['tipo_docto']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['folio_factura']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['estatus']);
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['fecha_creacion']);
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$arreglo['nombre'].' '.$arreglo['primer_apellido'].' '.$arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$arreglo['rfc']);
      $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$arreglo['razon_social']);
      $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$arreglo['fecha_actual']);
      $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$arreglo['uuid']);
      $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$arreglo['monto_final']);
      $fila++;
      }

      	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A6:L".$fila);

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
   header('Content-Disposition: attachment;filename="ReporteFacturas.xls"');
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
  

  