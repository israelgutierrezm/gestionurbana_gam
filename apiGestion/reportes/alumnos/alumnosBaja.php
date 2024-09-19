<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('datosGenerales');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

  $tipos_formacion = array(
    1 => 'Escolarizado',
    2 => 'Online',
    3 => 'Sabatino'
  );

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

      $query=query('SELECT tt.descripcion_ticket,p.nombre,p.primer_apellido, p.segundo_apellido,p.email, tc.carrera, 
      coj.orden_jerarquico_descripcion,csa.situacion_alumno_descripcion
      from '.$GLOBALS['db_controlEscolar'].'.tr_alumno ta 
      join personas p on ta.alumno_id = p.persona_id
      join '.$GLOBALS['db_controlEscolar'].'.inter_alumno_plan iap on iap.alumno_id = ta.alumno_id
      join inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id
      join tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
      join tr_carrera tc on tc.carrera_id = tpe.carrera_id
      join cat_orden_jerarquico coj on coj.orden_jerarquico_id = ipo.orden_jerarquico_id
      join '.$GLOBALS['db_controlEscolar'].'.cat_situacion_alumno csa on csa.situacion_alumno_id = ta.situacion_alumno_id
      left join '.$GLOBALS['db_controlEscolar'].'.tr_ticket tt  on tt.persona_id = p.persona_id  and tt.tipo_ticket_id in (4,5)
      where ta.situacion_alumno_id in (2,3) and ta.estatus = 1 and p.estatus= 1 and iap.estatus= 1 and 
      ipo.estatus= 1 and tpe.estatus=1 and tc.estatus= 1 '.$script.'');
      
      
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
  
  $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de bajas de alumnos');
	
   	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2");

  $objPHPExcel->getActiveSheet()->getStyle('A4:I4')->applyFromArray($estiloTituloColumnas);
   $objPHPExcel->setActiveSheetIndex(0);
   $objPHPExcel->getActiveSheet()->setTitle('ReporteBajas');
   
   $objPHPExcel->getActiveSheet()->setCellValue('A4','Nombre');
   $objPHPExcel->getActiveSheet()->setCellValue('B4','Primer apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('C4','Segundo apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('D4','Email');
   $objPHPExcel->getActiveSheet()->setCellValue('E4','Carrera');
   $objPHPExcel->getActiveSheet()->setCellValue('F4','Grado');
   $objPHPExcel->getActiveSheet()->setCellValue('G4','Situacion Alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('H4','Observacion');

   $fila=5;
    while($arreglo =  arreglo($query)){
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['nombre']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['email']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['carrera']);
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['orden_jerarquico_descripcion']);
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$arreglo['situacion_alumno_descripcion']);
      $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$arreglo['descripcion_ticket']);
      $fila++;
      }

      	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:I".$fila);

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
   header('Content-Disposition: attachment;filename="reporteBajas.xls"');
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
  

  