<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {

  
  db('learning');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

      // $usuario = Auth::GetData(
      //       $jwt  
      //   );
      

      $consulta_campus_administrador = arreglo(query('SELECT GROUP_CONCAT( ica.campus_id ) as campuses
            FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_administrador ta
            JOIN ' . $GLOBALS['db_controlEscolar'] . '.inter_campus_administrador ica ON ta.administrador_id = ica.administrador_id
            WHERE ta.estatus = 1 AND ica.estatus = 1 AND ta.administrador_id = ' . $usuario_id));
            if ($consulta_campus_administrador['campuses'] == '') {
              $script =' ';
            }else{
              $campus_administrador = $consulta_campus_administrador['campuses'];
              $script = 'and icc.campus_id IN ('.$campus_administrador.')';
            }

            
      $query = query('select p.nombre,p.primer_apellido,p.segundo_apellido,p.email,ca.asignatura_clave,ca.asignatura,total_pendientes, fecha_antigua
      from '.$GLOBALS['db_controlEscolar'].'.tr_docente td
      join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = td.docente_id
      join '.$GLOBALS['db_controlEscolar'].'.inter_docente_asignatura_grupo idag on idag.docente_id = td.docente_id
      join '.$GLOBALS['db_controlEscolar'].'.inter_asignatura_grupo iag on iag.asignatura_grupo_id = idag.asignatura_grupo_id
      join '.$GLOBALS['db_datosGenerales'].'.inter_orden_asignatura ioa on ioa.orden_asignatura_id = iag.orden_asignatura_id
      join '.$GLOBALS['db_datosGenerales'].'.inter_plan_orden ipo on ipo.plan_orden_id = ioa.plan_orden_id
      join '.$GLOBALS['db_datosGenerales'].'.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id 
      join '.$GLOBALS['db_datosGenerales'].'.cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id
      join '.$GLOBALS['db_datosGenerales'].'.tr_carrera tc on tc.carrera_id = tpe.carrera_id 
      join '.$GLOBALS['db_datosGenerales'].'.inter_carrera_campus icc on icc.carrera_id = tc.carrera_id '.$script.'
      join (select tm.asignatura_grupo_id,count(tmfa.materia_fecha_actividad_id) as total_pendientes,min(tmfa.fecha_actividad) as fecha_antigua from '.$GLOBALS['db_learning'].'.tr_materia_fecha_actividad tmfa
      join tr_materia tm ON tm.materia_id = tmfa.materia_id
      where estatus_actividad_id = 3 and tmfa.estatus = 1 group by tm.asignatura_grupo_id ) as t1 on t1.asignatura_grupo_id = idag.asignatura_grupo_id 
      where td.estatus=1 and p.estatus=1 and idag.estatus=1 and iag.estatus=1 and ioa.estatus=1 and ca.estatus=1 and iag.situacion_asignatura_grupo_id not in (4,5) and tpe.plan_estudio_id='.$id_plan_estudios);
      


  
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

  $objPHPExcel->getActiveSheet()->getStyle('A3:H3')->applyFromArray($estiloTituloColumnas);

       
   $objPHPExcel->getActiveSheet()->setTitle('ReporteGeneral');
      
   
   $objPHPExcel->getActiveSheet()->setCellValue('A1','Pendientes Docente');
  

   $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
   $objPHPExcel->getActiveSheet()->setCellValue('A3','Nombre');
   $objPHPExcel->getActiveSheet()->setCellValue('B3','Primer_Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('C3','Segundo_Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('D3','Email');
   $objPHPExcel->getActiveSheet()->setCellValue('E3','Asignatura_Clave');
   $objPHPExcel->getActiveSheet()->setCellValue('F3','Asignatura');
   $objPHPExcel->getActiveSheet()->setCellValue('G3','Total_Pendientes');
   $objPHPExcel->getActiveSheet()->setCellValue('H3','Fecha_Antigua');


   $fila=4;
    while($arreglo =  arreglo($query)){
      $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15); 
      $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo);

      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['nombre']); 
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['primer_apellido']); 
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['email']); 
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['asignatura_clave']); 
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['asignatura']); 
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$arreglo['total_pendientes']); 
      $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$arreglo['fecha_antigua']); 


      $fila++;

      }	
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:H".$fila);
	
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
   header('Content-Disposition: attachment;filename="PendietesDocente.xls"');
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
  

  