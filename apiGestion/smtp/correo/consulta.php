<?php
include '../../jwt.php';
include '../../headers.php';

try {
  
  db('SMTP');

  if($_SERVER['REQUEST_METHOD'] == "GET"){
    foreach($_GET as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }

      $usuario = Auth::GetData(
            $jwt  
        );

    //SERVICIO DE CONSULTA DE SESIÓN
    $query_correo = query('SELECT *
     FROM tr_correo WHERE correo_id = '.$id_correo);

    
       $json_correo= array();
       while ($arreglo_correo = arreglo($query_correo)){
          array_push($json_correo,$arreglo_correo);
       }
       
       //ingreso todas las escuelas que tiene este profesor
       
       if(num($query_correo)){
    		$json = array("status" => 1, "msg" => "Se encontraron correos","correo" => $json_correo);
    	 }else{
    		$json = array("status" => 0, "msg" => "No se encontraron los correos");
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
