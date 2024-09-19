<?php
include '../../jwt.php';
include '../../headers.php';

try {
  
  db ('datosGenerales');

  if($_SERVER['REQUEST_METHOD'] == "GET"){
    foreach($_GET as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }

      $usuario = Auth::GetData(
            $jwt  
        );

      $query_fecha = query('(SELECT fecha_inicio from tr_bitacora_sesion where usuario_id = '.$id_usuario.' order by fecha_inicio asc limit 1) union all
      (select fecha_inicio as fecha_fin from tr_bitacora_sesion where usuario_id = '.$id_usuario.' order by fecha_inicio desc limit 1)');
    
      $json=array();
      $arreglo_inicio=arreglo($query_fecha);
      $arreglo_fin=arreglo($query_fecha);

      if($query_fecha){
      $json = array("status" => 1, "msg" => "Se encontraron fechas","primer_acceso" =>$arreglo_inicio['fecha_inicio'], "ultimo_acceso"=>$arreglo_fin['fecha_inicio']);
      }else{
      $json = array("status" => 0, "msg" => "No se encontraron Respuestas");
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