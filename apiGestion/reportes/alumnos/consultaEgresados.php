<?php
include '../../jwt.php';
include '../../headers.php';

try {

  db('datosGenerales');

  if ($_SERVER['REQUEST_METHOD'] == "GET") {
    foreach ($_GET as $clave => $valor) {
      ${$clave} = escape_cara($valor);
    }

    $usuario = Auth::GetData(
      $jwt
    );

    $query_egresados = query('SELECT ta.clave_alumno, ta.alumno_id, ta.situacion_alumno_id, p.curp, p.nombre, 
    p.primer_apellido, p.segundo_apellido,IF(SUBSTRING(p.curp, 11,1) = "M", "250", "251") AS genero_id, 
    DATE_FORMAT(p.fecha_nacimiento , "%d/%m/%Y") AS fecha_nacimiento, ctp.periodo_clave AS tipo_periodo_id, 
    ipo.plan_estudio_id, tpe.plan_estudio_clave, tpe.plan_estudio, tc.carrera_id, tpe.rvoe
    FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_alumno ta 
    JOIN personas p ON p.persona_id = ta.alumno_id
    JOIN ' . $GLOBALS['db_controlEscolar'] . '.inter_alumno_plan iap ON iap.alumno_id = ta.alumno_id 
    JOIN inter_plan_orden ipo ON ipo.plan_orden_id = iap.plan_orden_id
    JOIN tr_plan_estudios tpe ON tpe.plan_estudio_id = ipo.plan_estudio_id 
    JOIN cat_tipo_periodo ctp ON ctp.tipo_periodo_id = tpe.tipo_periodo_id
    JOIN tr_carrera tc ON tc.carrera_id = tpe.carrera_id
    WHERE p.estatus = 1 AND iap.situacion_alumno_id = 4 AND tc.carrera_id = ' . $carrera_id);

    $json_egresados = array();
    while ($arreglo_egresados = arreglo($query_egresados)) {
      array_push($json_egresados, $arreglo_egresados);
    }

    if (num($query_egresados)) {
      $json = array("status" => 1, "msg" => "Se encontraron alumnos egresados", "egresados" => $json_egresados);
    } else {
      $json = array("status" => 0, "msg" => "No se encontraron alumnos egresados");
    }
  } else {
    $json = array("status" => 0, "msg" => "Método no aceptado");
  }

  /* Output header */

  echo json_encode($json);
} catch (Exception $e) {
  $json = array("status" => 0, "msg" =>  $e->getMessage());

  echo json_encode($json);
}
