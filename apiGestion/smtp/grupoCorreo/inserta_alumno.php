<?php
include '../../jwt.php';
include '../../headers.php';

db ('SMTP');

try {

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    
    $jwt=$_POST["jwt"];

    $id_persona=$_POST["id_persona"];
    
    $id_grupo_correo=$_POST["id_grupo_correo"];

    
  

        
      $usuario = Auth::GetData(
            $jwt  

        );


    $persona_alt = "INSERT INTO tr_grupo_correo (grupo_correo_id, institucion_id, persona_id,
    fecha_creacion,usuario_crea, estatus)
     VALUES";


    foreach ($id_persona as $persona_id_tmp) {
    
    
    $persona_alt .='("'.$id_grupo_correo.'",'.$usuario->id_institucion.','.$persona_id_tmp.',
     now(),current_user(),1),';
      }
    
       $persona_alt = trim($persona_alt, ',');
       //ingreso todas las escuelas que tiene este profesor

      // $inserta=inserta('inter_rol_privilegio','rol_id,privilegio_id',''.$rol_id.','.$privilegio_id_tmp.'');      

      $inserta=query($persona_alt);

       if($inserta){
    		$json = array("status" => 1, "msg" => "Se insertó el grupo correctamente");
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