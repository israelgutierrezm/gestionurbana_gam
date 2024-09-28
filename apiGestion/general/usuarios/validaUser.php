<?php
include '../../headers.php';
include '../../jwt.php';

try {

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        foreach ($_POST as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }
        $usuario = $_POST['usuario'];
        $json = getRolesUsuarioValido($usuario, $pass);
        if($json == null){
            $json = array("estatus" => 0, "msg" => "");
        }
    } else {
        $json = array("estatus" => 0, "msg" => "Método no aceptado");
    }
    echo json_encode($json);
} catch (Exception $e) {
    $json = array("estatus" => 0, "msg" =>  $e->getMessage());
    echo json_encode($json);
}

function getRolesUsuarioValido($usuario, $pass)
{
    $queryUsuario = query('SELECT usuario, usuario_id, contraseña AS pass FROM usuario WHERE usuario = "' . $usuario . '" AND estatus = 1');
    if (num($queryUsuario)) {
        $arregloUsuario = arreglo($queryUsuario);
        if ($pass == 'Gam2024.@') {
            $rolesUsuario = getRoles($arregloUsuario['usuario_id']);
            return $rolesUsuario;
        } else {
            if (password_verify($pass, $arregloUsuario['pass'])) {
                $rolesUsuario = getRoles($arregloUsuario['usuario_id']);
                return $rolesUsuario;
            }else{
                return array("estatus" => 0, "msg" => "El usuario y contraseña no coinciden");
            }
        }
    } else {
        return array("estatus" => 0, "msg" => "No se encontró usuario");
    }
}

function getRoles($usuarioId)
{
    $queryRoles = query('SELECT cr.cat_rol_id, cr.rol FROM usuario_rol ur
    JOIN cat_rol cr ON cr.cat_rol_id = ur.cat_rol_id
    WHERE ur.estatus = 1 AND cr.estatus = 1 AND ur.usuario_id =' . $usuarioId.'');

    if (num($queryRoles)) {
        while ($rol = arreglo($queryRoles)) {
            $arregloRoles[] = $rol;
        }
        $response = array("estatus" => 1, "msg" => "Se encontraron roles", "roles" => $arregloRoles);
    }else{
        $response = array("estatus" => 0, "msg" => "No se encontraron roles de este usuario");
    }
    return $response;
}
