
<?php
include '../../jwt.php';
include '../../headers.php';
try {
  
  db ('datosGenerales');

  if($_SERVER['REQUEST_METHOD'] == "GET"){
    foreach($_GET as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }

     /*$usuario = Auth::GetData(
            $jwt  
      );*/

        $query= query('select orden_jerarquico_id,orden_jerarquico,carrera, tipo_plan_estudio as modalidad,
        "SEP" as "dependencia",
        rvoe,
        carrera_id,
        nivel_estudios, substring(rvoe,25) as fecha_expediencion_rvoe
        ,total_periodos,periodo, total_creditos,
        DATE_SUB(fecha_inicio, INTERVAL 15 DAY) as pre_inicio_ciclo,
        fecha_inicio as inicio_ciclo, fecha_fin as fin_ciclo 
        from (select @id_ciclo := '.$id_ciclo.' ciclo_id) as vistas,'.$GLOBALS["db_reporte"].'.informacion_ciclo 
        group by carrera_id, orden_jerarquico_id
        ');

        $json=array();
        while($arreglo = arreglo($query)){
          

            $query_alumnos = query('SELECT ta.alumno_id, ta.situacion_alumno_id,curp, UPPER(substring(curp,11,1)) as sexo,
            YEAR(CURDATE()) - if (substring(curp,5,2) < 30,concat("20",substring(curp,5,2)), concat("19",substring(curp,5,2)))  as edad,
            ta.fecha_creacion
            from (select @id_ciclo := '.$id_ciclo.' ciclo_id) as vistas,  '.$GLOBALS["db_reporte"].'.informacion_ciclo ic 
            join '.$GLOBALS["db_controlEscolar"].'.inter_alumno_plan iap on iap.plan_orden_id = ic.plan_orden_id
            join '.$GLOBALS["db_controlEscolar"].'.tr_alumno ta on ta.alumno_id = iap.alumno_id
            join '.$GLOBALS["db_datosGenerales"].'.personas p on p.persona_id = ta.alumno_id
            where ta.situacion_alumno_id = 1 and iap.estatus = 1 and ic.carrera_id = '.$arreglo['carrera_id'].'');

            $total_alumnos = $alumnos_hombres = $alumnos_mujeres = $alumno_m_21 = $alumnos_22 = $alumnos_23 = 
            $alumnos_24 = $alumnos_25 = $alumnos_26_29 = $alumnos_M_30 = $alumnos_sin = $alumnos_egresados = $alumnos_primer_ingreso = 0 ;

            while($arreglo_alumnos = arreglo($query_alumnos)){

              $total_alumnos++;
              
              if($arreglo_alumnos['sexo'] == 'H') $alumnos_hombres++;
              else if($arreglo_alumnos['sexo'] == 'M') $alumnos_mujeres++;
              else {
                $alumnos_sin++;
              }

              if($arreglo_alumnos['edad'] > 1 &&  $arreglo_alumnos['edad'] <= 21) $alumno_m_21++;
              else if($arreglo_alumnos['edad'] == 22) $alumnos_22++;
              else if($arreglo_alumnos['edad'] == 23) $alumnos_23++;
              else if($arreglo_alumnos['edad'] == 24) $alumnos_24++;
              else if($arreglo_alumnos['edad'] == 25) $alumnos_25++;
              else if($arreglo_alumnos['edad'] >= 26 && $arreglo_alumnos['edad'] <= 30 ) $alumnos_26_29++;
              else if($arreglo_alumnos['edad'] >= 30 && $arreglo_alumnos['edad'] < 100 ) $alumnos_M_30++;

              if($arreglo_alumnos['situacion_alumno_id'] == "4"){
                $alumnos_egresados++;
              }

              $inicio_ciclo = strtotime(date($arreglo['pre_inicio_ciclo']));  
              $fin_ciclo = strtotime(date($arreglo['fin_ciclo']));  
              $fecha_creacion = strtotime(date($arreglo_alumnos['fecha_creacion']));  

              if ($fecha_creacion > $inicio_ciclo && $fecha_creacion < $fin_ciclo){
                $alumnos_primer_ingreso++;
              }


              
            }
            

            $arreglo['total'] =$total_alumnos;
            $arreglo['alumnos_hombres'] =$alumnos_hombres;
            $arreglo['alumnos_mujeres'] =$alumnos_mujeres;
            $arreglo['alumnos_sin'] =$alumnos_sin;
            $arreglo['alumno_m_21'] =$alumno_m_21;
            $arreglo['alumnos_22'] =$alumnos_22;
            $arreglo['alumnos_23'] =$alumnos_23;
            $arreglo['alumnos_24'] =$alumnos_24;
            $arreglo['alumnos_25'] =$alumnos_25;
            $arreglo['alumnos_26_29'] =$alumnos_26_29;
            $arreglo['alumnos_M_30'] =$alumnos_M_30;
            $arreglo['alumnos_primer_ingreso'] =$alumnos_primer_ingreso;
            $arreglo['alumnos_egresados'] =$alumnos_egresados;
            

            

            if($total_alumnos != 0){
              array_push($json,$arreglo);
              
            }
            

        }
        
       

       if(num($query)){
    		$json = array("status" => 1, "msg" => "Se encontro la información","info" => $json);
    	 }else{
    		$json = array("status" => 0, "msg" => "No se encontro la actividad");
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