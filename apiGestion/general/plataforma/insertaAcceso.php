<?php
include '../../jwt.php';
include '../../headers.php';
include '../class/accesos.class.php';

try {

  db('datosGenerales');

  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    foreach ($_POST as $clave => $valor) {
      ${$clave} = escape_cara($valor);
    }
    $acceso = new Accesos();

    if ($jwt == ''){
      $jwt = $jwt2;
    }

    $usuario = Auth::GetData(
      $jwt
    );

    $inserta_acceso = $acceso::nuevo_acceso($usuario->id_usuario, $ip_publica, $ventana, $dispositivo, $navegador, $usuario->tiempo_sesion);

    if ($inserta_acceso) {
      $json = array("status" => 1, "msg" => "Se insertó acceso correctamente");
    } else {
      $json = array("status" => 0, "msg" => "No se logro insertar");
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
