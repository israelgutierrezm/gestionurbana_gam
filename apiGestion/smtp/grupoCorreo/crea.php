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
         
        //
        $inserta = inserta_last_id('cat_grupo_correo','grupo_correo, fecha_creacion, usuario_crea, estatus',
        '"'.$grupo_correo.'", now(), current_user(),1');

       
       //ingreso todas las escuelas que tiene este profesor
       
       if($inserta){
    		$json = array("status" => 1, "msg" => "Se insertó el grupo correctamente", "id_correo_grupo" => $inserta);
    	 }else{
    		$json = array("status" => 0, "msg" => "No se logró insertar");
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
