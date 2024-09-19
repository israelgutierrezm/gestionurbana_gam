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


    $query_reportes = query('SELECT clave,nombre FROM cat_reporte where estatus = 1');

    $json_reportes = array();
    while($arreglo_reportes = arreglo($query_reportes)){
      array_push($json_reportes,$arreglo_reportes);
    }
  

    if(num($query_reportes) > 0 ){
      $json = array("status" => 1, "msg" => "Tipos de reportes","reportes" => $json_reportes);
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