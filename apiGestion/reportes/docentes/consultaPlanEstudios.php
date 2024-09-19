<?php
include '../../jwt.php';
include '../../headers.php';
include '../../controlEscolar/class/asignaturagrupo.class.php';


try {

    db('controlEscolar');

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        $usuario = Auth::GetData(
            $jwt
        );

        $datos = arreglo(query('SELECT ipo.plan_estudio_id, ipo.orden_jerarquico_id, tpe.minima_aprobatoria AS minima_aprobatoria_plan_estudio, ca.calif_min AS minima_aprobatoria_asignatura
        FROM inter_asignatura_grupo iag 
            JOIN ' . $GLOBALS['db_datosGenerales'] . '.inter_orden_asignatura ioa ON iag.orden_asignatura_id = ioa.orden_asignatura_id
            JOIN ' . $GLOBALS['db_datosGenerales'] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id 
            JOIN ' . $GLOBALS['db_datosGenerales'] . '.inter_plan_orden ipo ON ioa.plan_orden_id = ipo.plan_orden_id
            JOIN ' . $GLOBALS['db_datosGenerales'] . '.tr_plan_estudios tpe ON tpe.plan_estudio_id = ipo.plan_estudio_id
            WHERE iag.asignatura_grupo_id = ' . $id_asignatura_grupo));

        if (count($datos) > 0) {
            $json = array(
                "status" => 1, "msg" => "Se encontraron datos", "id_plan_estudios" => $datos['plan_estudio_id'],
                "id_orden_jerarquico" => $datos['orden_jerarquico_id'], "minima_aprobatoria_plan_estudio" => $datos['minima_aprobatoria_plan_estudio'], 
                "minima_aprobatoria_asignatura" => $datos['minima_aprobatoria_asignatura']
            );
        } else {
            $json = array("status" => 0, "msg" => "No se encontraron datos");
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
