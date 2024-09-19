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
          $edita = update('cat_grupo_correo',
          'grupo_correo = "'.$grupo_correo.'",
          fecha_actualiza = now(),
          usuario_actualiza = current_user()',
          'grupo_correo_id = '.$id_correo_grupo);


       
       //ingreso todas las carreras que tiene
       
       if($edita){
    		$json = array("status" => 1, "msg" => "Se actualizó el grupo correctamente");
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
