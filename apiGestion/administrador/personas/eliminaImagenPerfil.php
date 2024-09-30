<?php
include '../../jwt.php';
include '../../headers.php';

try {

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        // $usuario = Auth::GetData(
        //     $jwt  
        // );
        include './class/personas.class.php';
        $personaClass = new Personas();
        $persona = $personaClass->consultaEspPersona($usuarioId);
        $eliminaUrl = update('usuario', 'url_foto = NULL', 'usuario_id='.$usuarioId);
        unlink('../../../'.$persona['url_foto']);
        if($eliminaUrl){
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