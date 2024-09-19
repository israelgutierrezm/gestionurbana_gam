<?php
include '../../jwt.php';
include '../../headers.php';

try {

  db('datosGenerales');

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    foreach($_POST as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }
    
    $consulta_token = query('SELECT token_pass_id from tr_token_pass tkp where token ='.$token.' and tkp.estatus=1');

        if(num($consulta_token)){
            $arreglo = arreglo($consulta_token);
            $baja_token=update('tr_token_pass','estatus=0','token_pass_id='.$arreglo['token_pass_id']);

            $pass_cifrado= password_hash("$pass", PASSWORD_BCRYPT, array('cost'=>12));

            $edita = update('usuarios',
            'password = "'.$pass_cifrado.'"',
            'usuario_id = '.$usuario_id);

            //ingreso todas las escuelas que tiene este profesor       
            if($edita){
              $json = array("status" => 1, "msg" => "Se actualizó el usuario correctamente");
            }else{
              $json = array("status" => 0, "msg" => "No se logró actualizar");
            }

        }else {
            $baja_token=0;
            $json = array("status" => 0, "msg" => "El token no es válido");
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
