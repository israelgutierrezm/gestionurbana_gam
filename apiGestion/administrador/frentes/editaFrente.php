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

        include './class.php/frentes.class.php';
        $frentesClass = new Frentes();
        $datosFrente = [
            'frenteId' => $frenteId,
            'cat_direccion_territorial_id' => $cat_direccion_territorial_id,
            'area' => $area,
            'cat_colonia_id' => $cat_colonia_id,
            'nombre' => $nombre,
            'dias_jornada' => $dias_jornada,
            'personal_necesario' => $personal_necesario,
        ];
        $editaFrente = $frentesClass->editaFrente($datosFrente);
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