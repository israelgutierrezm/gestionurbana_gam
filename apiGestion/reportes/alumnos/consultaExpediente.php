<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('seguimiento');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

      /*$usuario = Auth::GetData(
            $jwt  
        );*/

      // $arreglo_plan = arreglo(query('SELECT tpe.plan_estudio_id,plan_estudio, tpe.carrera_id, tc.carrera
      // from '.$GLOBALS["db_datosGenerales"].'.tr_plan_estudios tpe
      // join '.$GLOBALS["db_datosGenerales"].'.tr_carrera tc on tc.carrera_id = tpe.carrera_id
      // where plan_estudio_id ='.$id_plan_estudios.' and tpe.estatus = 1 and tc.estatus = 1'));
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


      $query=query('SELECT tpe.plan_estudio,coj.orden_jerarquico_descripcion as grado, nombre as nombre_alumno, p.primer_apellido, p.segundo_apellido, 
      cd.nombre_documento as documento,ie.url,ced.nombre_documento as estado_documento,cc.campus
      from inter_expediente ie 
      join cat_estado_documento ced on ced.estado_documento_id = ie.estado_documento_id
      join cat_documento cd on cd.documento_id = ie.documento_id
      join '.$GLOBALS['db_datosGenerales'].'.personas p on ie.aspirante_id = p.persona_id
      join '.$GLOBALS['db_controlEscolar'].'.tr_alumno ta on ta.alumno_id = p.persona_id
      left join '.$GLOBALS['db_controlEscolar'].'.inter_alumno_plan iap on iap.alumno_id = ta.alumno_id  and iap.estatus=1
      left join '.$GLOBALS['db_datosGenerales'].'.inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id  and ipo.estatus=1
      left join '.$GLOBALS['db_datosGenerales'].'.cat_orden_jerarquico coj on coj.orden_jerarquico_id = ipo.orden_jerarquico_id
      left join '.$GLOBALS['db_datosGenerales'].'.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id and tpe.estatus=1
      left join '.$GLOBALS['db_datosGenerales'].'.cat_campus cc on cc.campus_id = iap.campus_id
      where ced.estatus =1 and cd.estatus=1 and ta.estatus=1 and
      ie.estatus=1 and cd.estatus =1 and p.estatus=1 '.$script.' order by nombre, primer_apellido, segundo_apellido ');
    

  
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

  $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($estiloTituloColumnas);

       
   $objPHPExcel->getActiveSheet()->setTitle('ExpedientesAlumnos');
      
   
   $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de Expedientes.');
  //  $objPHPExcel->getActiveSheet()->setCellValue('A2',$arreglo_plan['plan_estudio']);
  //  $objPHPExcel->getActiveSheet()->setCellValue('C1','Carrera');
  //  $objPHPExcel->getActiveSheet()->setCellValue('C2',$arreglo_plan['carrera']);
  

   $objPHPExcel->getActiveSheet()->getStyle('D3')->getAlignment()->setWrapText(true);
   $objPHPExcel->getActiveSheet()->setCellValue('A3','Plan de Estudios');
   $objPHPExcel->getActiveSheet()->setCellValue('B3','Grado');
   $objPHPExcel->getActiveSheet()->setCellValue('C3','Nombre_alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('D3','Primer_apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('E3','Segundo_Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('F3','Tipo_documento');
   $objPHPExcel->getActiveSheet()->setCellValue('G3','Estado_documento');
   $objPHPExcel->getActiveSheet()->setCellValue('H3','Url_documento');
   $objPHPExcel->getActiveSheet()->setCellValue('I3','Campus');

   $fila=4;
    while($arreglo =  arreglo($query)){
      $arreglo['url']=$GLOBALS['url_front'].substr($arreglo['url'], 1);
      $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15); 
      $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo);
      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['plan_estudio']); 
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['grado']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['nombre_alumno']); 
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['segundo_apellido']); 
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['documento']); 
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$arreglo['estado_documento']); 
      $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$arreglo['url']); 
      $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$arreglo['campus']); 


      $fila++;

      }	
	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:I".$fila);
	
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
   header('Content-Disposition: attachment;filename="ExpedientesAlumnos.xls"');
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
  

  