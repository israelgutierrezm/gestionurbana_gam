<?php
include '../../jwt.php';
include '../../headers.php';

try {

    db('datosGenerales');

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    foreach($_POST as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }     

        
        $consulta_token = query('SELECT token_pass_id, usuario_id, nombre, primer_apellido, segundo_apellido
                from tr_token_pass tkp
                join inter_persona_usuario_rol ipur on ipur.persona_id = tkp.persona_id
                join personas p on ipur.persona_id = p.persona_id
                where token ='.$token.' and tkp.estatus=1');

        $arreglo=arreglo($consulta_token);
       
       
       if(num($consulta_token)){
    		$json = array("status" => 1, "msg" => "Token valido", "usuario_id"=>$arreglo['usuario_id'], "nombre"=>$arreglo['nombre'].' '.$arreglo['primer_apellido'].' '.$arreglo['segundo_apellido']);
    	 }else{
    		$json = array("status" => 0, "msg" => "Token no existe");
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
