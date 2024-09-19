<?php
include '../../jwt.php';
include '../../headers.php';
include './class/bitacoraAccion.class.php';

db('datosGenerales');

try {

  $accion = new bitacoraAccion();

  if ($_SERVER['REQUEST_METHOD'] == "POST") {
    foreach ($_POST as $clave => $valor) {
      ${$clave} = escape_cara($valor);
    }

    // $usuario = Auth::GetData(
    //       $jwt  
    //   );

    $inserta_accion = $accion::insertaAccion($id_accion, $id_usuario);

    if ($inserta_accion) {
      $json = array("status" => 1, "msg" => "Se insertó acción correctamente");
    } else {
      $json = array("status" => 0, "msg" => "No se logró insertar");
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
