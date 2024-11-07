<?php
include '../jwt.php';
include '../headers.php';
try {

  foreach($_GET as $clave => $valor){
    ${$clave} = $valor;
  }
  
    $jwt = substr($jwt, 1, -1);
    $info_token = Auth::getDataJWT(
            $jwt  
        );
    $json = array("estatus" => 1, "msg" =>'Active Token', "usuario"=> $info_token);
    	echo json_encode($json);
} catch (Exception $e) {
    $json = array("estatus" => 0, "msg" => $e->getMessage());
    echo json_encode($json);
}
