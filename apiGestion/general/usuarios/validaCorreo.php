<?php
include '../../headers.php';
include '../../jwt.php';

try {

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }
        // $usuario = $_POST['usuario'];
        $json = validaEmail($email);
    } else {
        $json = array("estatus" => 0, "msg" => "Método no aceptado");
    }
    echo json_encode($json);
} catch (Exception $e) {
    $json = array("estatus" => 0, "msg" =>  $e->getMessage());
    echo json_encode($json);
}

function validaEmail($email)
{
    $queryUsuario = query('SELECT email FROM usuario WHERE email ="'.$email.'"');
    if (num($queryUsuario)) {
        return array("estatus" => 1, "msg" => "El email ya ha sido registrado");
    } else {
        return array("estatus" => 0, "msg" => "No se encontró email");
    }
}