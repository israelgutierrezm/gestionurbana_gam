<?php
include '../../jwt.php';
include '../../headers.php';
include '../class/accesos.class.php';

try {

  db ('datosGenerales');

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    foreach($_POST as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }

    $acceso = new Accesos();

      
      $finaliza_sesion = $acceso::finaliza_sesion($id_usuario);
           
       //ingreso todas las carreras que tiene
       
       if($finaliza_sesion){
    		$json = array("status" => 1, "msg" => "Se actualizo fecha_fin");
    	 }else{
    		$json = array("status" => 0, "msg" => "No se logró actualizar");
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
