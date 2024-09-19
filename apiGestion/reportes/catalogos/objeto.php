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


    $query_objetos = query('SELECT clave,nombre,tabla,base FROM cat_objeto where estatus = 1');

    $json_objetos = array();
    while($arreglo_objetos = arreglo($query_objetos)){
      array_push($json_objetos,$arreglo_objetos);
    }
  

    if(num($query_objetos) > 0 ){
      $json = array("status" => 1, "msg" => "Tipos de objetos","objetos" => $json_objetos);
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