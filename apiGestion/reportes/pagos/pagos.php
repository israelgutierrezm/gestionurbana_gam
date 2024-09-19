<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {
  
  db('pagos');

  if($_SERVER['REQUEST_METHOD'] == "GET"){

  foreach($_GET as $clave => $valor){
    ${$clave} = escape_cara($valor);
  }

  $tipo_reporte = $_GET['tipo_reporte']; // 1 todos, 0 del día
      
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
  if($tipo_reporte == 0){ // solo del día
    $fechaActual = date('d-m-Y');
    $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de pagos del día: '.$fechaActual);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
  }else{
    $objPHPExcel->getActiveSheet()->setCellValue('A1','Reporte de pagos general');
    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);
  }

   $objPHPExcel->getActiveSheet()->getStyle('A5:S5')->applyFromArray($estiloTituloColumnas);
   $objPHPExcel->setActiveSheetIndex(0);
//    $objPHPExcel->getActiveSheet()->setTitle('ReporteAcceso');

   $objPHPExcel->getActiveSheet()->setCellValue('A5','Carrera');
   $objPHPExcel->getActiveSheet()->setCellValue('B5','Nombre');
   $objPHPExcel->getActiveSheet()->setCellValue('C5','Primer Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('D5','Segundo Apellido');
   $objPHPExcel->getActiveSheet()->setCellValue('E5','Clave Alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('F5','Situacion Alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('G5','Nombre del Pago');
   $objPHPExcel->getActiveSheet()->setCellValue('H5','Monto');
   $objPHPExcel->getActiveSheet()->setCellValue('I5','Estatus del Pago');
   $objPHPExcel->getActiveSheet()->setCellValue('J5','Fecha de Pago');
   $objPHPExcel->getActiveSheet()->setCellValue('K5','Beca');
   $objPHPExcel->getActiveSheet()->setCellValue('L5','Monto final');
   $objPHPExcel->getActiveSheet()->setCellValue('M5','Email');
   $objPHPExcel->getActiveSheet()->setCellValue('N5','Celular');
   $objPHPExcel->getActiveSheet()->setCellValue('O5','Tipo de Pago');
   $objPHPExcel->getActiveSheet()->setCellValue('P5','Usuario creación');
   $objPHPExcel->getActiveSheet()->setCellValue('Q5','Usuario actualiza');
   $objPHPExcel->getActiveSheet()->setCellValue('R5','Fecha creación');
   $objPHPExcel->getActiveSheet()->setCellValue('S5','Fecha actualiza');

    if($tipo_reporte == 0){
        $condicion = " and tsp.fecha_creacion >= CURDATE() and tsp.fecha_actualiza >= CURDATE()";
    }else{
        $condicion = '';
    }
  
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
            
  // $query = query('SELECT tc.carrera, p.nombre,p.primer_apellido, p.segundo_apellido,ta.clave_alumno,csa.situacion_alumno_descripcion as situacion_alumno,
  // tsp.nombre_pago, tsp.monto,cesp.descripcion as estatus_pago, 
  //   tsp.fecha_pago, (tsp.monto - tsp.monto_final) as beca, tsp.monto_final,p.email, p.celular, cfp.descripcion, tsp.usuario_creacion, 
  //   tsp.usuario_actualiza,tsp.fecha_creacion, tsp.fecha_actualiza
  //   from tr_solicitud_pago tsp
  //   join tr_pago tp on tp.pago_id = tsp.pago_id
  //   join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = tsp.persona_id
  //   join '.$GLOBALS['db_controlEscolar'].'.tr_alumno ta on ta.alumno_id = p.persona_id 
  //   left join inter_alumno_suscripcion ias on ias.alumno_id = ta.alumno_id
  //   join inter_suscripcion_carrera isc on isc.suscripcion_id = ias.suscripcion_id 
  //   join '.$GLOBALS['db_datosGenerales'].'.tr_carrera tc on tc.carrera_id = isc.carrera_id
  //   join '.$GLOBALS['db_controlEscolar'].'.cat_situacion_alumno csa on csa.situacion_alumno_id = ta.situacion_alumno_id
  //   join cat_forma_pago cfp on cfp.forma_pago_id = tsp.forma_pago_id
  //   join cat_estatus_solicitud_pago cesp on cesp.estatus_solicitud_pago_id = tsp.estatus_solicitud_pago_id 
  //   join '.$GLOBALS["db_controlEscolar"].'.inter_alumno_plan iap on iap.alumno_id = ta.alumno_id
  //   where tsp.estatus =1 and tp.estatus =1 and tsp.estatus =1 and p.estatus =1 and cfp.estatus =1 and cesp.estatus =1
  //   and tsp.persona_id != 2 '.$condicion.' '.$script.' order by tc.carrera_id,p.nombre, p.primer_apellido, p.segundo_apellido, 
  //   tsp.fecha_creacion desc');

      $query = query('SELECT tc.carrera, p.nombre,p.primer_apellido, p.segundo_apellido,ta.clave_alumno,csa.situacion_alumno_descripcion as situacion_alumno,
      tsp.nombre_pago, tsp.monto,cesp.descripcion as estatus_pago, 
      tsp.fecha_pago, (tsp.monto - tsp.monto_final) as beca, tsp.monto_final,p.email, p.celular, cfp.descripcion, tsp.usuario_creacion, 
      tsp.usuario_actualiza,tsp.fecha_creacion, tsp.fecha_actualiza
      from tr_solicitud_pago tsp
      join tr_suscripcion_pago tsp2 on tsp2.pago_id = tsp.pago_id and tsp2.estatus = 1
      join cat_suscripcion cs on cs.suscripcion_id = tsp2.suscripcion_id and cs.estatus = 1
      join tr_pago tp on tp.pago_id = tsp.pago_id
      join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = tsp.persona_id
      join '.$GLOBALS['db_controlEscolar'].'.tr_alumno ta on ta.alumno_id = p.persona_id 
      join inter_alumno_suscripcion ias on ias.alumno_id = ta.alumno_id
      join inter_suscripcion_carrera isc on isc.suscripcion_id = ias.suscripcion_id
      join '.$GLOBALS['db_datosGenerales'].'.tr_carrera tc on tc.carrera_id = isc.carrera_id
      join '.$GLOBALS['db_controlEscolar'].'.cat_situacion_alumno csa on csa.situacion_alumno_id = ta.situacion_alumno_id
      join cat_forma_pago cfp on cfp.forma_pago_id = tsp.forma_pago_id
      join cat_suscripcion cs2 on cs2.suscripcion_id = ias.suscripcion_id and cs2.estatus = 1
      join cat_estatus_solicitud_pago cesp on cesp.estatus_solicitud_pago_id = tsp.estatus_solicitud_pago_id 
      join '.$GLOBALS['db_controlEscolar'].'.inter_alumno_plan iap on iap.alumno_id = ta.alumno_id and iap.estatus = 1
      join '.$GLOBALS['db_datosGenerales'].'.inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id and iap.estatus = 1
      join '.$GLOBALS['db_datosGenerales'].'.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id and tpe.estatus = 1 and tpe.carrera_id = tc.carrera_id 
      where tsp.estatus =1 and tp.estatus =1 and tsp.estatus =1 and p.estatus =1 and ta.estatus =1  and cfp.estatus =1 and cesp.estatus =1
      and tsp.persona_id != 2 '.$condicion.' '.$script.' order by tc.carrera_id,p.nombre, p.primer_apellido, p.segundo_apellido, case 
      WHEN tsp2.tipo = 3 THEN 1 
      WHEN tsp2.tipo = 1 THEN 2 
      WHEN tsp2.tipo = 2 THEN 3
      end');

   $fila=6;
  while($arreglo = arreglo($query)){
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['carrera']);
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['nombre']);
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['primer_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['segundo_apellido']);
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['clave_alumno']);
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['situacion_alumno']);
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$arreglo['nombre_pago']);
      $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,'$'.$arreglo['monto']);
      $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$arreglo['estatus_pago']);
      $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$arreglo['fecha_pago']);
      $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$arreglo['beca']);
      $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$arreglo['monto_final']);
      $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$arreglo['email']);
      $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$arreglo['celular']);
      $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$arreglo['descripcion']);
      $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$arreglo['usuario_creacion']);
      $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$arreglo['usuario_actualiza']);
      $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$arreglo['fecha_creacion']);
      $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$arreglo['fecha_actualiza']);

      $fila++;
      }

      	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A6:S".$fila);

      foreach(range('A','S') as $columnID) { 
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
   if($tipo_reporte == 0){// del día
     header('Content-Disposition: attachment;filename="pagosDelDia.xls"');
    }else{
     header('Content-Disposition: attachment;filename="pagosGenerales.xls"');
   }
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
  

  