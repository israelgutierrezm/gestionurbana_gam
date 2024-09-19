<?php

include '../../jwt.php';
include '../../learning/class/materia.class.php';
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

      
  $materia = new Materias();
      

/*$query_alumnos = query('SELECT ta.alumno_id,p.nombre,p.primer_apellido, p.segundo_apellido, ta.clave_alumno,email
from (select @id_ciclo := '.$id_ciclo.' ciclo_id) as vistas,  '.$GLOBALS["db_reporte"].'.informacion_ciclo ic 
join '.$GLOBALS["db_controlEscolar"].'.inter_alumno_plan iap on iap.plan_orden_id = ic.plan_orden_id
join '.$GLOBALS["db_controlEscolar"].'.tr_alumno ta on ta.alumno_id = iap.alumno_id
join '.$GLOBALS["db_datosGenerales"].'.personas p on p.persona_id = ta.alumno_id
where iap.estatus = 1 and ic.carrera_id = '.$id_carrera.' '); */

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

$query_alumnos =query('select tm.materia_id ,tm.asignatura_grupo_id,tm.alumno_id,
ta.clave_alumno, p.nombre,p.primer_apellido ,p.segundo_apellido,p.email ,p.celular,csa.situacion_alumno_descripcion,
 ca.asignatura_clave , ca.asignatura,coj.orden_jerarquico,cpe.plan_estudio,tg.grupo
from tr_materia tm 
join '.$GLOBALS['db_controlEscolar'].'.inter_asignatura_grupo iag on iag.asignatura_grupo_id = tm.asignatura_grupo_id 
join '.$GLOBALS['db_controlEscolar'].'.tr_grupo tg on tg.grupo_id = iag.grupo_id 
join '.$GLOBALS['db_controlEscolar'].'.cat_ciclo cc on cc.ciclo_id = tg.ciclo_id 
join '.$GLOBALS['db_controlEscolar'].'.tr_alumno ta on ta.alumno_id = tm.alumno_id 
join '.$GLOBALS['db_controlEscolar'].'.cat_situacion_alumno csa on csa.situacion_alumno_id = ta.situacion_alumno_id
join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = ta.alumno_id 
join '.$GLOBALS['db_datosGenerales'].'.inter_orden_asignatura ioa on ioa.orden_asignatura_id = iag.orden_asignatura_id 
join '.$GLOBALS['db_datosGenerales'].'.inter_plan_orden ipo on ipo.plan_orden_id = ioa.plan_orden_id 
join '.$GLOBALS['db_datosGenerales'].'.cat_orden_jerarquico coj on coj.orden_jerarquico_id = ipo.orden_jerarquico_id
join '.$GLOBALS['db_datosGenerales'].'.tr_plan_estudios cpe on cpe.plan_estudio_id = ipo.plan_estudio_id
join '.$GLOBALS['db_datosGenerales'].'.cat_asignaturas ca on ca.asignatura_id =ioa.asignatura_id 
join '.$GLOBALS["db_controlEscolar"].'.inter_alumno_plan iap on iap.alumno_id = ta.alumno_id
where cc.ciclo_estatus_id =1 and iag.situacion_asignatura_grupo_id in (1,2,3) 
and tm.estatus = 1 and iag.estatus =1 and tg.estatus =1 and ta.estatus = 1 and ipo.estatus =1 and ioa.estatus = 1 
and cpe.plan_estudio_id = '.$id_plan_estudios.' '.$script.'
group by ta.alumno_id
');


    $materias = array();
      while($arreglo_alumnos = arreglo($query_alumnos)){ 

        $query = $materia::materiasActivas($arreglo_alumnos['alumno_id']);

        $total_actividades=0;
            
            while ($arreglo = arreglo($query)){   

            $arreglo_docente = arreglo(query('select p.nombre,p.primer_apellido,p.segundo_apellido,email from '.$GLOBALS["db_controlEscolar"].'.inter_docente_asignatura_grupo idag
            join '.$GLOBALS["db_datosGenerales"].'.personas p on p.persona_id = idag.docente_id
            where idag.estatus=1 and p.estatus=1 and idag.asignatura_grupo_id = '.$arreglo['asignatura_grupo_id']));
                    
            $arreglo_actividades = $materia::avanceDetalladoMateria($arreglo['materia_id']);
            $arreglo_actividades['asignatura'] = $arreglo['asignatura'];
            $arreglo_actividades['calificacion'] = $arreglo['calificacion'];
            $arreglo_actividades['nombre_docente'] = $arreglo_docente['nombre'].''.$arreglo_docente['primer_apellido'].' '.$arreglo_docente['segundo_apellido'];
            $arreglo_actividades['email'] = $arreglo_docente['email'];
            $arreglo_actividades['nombre_alumno'] = $arreglo_alumnos['nombre'].' '.$arreglo_alumnos['primer_apellido'].' '.$arreglo_alumnos['segundo_apellido'];
            $arreglo_actividades['email_alumno'] = $arreglo_alumnos['email'];
            $arreglo_actividades['celular_alumno'] = $arreglo_alumnos['celular'];
            $arreglo_actividades['clave_alumno'] = $arreglo_alumnos['clave_alumno'];
            $arreglo_actividades['situacion_alumno_descripcion'] = $arreglo_alumnos['situacion_alumno_descripcion'];

            
            $arreglo_actividades['orden_jerarquico'] = $arreglo_alumnos['orden_jerarquico'];
            $arreglo_actividades['plan_estudio'] = $arreglo_alumnos['plan_estudio'];
            $arreglo_actividades['asignatura_clave'] = $arreglo_alumnos['asignatura_clave'];
            $arreglo_actividades['grupo'] = $arreglo_alumnos['grupo'];

            array_push($materias,$arreglo_actividades);    
            
    }
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

  $objPHPExcel->getActiveSheet()->getStyle('A4:S4')->applyFromArray($estiloTituloColumnas);

       
   $objPHPExcel->getActiveSheet()->setTitle('ReporteGeneral');
      
   
   $objPHPExcel->getActiveSheet()->setCellValue('A1','Plan de Estudio');
//    $objPHPExcel->getActiveSheet()->setCellValue('A2',$arreglo_plan['plan_estudio']);
  

   $objPHPExcel->getActiveSheet()->getStyle('D4')->getAlignment()->setWrapText(true);
   $objPHPExcel->getActiveSheet()->setCellValue('A4','Nombre del Docente');
   $objPHPExcel->getActiveSheet()->setCellValue('B4','Email el Docente');
   $objPHPExcel->getActiveSheet()->setCellValue('C4','Nombre del Alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('D4','Email del Alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('E4','Celular del Alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('F4','Clave del Alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('G4','Situacion del Alumno');
   $objPHPExcel->getActiveSheet()->setCellValue('H4','Plan de estudios');
   $objPHPExcel->getActiveSheet()->setCellValue('I4','Grado');
   $objPHPExcel->getActiveSheet()->setCellValue('J4','Clave de la asignatura');
   $objPHPExcel->getActiveSheet()->setCellValue('K4','Asignatura');
   $objPHPExcel->getActiveSheet()->setCellValue('L4','Grupo');
   $objPHPExcel->getActiveSheet()->setCellValue('M4','Total de Actividades');
   $objPHPExcel->getActiveSheet()->setCellValue('N4','Actividades Contestas');
   $objPHPExcel->getActiveSheet()->setCellValue('O4','Actividades Incompletas');
   $objPHPExcel->getActiveSheet()->setCellValue('P4','Actividades sin Calificar');   
   $objPHPExcel->getActiveSheet()->setCellValue('Q4','Actividades en Borrador');
   $objPHPExcel->getActiveSheet()->setCellValue('R4','Promedio de Actividades');
   $objPHPExcel->getActiveSheet()->setCellValue('S4','Promedio Total');
   //$objPHPExcel->getActiveSheet()->setCellValue('N4','Calificacion');


   $fila=5;
    foreach($materias as $arreglo){
      $objPHPExcel->getActiveSheet()->getRowDimension($fila)->setRowHeight(15); 
      $objPHPExcel->getDefaultStyle()->applyFromArray($arreglo);

      $objPHPExcel->getActiveSheet()->setCellValue('A'.$fila,$arreglo['nombre_docente']); 
      $objPHPExcel->getActiveSheet()->setCellValue('B'.$fila,$arreglo['email']); 
      $objPHPExcel->getActiveSheet()->setCellValue('C'.$fila,$arreglo['nombre_alumno']);
      $objPHPExcel->getActiveSheet()->setCellValue('D'.$fila,$arreglo['email_alumno']); 
      $objPHPExcel->getActiveSheet()->setCellValue('E'.$fila,$arreglo['celular_alumno']); 
      $objPHPExcel->getActiveSheet()->setCellValue('F'.$fila,$arreglo['clave_alumno']); 
      $objPHPExcel->getActiveSheet()->setCellValue('G'.$fila,$arreglo['situacion_alumno_descripcion']); 
      $objPHPExcel->getActiveSheet()->setCellValue('H'.$fila,$arreglo['plan_estudio']); 
      $objPHPExcel->getActiveSheet()->setCellValue('I'.$fila,$arreglo['orden_jerarquico']); 
      $objPHPExcel->getActiveSheet()->setCellValue('J'.$fila,$arreglo['asignatura_clave']); 
      $objPHPExcel->getActiveSheet()->setCellValue('K'.$fila,$arreglo['asignatura']); 
      $objPHPExcel->getActiveSheet()->setCellValue('L'.$fila,$arreglo['grupo']); 
      $objPHPExcel->getActiveSheet()->setCellValue('M'.$fila,$arreglo['total']); 
      $objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$arreglo['contestadas']); 
      $objPHPExcel->getActiveSheet()->setCellValue('O'.$fila,$arreglo['incompletas']); 
      $objPHPExcel->getActiveSheet()->setCellValue('P'.$fila,$arreglo['actividadesSinCalificacion']); 
      $objPHPExcel->getActiveSheet()->setCellValue('Q'.$fila,$arreglo['actividadesBorrador']); 
      $objPHPExcel->getActiveSheet()->setCellValue('R'.$fila,$arreglo['promedioActividades']); 
      $objPHPExcel->getActiveSheet()->setCellValue('S'.$fila,$arreglo['promedioTotal']); 
      //$objPHPExcel->getActiveSheet()->setCellValue('N'.$fila,$arreglo['calificacion']); 


      $fila++;
      }	

	$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A5:S".$fila);
	
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
   header('Content-Disposition: attachment;filename="AvanceGeneralAlumno.xls"');
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
  

  