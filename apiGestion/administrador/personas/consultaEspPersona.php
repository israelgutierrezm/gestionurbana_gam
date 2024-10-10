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
        $arregloUsuario = $personasClass->consultaEspPersona($usuarioId);
        if($arregloUsuario && sizeof($arregloUsuario) > 0){
            $json = array("estatus" => 1, "msg" => "Se encontró información", "usuario" => $arregloUsuario);
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
