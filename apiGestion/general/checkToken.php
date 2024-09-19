<?php
include '../jwt.php';
include '../headers.php';
try {

  foreach($_GET as $clave => $valor){
    ${$clave} = $valor;
  }


  $token = Auth::Check($jwt);
    $info_token = Auth::GetData(
            $jwt  
        );
        // print_r($usuario);
  
    $json = array("status" => 1, "msg" =>'Active Token', "jwt"=> $info_token);
  
    /* Output header */
    	echo json_encode($json);

} catch (Exception $e) {
    $json = array("status" => 0, "msg" => $e->getMessage());
    /* Output header */
    echo json_encode($json);
}
