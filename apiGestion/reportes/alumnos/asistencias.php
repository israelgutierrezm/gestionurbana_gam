<?php
include '../../jwt.php';
include '../../headers.php';

try {
  
  db('learning');

  if($_SERVER['REQUEST_METHOD'] == "GET"){
    foreach($_GET as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }

      // $usuario = Auth::GetData(
      //       $jwt  
      //   );

        $query_materia = query('select tm.materia_id, tm.asignatura_grupo_id, ca.asignatura, p.nombre, p.primer_apellido, p.segundo_apellido
        from tr_materia tm 
        join '.$GLOBALS["db_controlEscolar"].'.inter_asignatura_grupo iag on iag.asignatura_grupo_id = tm.asignatura_grupo_id
        join '.$GLOBALS["db_datosGenerales"].'.inter_orden_asignatura ioa on ioa.orden_asignatura_id = iag.orden_asignatura_id
        join '.$GLOBALS["db_datosGenerales"].'.inter_plan_orden ipo on ipo.plan_orden_id = ioa.plan_orden_id
        join '.$GLOBALS["db_datosGenerales"].'.cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id
        join '.$GLOBALS["db_datosGenerales"].'.personas p on p.persona_id = tm.alumno_id
        where tm.estatus =1 and iag.estatus=1 and ioa.estatus=1 and ipo.estatus=1 and plan_estudio_id = '.$plan_estudio_id);

        $json = array();
        while($arreglo_materia = arreglo($query_materia)){
        
        $query_clases = query('SELECT count(tc.clase_id) as num_clases, 
        (count(tc.clase_id) - count(tic.inasistencia_clase_id)) as num_asistencia_clase,
        count(tic.inasistencia_clase_id) as num_inasistencias_clases
        from tr_materia tm
        join '.$GLOBALS["db_controlEscolar"].'.tr_clase tc on tm.asignatura_grupo_id = tc.asignatura_grupo_id
        left join '.$GLOBALS["db_controlEscolar"].'.tr_inasistencia_clase tic on tic.clase_id = tc.clase_id and tm.alumno_id = tic.persona_id
        where tm.alumno_id != 0 and tm.estatus=1 and tc.estatus=1 and tm.asignatura_grupo_id = '.$arreglo_materia['asignatura_grupo_id'].' and 
        tm.materia_id = '.$arreglo_materia['materia_id'].' group by tc.asignatura_grupo_id,tm.alumno_id');
        $json_clases = array();
        while($arreglo_clases = arreglo($query_clases)){
            array_push($json_clases, $arreglo_clases);
        }   


        $query_conferencias = query('SELECT  count(tv.videoconferencia_id) as num_conferencias, 
        (count(tv.videoconferencia_id) - (count(tv.videoconferencia_id) - count(tav.acceso_videoconferencia_id))) as num_asistencias_conferencias,
        (count(tv.videoconferencia_id) - count(tav.acceso_videoconferencia_id)) as num_inasistencias_conferencias
        from tr_materia tm
        join tr_videoconferencia tv on tv.asignatura_grupo_id = tm.asignatura_grupo_id 
        left join tr_acceso_videoconferencia tav on tav.videoconferencia_id = tv.videoconferencia_id and tav.persona_id = tm.alumno_id
        where tv.estatus != 0 and tm.asignatura_grupo_id = '.$arreglo_materia['asignatura_grupo_id'].' and tm.materia_id = '.$arreglo_materia['materia_id'].'
        group by tm.asignatura_grupo_id, tm.alumno_id ');
        $json_conferencias = array();
        while($arreglo_conferencias = arreglo($query_conferencias)){
             array_push($json_conferencias, $arreglo_conferencias);
        }   

        $arreglo_materia['clases'] = $json_clases;
        $arreglo_materia['conferencias'] = $json_conferencias;

        array_push($json, $arreglo_materia);
        
    }
        
    if(num($query_materia)){
        $json = array("status" => 1, "msg" => "Se encontró el alumno","alumno" => $json);
     }else{
        $json = array("status" => 0, "msg" => "No se encontró alumno");
     }



  }else{
  	$json = array("status" => 0, "msg" => "Método no aceptado");
  }

  /* Output header */

  echo json_encode($json);

} catch (Exception $e) {
    $json = array("status" => 0, "msg" =>  $e->getMessage());

    echo json_encode($json);
}
