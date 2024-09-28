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
        include './class/personas.class.php';
        $personaClass = new Personas();
        $datosUsuario = [
            'usuarioId' => $usuarioId,
            'rolId' => $rolId,
            'nombre' => $nombre,
            'apellidoPaterno' => $apellidoPaterno,
            'apellidoMaterno' => $apellidoMaterno,
            'curp' => $curp,
            'generoId' => $generoId,
            'fechaNacimiento' => $fechaNacimiento,
            'numeroTelefono' => $numeroTelefono,
            'numeroCelular' => $numeroCelular,
            'email' => $email,
            'nombreContacto' => $nombreContacto,
            'apellidoContacto' => $apellidoContacto,
            'parentescoContacto' => $parentescoContacto,
            'numeroContacto' => $numeroContacto,
            'enfermedades' => $enfermedades,
            'alergias' => $alergias,
            'medicamentos' => $medicamentos,
            'tipoSangre' => $tipoSangre];

        $editaPersona = $personaClass->editaPersona($datosUsuario);
        if($editaPersona){
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