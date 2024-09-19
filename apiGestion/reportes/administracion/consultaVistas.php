<?php
include '../../jwt.php';
include '../../headers.php';

try {
  
  db('reporte');

  if($_SERVER['REQUEST_METHOD'] == "GET"){
    foreach($_GET as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }
    
    $usuario = Auth::GetData(
        $jwt  
    );


    $query_vistas = query('select tv.vista_id, tv.nombre,tv.descripcion, tv.vista_db,url_api,tv.objeto_id as nivel from cat_vista tv
    join inter_vista_objeto ivo on tv.vista_id = ivo.vista_id
    where ivo.objeto_id = '.$id_objeto);


    $json_vistas = array();
    while($arreglo_vistas = arreglo($query_vistas)){
      array_push($json_vistas,$arreglo_vistas);
    }
    if(num($query_vistas) > 0 ){
      $json = array("status" => 1, "msg" => "Tipos de vistas","vistas" => $json_vistas);
     }else{
      $json = array("status" => 0, "msg" => "No se encontró información");
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