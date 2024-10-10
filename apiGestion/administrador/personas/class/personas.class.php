<?php
// include '../../../jwt.php';

class Personas
{
    public function consultaGeneralPersonas()
    {
        $queryPersonas = query('SELECT usuario_id, usuario, nombre, CONCAT(ap_pat, " ", ap_mat) AS apellidos, curp FROM usuario u WHERE estatus = 1');
        while ($persona = arreglo($queryPersonas)) {
            $arregloPersonas[] = $persona;
        }
        return $arregloPersonas;
    }

    public function consultaPersonasRol($rolId)
    {
        $queryPersonas = query('SELECT u.usuario_id, u.usuario, u.nombre, CONCAT(u.ap_pat, " ", u.ap_mat) AS apellidos, u.curp, ur.cat_rol_id
        FROM usuario u
        JOIN usuario_rol ur On ur.usuario_id = u.usuario_id
        WHERE u.estatus = 1 AND ur.estatus = 1 AND ur.cat_rol_id =' . $rolId);
        while ($persona = arreglo($queryPersonas)) {
            $arregloPersonas[] = $persona;
        }
        return $arregloPersonas;
    }

    public function consultaEspPersona($usuarioId)
    {
        $persona = arreglo(query('SELECT u.usuario_id, u.nombre, u.ap_pat, u.ap_mat, u.curp, u.email, u.telefono, u.celular, u.fecha_nacimiento, u.cat_genero_id,
        ur.cat_rol_id, ude.nombre AS nombre_contacto, ude.apellido_paterno AS apellido_contacto, ude.telefono AS telefono_contacto,
        ude.celular AS celular_contacto, ude.parentesco, udm.tipo_sangre, udm.alergias, udm.medicamentos, udm.condiciones_preexistentes, u.url_foto, udm.seguro_social,
        udm.complexion_id, udm.estatura, u.estado_civil_id, u.oficio, udm.tipo_seguro_id, udm.numero_seguro, u.oficio, u.area, u.funcion, u.direccion_id
        FROM usuario u 
        JOIN usuario_rol ur on ur.usuario_id = u.usuario_id
        JOIN usuario_datos_emergencia ude on ude.usuario_id = u.usuario_id
        JOIN usuario_datos_medicos udm on udm.usuario_id = u.usuario_id
        WHERE u.estatus = 1 AND ur.estatus = 1 AND u.usuario_id =' . $usuarioId.''));
        return $persona;
    }

    public function editaPersona($datosUsuario)
    {
        $editaUsuario = update(
            'usuario',
            'nombre = "' . $datosUsuario['nombre'] . '",
            ap_pat = "' . $datosUsuario['apellidoPaterno'] . '",
            ap_mat = "' . $datosUsuario['apellidoMaterno'] . '",
            curp = "' . $datosUsuario['curp'] . '",
            cat_genero_id = "' . $datosUsuario['generoId'] . '",
            fecha_nacimiento = "' . $datosUsuario['fechaNacimiento'] . '",
            oficio = "' . $datosUsuario['oficio'] . '",
            area = "' . $datosUsuario['area'] . '",
            funcion = "' . $datosUsuario['funcion'] . '",
            direccion_id = "' . $datosUsuario['direccionId'] . '",
            estado_civil_id = "' . $datosUsuario['edoCivil'] . '",
            telefono = "' . $datosUsuario['numeroTelefono'] . '",
            celular = "' . $datosUsuario['numeroCelular'] . '",
            email = "' . $datosUsuario['email'] . '"',
            'usuario_id =' . $datosUsuario['usuarioId']
        );

        $editaUsuarioRol = update('usuario_rol', 'cat_rol_id = "' . $datosUsuario['rolId'] . '"',  'usuario_id =' . $datosUsuario['usuarioId']);

        $editaInfoMedica = update(
            'usuario_datos_medicos',
            'condiciones_preexistentes = "' . $datosUsuario['enfermedades'] . '",
            alergias = "' . $datosUsuario['alergias'] . '",
            medicamentos = "' . $datosUsuario['medicamentos'] . '",
            estatura = ' . $datosUsuario['estatura'] . ',
            complexion_id = ' . $datosUsuario['complexion'] . ',
            seguro_social = ' . $datosUsuario['sSocial'] . ',
            tipo_seguro_id = ' . $datosUsuario['tipoSeguro'] . ',
            numero_seguro = "' . $datosUsuario['numeroSeguro'] . '",
            tipo_sangre = "' . $datosUsuario['tipoSangre'] . '"',
            'usuario_id =' . $datosUsuario['usuarioId']
        );

        $editaInfoEmergencia = update(
            'usuario_datos_emergencia',
            'nombre = "' . $datosUsuario['nombreContacto'] . '",
            apellido_paterno = "' . $datosUsuario['apellidoContacto'] . '",
            parentesco = "' . $datosUsuario['parentescoContacto'] . '",
            telefono = "' . $datosUsuario['numeroContacto'] . '"',
            'usuario_id =' . $datosUsuario['usuarioId']
        );
        return $editaUsuario;
    }

    public function creaPersona($datosUsuario)
    {
        $usuarioId = inserta_last_id('usuario', '
        usuario,
        contrasena,
        nombre,
        ap_pat,
        ap_mat, 
        curp,
        email,
        telefono,
        celular,
        fecha_nacimiento,
        oficio,
        area,
        direccion_id,
        funcion,
        estado_civil_id,
        url_foto,
        cat_genero_id,
        fecha_creacion,
        estatus', '
        "' . $datosUsuario['email'] . '",
        "password",
        "' . $datosUsuario['nombre'] . '",
        "' . $datosUsuario['apellidoPaterno'] . '",
        "' . $datosUsuario['apellidoMaterno'] . '",
        "' . $datosUsuario['curp'] . '",
        "' . $datosUsuario['email'] . '",
        ' . $datosUsuario['numeroTelefono'] . ',
        ' . $datosUsuario['numeroCelular'] . ',
        "' . $datosUsuario['fechaNacimiento'] . '",
        "' . $datosUsuario['oficio'] . '",
        "' . $datosUsuario['area'] . '",
        "' . $datosUsuario['direccionId'] . '",
        "' . $datosUsuario['funcion'] . '",
        "' . $datosUsuario['edoCivil'] . '",
        "",
        ' . $datosUsuario['generoId'] . ',
        NOW(),
        1');
        if ($usuarioId) {
            $insertaUsuarioRol = inserta('usuario_rol', 'usuario_id, cat_rol_id, estatus', '' . $usuarioId . ', ' . $datosUsuario['rolId'] . ',1');
            if($datosUsuario['rolId'] == 1){
                $clave = 'T'.$usuarioId;

                $insertaTrabajador = inserta(
                    'tr_administrador', 
                    'usuario_id, clave_administrador, estatus', 
                    $usuarioId . ', "' . $clave . '", 1'
                );
            }
            if($datosUsuario['rolId'] == 2){
                $clave = 'T'.$usuarioId;
                $insertaTrabajador = inserta(
                    'tr_trabajador', 
                    'usuario_id, clave_trabajador, fecha_ingreso, estatus', 
                    $usuarioId . ', "' . $clave . '", NOW(), 1'
                );

            }
            if($datosUsuario['rolId'] == 3){
                $clave = 'S'.$usuarioId;
                $insertaTrabajador = inserta(
                    'tr_supervisor', 
                    'usuario_id, clave_supervisor, estatus', 
                    $usuarioId . ', "' . $clave . '", 1'
                );
            }

            if($datosUsuario['email'] == 'NULL'){
                $this->editaUsuarioSinCorreo($usuarioId, $clave);
            }
            $responseInsertaDatosMedicos = $this->insertaDatosMedicos($usuarioId, $datosUsuario);
            if ($responseInsertaDatosMedicos) {
                $responseInsertaDatosEmergencia = $this->insertaDatosEmergencia($usuarioId, $datosUsuario);
                //return $responseInsertaDatosEmergencia;
                return array("estatus" => 1, "msg" => "Se guardó la información correctamente.", "usuarioId"=> $usuarioId);

            } else {
                return array("estatus" => 0, "msg" => "Error al guardar los datos médicos");
            }
        } else {
            return array("estatus" => 0, "msg" => "Error al guardar los datos del usuario");
        }
    }

    private function editaUsuarioSinCorreo($usuario_id, $clave){
        $edita = update('usuario','usuario="'.$clave.'"','usuario_id='.$usuario_id);
    }

    private function insertaDatosMedicos($usuarioId, $datosUsuario)
    {
        $insertaDatosMedicos = inserta_last_id(
            'usuario_datos_medicos',
            'usuario_id,
            tipo_sangre,
            alergias,
            medicamentos,
            estatura,
            complexion_id,
            seguro_social,
            tipo_seguro_id,
            numero_seguro,
            condiciones_preexistentes',
            '' . $usuarioId . ',
            ' . $datosUsuario['tipoSangre'] . ',
            "' . $datosUsuario['alergias'] . '",
            "' . $datosUsuario['medicamentos'] . '",
            "' . $datosUsuario['estatura'] . '",
            ' . $datosUsuario['complexion'] . ',
            ' . $datosUsuario['sSocial'] . ',
            ' . $datosUsuario['tipoSeguro'] . ',
            "' . $datosUsuario['numeroSeguro'] . '",
            "' . $datosUsuario['enfermedades'] . '"'
        );
        return $insertaDatosMedicos;
    }

    private function insertaDatosEmergencia($usuarioId, $datosUsuario)
    {
        $insertaDatosEmergencia = inserta_last_id(
        'usuario_datos_emergencia',
        'usuario_id,
         nombre,
         apellido_paterno,
         telefono,
         parentesco',
            '' . $usuarioId . ',
         "' . $datosUsuario['nombreContacto'] . '",
         "' . $datosUsuario['apellidoContacto'] . '",
         ' . $datosUsuario['numeroContacto'] . ',
         "' . $datosUsuario['parentescoContacto'] . '"'
        );
        if ($insertaDatosEmergencia) {
            return array("estatus" => 1, "msg" => "Se guardó la información correctamente.", "usuarioId"=> $usuarioId);
        } else {
            return array("estatus" => 0, "msg" => "Error al guardar los datos del contacto de emergencia.");
        }
    }

    private function encriptaPassword($pass)
    {
        include '../../extras/encriptacion/class/encriptacion.class.php';
        $encriptacionClass = new Encriptacion();
        $encriptada = $encriptacionClass->hash($pass);
        return $encriptada;
    }
}
