<?php
include '../../jwt.php';
include '../../headers.php';

try {

  db('SMTP');

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    foreach($_POST as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }
    
      $usuario = Auth::GetData(
            $jwt  
        );

         
        //devuelve el último id insertado
        $inserta = inserta_last_id('tr_correo','correo_from, correo_to, correo_cc, correo_bco, correo_subject,
         correo_body, fecha_envio, configuracion_id, estatus_correo_id,fecha_creacion,
        institucion_id, usuario_crea ,estatus',
        '"'.$correo_from.'","'.$correo_to.'", "'.$correo_cc.'","'.$correo_bco.'","'.$correo_subject.'"
        ,"'.$correo_body.'", now(), '.$id_configuracion.', '.$id_correo_estatus.', now(),
        '.$usuario->id_institucion.', current_user(),1');


       
  
      if($inserta){
    		$json = array("status" => 1, "msg" => "Se insertó el correo correctamente", "id_correo" => $inserta);
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
