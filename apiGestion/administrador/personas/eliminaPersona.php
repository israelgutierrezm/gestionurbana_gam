<?php
include '../../jwt.php';
include '../../headers.php';

try {

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        foreach ($_POST as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        // $usuario = Auth::GetData(
        //     $jwt  
        // );

        $eliminaUsuario = update('usuario', 'estatus = 0', 'usuario_id='.$usuarioId);
        $eliminaRolUsuario = update('usuario_rol', 'estatus = 0', 'usuario_id='.$usuarioId);

        if($eliminaUsuario){
            $json = array("estatus" => 1, "msg" => "Se eliminó correctamente");
        }
    } else {
        $json = array("estatus" => 0, "msg" => "Método no aceptado");
    }
    echo json_encode($json);
} catch (Exception $e) {
    $json = array("estatus" => 0, "msg" =>  $e->getMessage());
    echo json_encode($json);
}