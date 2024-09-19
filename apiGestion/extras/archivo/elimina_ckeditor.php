<?php
include '../../jwt.php';
include '../../headers.php';

try {
  
  db ('datosGenerales');

  if($_SERVER['REQUEST_METHOD'] == "GET"){
    foreach($_GET as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }

      $usuario = Auth::GetData(
            $jwt  
        );
        
      $default_url = '../../../';
      $archivo = stripslashes($url);
      $elimina_archivo= unlink( $default_url.$archivo );

       if($elimina_archivo){
    		$json = array("status" => 1, "msg" => "Se eliminó archivo correctamente" );
    	 }else{
    		$json = array("status" => 0, "msg" => "No se logro eliminar");
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