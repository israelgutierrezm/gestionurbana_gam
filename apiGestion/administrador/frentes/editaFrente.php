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
        include './class.php/frente.class.php';
        $frenteClass = new Frentes();
        $datosFrente = [
            'usuarioId' => $usuarioId,
            'rolId' => $rolId,
            'nombre' => $nombre,
            'apellidoPaterno' => $apellidoPaterno];

        $editaFrente = $frenteClass->editaFrente($datosFrente);
        if($editaFrente){
            $json = array("estatus" => 1, "msg" => "Información actualizada correctamente");
        }
    } else {
        $json = array("estatus" => 0, "msg" => "Método no aceptado");
    }
    echo json_encode($json);
} catch (Exception $e) {
    $json = array("estatus" => 0, "msg" =>  $e->getMessage());
    echo json_encode($json);
}