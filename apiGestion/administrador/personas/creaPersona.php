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
        
        $respuestaGuardaPersona = insertaUsuario(
            $rolId,
            $nombre,
            $apellidoPaterno,
            $apellidoMaterno,
            $curp,
            $generoId,
            $fechaNacimiento,
            $numeroTelefono,
            $numeroCelular,
            $email,
            $pass,
            $nombreContacto,
            $apellidoContacto,
            $parentescoContacto,
            $numeroContacto,
            $enfermedades,
            $alergias,
            $medicamentos,
            $tipoSangre
        );

        $json = $respuestaGuardaPersona;
    } else {
        $json = array("estatus" => 0, "msg" => "Método no aceptado");
    }
    echo json_encode($json);
} catch (Exception $e) {
    $json = array("estatus" => 0, "msg" =>  $e->getMessage());
    echo json_encode($json);
}

function insertaUsuario(
    $rolId,
    $nombre,
    $apellidoPaterno,
    $apellidoMaterno,
    $curp,
    $generoId,
    $fechaNacimiento,
    $numeroTelefono,
    $numeroCelular,
    $email,
    $pass,
    $nombreContacto,
    $apellidoContacto,
    $parentescoContacto,
    $numeroContacto,
    $enfermedades,
    $alergias,
    $medicamentos,
    $tipoSangre
) {
    $usuarioId = inserta_last_id('usuario', '
    usuario,
    contraseña,
    nombre,
    ap_pat,
    ap_mat, 
    curp,
    email,
    telefono,
    celular,
    fecha_nacimiento,
    url_foto,
    cat_genero_id,
    fecha_creacion,
    estatus', '
    "' . $email . '",
    "'.encriptaPassword($pass).'",
    "' . $nombre . '",
    "' . $apellidoPaterno . '",
    "' . $apellidoMaterno . '",
    "' . $curp . '",
    "' . $email . '",
    ' . $numeroTelefono . ',
    ' . $numeroCelular . ',
    "' . $fechaNacimiento . '",
    "/url",
    ' . $generoId . ',
    NOW(),
    1');
    if ($usuarioId) {
        $insertaUsuarioRol = inserta('usuario_rol','usuario_id, cat_rol_id, estatus',''.$usuarioId.', '.$rolId.',1'); 
        if($rolId == 1)
        $insertaTrabajador = inserta(
            'tr_administrador', 
            'usuario_id, clave_administrador, estatus', 
            $usuarioId . ', "A' . $usuarioId . '", 1'
        );  
        if($rolId == 2)
            $insertaTrabajador = inserta(
                'tr_trabajador', 
                'usuario_id, clave_trabajador, estatus', 
                $usuarioId . ', "T' . $usuarioId . '", 1'
            );   
        if($rolId == 3)     
            $insertaTrabajador = inserta(
                'tr_supervisor', 
                'usuario_id, clave_supervisor, estatus', 
                $usuarioId . ', "S' . $usuarioId . '", 1'
            );   
        $responseInsertaDatosMedicos = insertaDatosMedicos($usuarioId, $enfermedades, $alergias, $medicamentos, $tipoSangre);
        if ($responseInsertaDatosMedicos) {
            $responseInsertaDatosEmergencia = insertaDatosEmergencia($usuarioId, $nombreContacto, $apellidoContacto, $parentescoContacto, $numeroContacto);
            return $responseInsertaDatosEmergencia;
        } else {
            return array("estatus" => 0, "msg" => "Error al guardar los datos médicos");
        }
    } else {
        return array("estatus" => 0, "msg" => "Error al guardar los datos del usuario");
    }
}

function insertaDatosMedicos($usuarioId, $enfermedades, $alergias, $medicamentos, $tipoSangre)
{
    $insertaDatosMedicos = inserta_last_id(
        'usuario_datos_medicos',
        'usuario_id,
        tipo_sangre,
        alergias,
        medicamentos,
        condiciones_preexistentes',
        ''.$usuarioId.',
        '.$tipoSangre.',
        "'.$alergias.'",
        "'.$medicamentos.'",
        "'.$enfermedades.'"'
    );
    return $insertaDatosMedicos;
}


function insertaDatosEmergencia($usuarioId, $nombreContacto, $apellidoContacto, $parentescoContacto, $numeroContacto)
{
    $insertaDatosEmergencia = inserta_last_id('usuario_datos_emergencia',
     'usuario_id,
     nombre,
     apellido_paterno,
     telefono,
     parentesco',
     ''.$usuarioId.',
     "'.$nombreContacto.'",
     "'.$apellidoContacto.'",
     '.$numeroContacto.',
     "'.$parentescoContacto.'"');
    if ($insertaDatosEmergencia) {
        return array("estatus" => 1, "msg" => "Se guardó la información correctamente.");
    } else {
        return array("estatus" => 0, "msg" => "Error al guardar los datos del contacto de emergencia.");
    }
}

function encriptaPassword($pass){
    include '../../extras/encriptacion/class/encriptacion.class.php';
    $encriptacionClass = new Encriptacion();
    $encriptada = $encriptacionClass->hash($pass);
    return $encriptada;
}
