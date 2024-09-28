<?php
include '../headers.php';
include '../jwt.php';

try {

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        foreach ($_POST as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }
        // $usuarioId = $_POST['usuarioId'];
        // $pass = $_POST['pass'];

        $rolesUsuario = editaPass($usuarioId, $pass);

        if($rolesUsuario == null){
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

function editaPass($usuarioId, $p){
    include './encriptacion/class/encriptacion.class.php';
    $encriptacionClass = new Encriptacion();
    $encriptada = $encriptacionClass->hash($p);

    $edita = update('usuario','contraseña="'.$encriptada.'"','usuario_id = '.$usuarioId);
}