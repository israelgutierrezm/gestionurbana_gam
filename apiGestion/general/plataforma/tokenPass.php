<?php
include '../../jwt.php';
include '../../headers.php';
include '../../extras/correo/class/correo.class.php';

try {

    db('datosGenerales');
    $correo=new Correo();

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    foreach($_POST as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }     

    //   $usuario = Auth::GetData(
    //         $jwt  
    //     );

        
        $token=rand(1000,9999);   

        $arreglo_correo=arreglo(query('SELECT persona_id from personas where email="'.$email.'"'));
        if(isset($arreglo_correo['persona_id'])){

            $inserta_token=inserta('tr_token_pass','token, persona_id, estatus',
            ''.$token.', '.$arreglo_correo['persona_id'].', 1');

            $usuario = new stdClass();


            if($inserta_token){
                $email_correccion=$correo::reestablecePass($arreglo_correo['persona_id'],$token);
            }

        }else{
            $email_correccion=0;
        }


       
       //ingreso todas las escuelas que tiene este profesor
       
       if($email_correccion){
    		$json = array("status" => 1, "msg" => "Se genero token correctamente");
    	 }else{
    		$json = array("status" => 0, "msg" => "Correo no existe");
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
