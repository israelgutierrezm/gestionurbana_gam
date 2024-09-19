<?php
include '../../jwt.php';
include '../../headers.php';

try {
  if ($_SERVER['REQUEST_METHOD'] == "GET") {
    foreach ($_GET as $clave => $valor) {
      ${$clave} = escape_cara($valor);
    }

    $usuario = Auth::GetData(
      $jwt
    );

    $query_area = query('SELECT area_id, administrador_id
    FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_administrador ta
    JOIN inter_persona_usuario_rol uri ON uri.usuario_id = ta.administrador_id
    WHERE  uri.estatus=1 and uri.usuario_id = ' . $usuario->id_usuario . ' AND uri.rol_id =' . $usuario->id_rol . ' ');

    $arreglo_area = arreglo($query_area);
    $query_privilegios_padre = query('SELECT distinct ipa.area_id, cp.privilegio_id, privilegio_clave, privilegio, icono, url
        FROM cat_privilegios cp
        JOIN inter_rol_privilegios irp ON cp.privilegio_id = irp.privilegio_id
        JOIN ' . $GLOBALS['db_controlEscolar'] . '.inter_privilegio_area ipa ON ipa.privilegio_id = irp.privilegio_id  	
        WHERE ipa.estatus = 1 AND cp.estatus = 1 AND privilegio_padre_id IS NULL AND area_id = ' . $arreglo_area['area_id']);

    $arreglo_privilegios = array();

    while ($privilegio_padre = arreglo($query_privilegios_padre)) {
      $query_subprivilegios = query('SELECT cp.privilegio_id, privilegio_clave, privilegio, icono, url, tpa.edicion
          FROM cat_privilegios cp
          JOIN ' . $GLOBALS['db_controlEscolar'] . '.inter_privilegio_area ipa ON ipa.privilegio_id = cp.privilegio_id 
          LEFT JOIN ' . $GLOBALS['db_controlEscolar'] . '.tr_privilegios_administrador tpa ON cp.privilegio_id = tpa.privilegio_id 
          AND tpa.administrador_id = ' . $arreglo_area['administrador_id'] . '
          WHERE  cp.estatus = 1 AND privilegio_padre_id = ' . $privilegio_padre['privilegio_id'] . ' AND area_id = ' . $arreglo_area['area_id']);

        $arreglo_subprivilegios = array();

        while ($subprivilegio = arreglo($query_subprivilegios)) {
          array_push($arreglo_subprivilegios, $subprivilegio);
        }
        $privilegio_padre['sub_privilegios'] = $arreglo_subprivilegios;

        array_push($arreglo_privilegios, $privilegio_padre);
    }

    if (num($query_privilegios_padre)) {
      $json = array("status" => 1, "msg" => "Se encontraron privilegios", "privilegios" => $arreglo_privilegios);
    } else {
      $json = array("status" => 0, "msg" => "No se encontraron privilegios");
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
