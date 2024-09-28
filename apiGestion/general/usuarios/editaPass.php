<?php
include '../../jwt.php';
include '../../headers.php';

try {

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }
        include './class/personas.class.php';
        $personasClass = new Personas();
        $arregloPersonas = $personasClass->consultaGeneralPersonas();
        if(sizeof($arregloPersonas) > 0){
            $json = array("estatus" => 1, "msg" => "Se encontraron personas", "personas" => $arregloPersonas);
        }else{
            $json = array("estatus" => 0, "msg" => "No se encontró información");
        }
    } else {
        $json = array("estatus" => 0, "msg" => "Método no aceptado");
    }
    echo json_encode($json);
} catch (Exception $e) {
    $json = array("estatus" => 0, "msg" =>  $e->getMessage());
    echo json_encode($json);
}
