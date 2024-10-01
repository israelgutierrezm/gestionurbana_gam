<?php
include '../jwt.php';
include '../headers.php';

try {

  if($_SERVER['REQUEST_METHOD'] == "GET"){
    foreach($_GET as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }
    
    // $usuario = Auth::GetData(
    //     $jwt  
    // );

    $queryCatalogo = query('SELECT estado_civil_id, estado_civil FROM cat_estado_civil WHERE estatus = 1');

    while($estado = arreglo($queryCatalogo)){
        $arregloCatalogo[] = $estado;
    }
  
    if(num($queryCatalogo) > 0 ){
      $json = array("estatus" => 1, "msg" => "Se encontró información", "catalogo" => $arregloCatalogo);
     }else{
      $json = array("estatus" => 0, "msg" => "No se encontró información");
     }

}else{
    $json = array("estatus" => 0, "msg" => "Método no aceptado");
}
echo json_encode($json);
} catch (Exception $e) {
  $json = array("estatus" => 0, "msg" =>  $e->getMessage());
  echo json_encode($json);
}
