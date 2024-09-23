<?php
// include '../../../jwt.php';

class Personas
{
    public function consultaGeneralPersonas()
    {
        $queryPersonas = query('SELECT usuario_id, usuario, nombre, CONCAT(ap_pat, " ", ap_mat) AS apellidos, curp from usuario u where estatus = 1');
        while ($persona = arreglo($queryPersonas)) {
            $arregloPersonas[] = $persona;
        }
        return $arregloPersonas;
    }

    public function consultaEspPersona($usuarioId)
    {
        $persona = arreglo(query('SELECT u.usuario_id, u.nombre, u.ap_pat, u.ap_mat, u.curp, u.email, u.telefono, u.celular, u.fecha_nacimiento, u.cat_genero_id,
        ur.cat_rol_id, ude.nombre AS nombre_contacto, ude.apellido_paterno AS apellido_contacto, ude.telefono AS telefono_contacto,
        ude.celular AS celular_contacto, ude.parentesco, udm.tipo_sangre, udm.alergias, udm.medicamentos, udm.condiciones_preexistentes
        FROM usuario u 
        JOIN usuario_rol ur on ur.usuario_id = u.usuario_id
        JOIN usuario_datos_emergencia ude on ude.usuario_id = u.usuario_id
        JOIN usuario_datos_medicos udm on udm.usuario_id = u.usuario_id
        WHERE u.estatus = 1 AND ur.estatus = 1 AND u.usuario_id =' . $usuarioId));
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

    public function eliminaPersona($usuarioId)
    {
        $edita = update('', '', '');
        return $edita;
    }
}
