<?php
include '../../jwt.php';
include '../../headers.php';
include '../class/accesos.class.php';

try {
  
  db ('datosGenerales');

  $acceso = new Accesos();

  if($_SERVER['REQUEST_METHOD'] == "GET"){
    foreach($_GET as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }

    $usuario = Auth::GetData(
        $jwt  
    );

    $arreglo_acceso = $acceso::consultasesionActiva($id_usuario);
        
    if($arreglo_acceso != null){
      $json = array("status" => 1, "msg" => "Se encontró acceso", "acceso"=>$arreglo_acceso);
    }else{
      $json = array("status" => 0, "msg" => "No se encontró");
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