<?php
include '../../jwt.php';
include '../../headers.php';

try {

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }
        include '../../administrador/personas/class/personas.class.php';
        $personasClass = new Personas();
        $pass = $personasClass->encriptaPassword($pass);
        echo $pass;
    } else {
        $json = array("estatus" => 0, "msg" => "Método no aceptado");
    }
    echo json_encode($json);
} catch (Exception $e) {
    $json = array("estatus" => 0, "msg" =>  $e->getMessage());
    echo json_encode($json);
}
