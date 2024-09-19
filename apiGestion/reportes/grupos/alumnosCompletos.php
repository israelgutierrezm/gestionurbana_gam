<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';
// include '../../class/asignaturagrupo.class.php';
include '../../controlEscolar/class/asignaturagrupo.class.php';

try {
  
  db('controlEscolar');
  $id_asignatura_grupo = $_GET['id_asignatura_grupo'];
  $grupo = new AsignaturaGrupo();
  $ini =0;
  $fin =500;


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


   
  $objPHPExcel->getActiveSheet()->setCellValue('B1','Reporte de Actividades Alumnos');
  
  $objPHPExcel->getActiveSheet()->getStyle('B1')->applyFromArray($estiloTituloReporte);

   $objPHPExcel->setActiveSheetIndex(0);
   $objPHPExcel->getActiveSheet()->setTitle('ReporteCalificacionesAlumos.');

   $consultaActividadesDocente=$grupo::consultaActividadesDocente($id_asignatura_grupo);    
   $ActividadesDocente = $consultaActividadesDocente['actividades'];

        
   $consulta_alumnos = $grupo::consultaAlumnos($id_asignatura_grupo,$ini,$fin);
   $alumno_asignatura_grupo= array();
   $arreglo_actividades_alumno = array();
  while ($arreglo_alumno_asignatura_grupo = arreglo($consulta_alumnos)){
   $numMensajesAlumno=$grupo::numMensajesAlumno($arreglo_alumno_asignatura_grupo['materia_id']);
   $arregloactividades = $grupo::consultaActividadesContestadasAlumno($arreglo_alumno_asignatura_grupo['materia_id'],$consultaActividadesDocente['actividades']);
   $arreglo_alumno_asignatura_grupo['actividadesAlumno']=$arregloactividades;
   array_push($arreglo_actividades_alumno, $arregloactividades);
   array_push($alumno_asignatura_grupo,$arreglo_alumno_asignatura_grupo); 
  }

$letras = ['a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','aa','ab','ac','ad','ae','af','ag','ah','ai','aj','ak','al','am','an','ao','ap','aq','ar','as','at','au','av','aw','ax','ay','az'];
$objPHPExcel->getActiveSheet()->setCellValue('A3','Id');
$objPHPExcel->getActiveSheet()->setCellValue('B3','Nombre del Alumno');   
$objPHPExcel->getActiveSheet()->setCellValue('C3','Matricula');   

$objPHPExcel->getActiveSheet()->getStyle('A3:C3')->applyFromArray($estiloTituloColumnas);

$num_actividades =  count($consultaActividadesDocente['actividades']);
$colum =3;
$letra= 3;
$ultima_letra =0;
$num_final= $num_actividades-1;
for ($i=0; $i < $num_actividades ; $i++) { 
  $celda = $letra+$i;
  $objPHPExcel->getActiveSheet()->setCellValue($letras[$celda].$colum,$consultaActividadesDocente['actividades'][$i]['actividad_nombre']);  
  $objPHPExcel->getActiveSheet()->getStyle($letras[$celda].$colum)->applyFromArray($estiloTituloColumnas);
  if($i == $num_final){
  $celda = $celda +1;
  $objPHPExcel->getActiveSheet()->setCellValue($letras[$celda].$colum,"Calificación");  
  $objPHPExcel->getActiveSheet()->getStyle($letras[$celda].$colum)->applyFromArray($estiloTituloColumnas);
  }
}





$fila=4;
$numero_lista = 1;
$num_alumno = count($alumno_asignatura_grupo);
for ($j=0; $j < $num_alumno ; $j++) {
  $objPHPExcel->getActiveSheet()->setCellValue("A".$fila,$numero_lista);  
  $objPHPExcel->getActiveSheet()->setCellValue("B".$fila,$alumno_asignatura_grupo[$j]['nombre'].$alumno_asignatura_grupo[$j]['primer_apellido'].$alumno_asignatura_grupo[$j]['segundo_apellido']);  
  $objPHPExcel->getActiveSheet()->setCellValue("C".$fila,$alumno_asignatura_grupo[$j]['clave_alumno']);  
  $fila++; $numero_lista++;

}


$colum_cal=4;
$letra_cal=3;
$num_actividades_alumno = count($arreglo_actividades_alumno);
for ($k=0; $k < $num_actividades_alumno ; $k++) {
  $num_actividades_arreglo = count($arreglo_actividades_alumno[$k]);
  $num_act_alu_final = $num_actividades_arreglo-1;
  for ($l=0; $l < $num_actividades_arreglo ; $l++) { 
    $celda_cal = $letra_cal+$l;
    if(empty($arreglo_actividades_alumno[$k][$l]['calificacion'])){
      $arreglo_actividades_alumno[$k][$l]['calificacion'] = "-";
    }
      $objPHPExcel->getActiveSheet()->setCellValue($letras[$celda_cal].$colum_cal,$arreglo_actividades_alumno[$k][$l]['calificacion']);      
  
      if($l >= $num_act_alu_final){
        if(empty($arreglo_actividades_alumno[$k]['calificacion'])){
          $arreglo_actividades_alumno[$k]['calificacion'] = "-";
        }    
        $celda_cal = $celda_cal+1;
        $objPHPExcel->getActiveSheet()->setCellValue($letras[$celda_cal].$colum_cal,$alumno_asignatura_grupo[$k]['calificacion']);    
      }
    }

    $colum_cal++;
}


foreach(range('A','F') as $columnID) { 
    $objPHPExcel->getActiveSheet()->getColumnDimension($columnID) ->setAutoSize(true); 
} 

    
if($GLOBALS['version']==5){
  $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');  
}else{
  $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel7');  
}

   header('Content-Type: application/vnd.ms-excel');
   header('Content-Disposition: attachment;filename="reporteAlumnosReales.xls"');
   header('Cache-Control: max-age=0');
   $writer->save('php://output');


    }else{
      $json = array("status" => 0, "msg" => "Método no aceptado");
    }
  
    /* Output header */
  
    // echo encode($json);
  
  } catch (Exception $e) {
      echo  $e->getMessage();
  }
  

  