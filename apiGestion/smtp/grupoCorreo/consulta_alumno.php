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

    
    $query_grupo_correo = query('SELECT grupo_correo_id, institucion_id,
    p.nombre, p.primer_apellido, p.segundo_apellido, p.curp, p.email
    FROM tr_grupo_correo gc
    join '.$GLOBALS["db_datosGenerales"].'.personas p on p.persona_id = gc.persona_id
    where grupo_correo_id = "'.$id_correo_grupo.'" and gc.estatus = 1');

    
       $json_grupo_correo = array();
       while ($arreglo_grupo_correo = arreglo($query_grupo_correo)){
          array_push($json_grupo_correo,$arreglo_grupo_correo);
       }
       
       //ingreso todas las escuelas que tiene este profesor
       
       if(num($query_grupo_correo)){
    		$json = array("status" => 1, "msg" => "Se encontraron alumnos","alumnos" => $json_grupo_correo);
    	 }else{
    		$json = array("status" => 0, "msg" => "No se encontró alumnos");
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
