<?php
include '../../jwt.php';
include '../../headers.php';

try {

  db ('SMTP');

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    foreach($_POST as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }
    
      $usuario = Auth::GetData(
            $jwt  
        );

        
         //if ($instituciones_db != $institucion_clave) {ok
          $edita = update('tr_correo',
          'correo_from = "'.$correo_from.'",
          correo_to = "'.$correo_to.'",
          correo_cc = "'.$correo_cc.'",
          correo_bco = "'.$correo_bco.'",
          correo_subject = "'.$correo_subject.'",
          correo_body = "'.$correo_body.'",
          fecha_envio = now(),
          configuracion_id = '.$id_configuracion.',
          estatus_correo_id = '.$id_correo_estatus.',
          fecha_actualiza = now(), 
          institucion_id = '.$usuario->id_institucion.',
          usuario_actualiza = current_user(),
          estatus = 1',
          'correo_id = '.$id_correo);


       
       //ingreso todas las carreras que tiene
       
       if($edita){
    		$json = array("status" => 1, "msg" => "Se actualizó el correo correctamente");
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
