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

    $queryCatalogo = query('SELECT cat_tipo_sangre_id, tipo_sangre FROM cat_tipo_sangre cts WHERE estatus = 1');

    while($tipoSangre = arreglo($queryCatalogo)){
        $arregloTipoSangre[] = $tipoSangre;
    }
  
    if(num($queryCatalogo) > 0 ){
      $json = array("estatus" => 1, "msg" => "Se encontró información", "catalogo" => $arregloTipoSangre);
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