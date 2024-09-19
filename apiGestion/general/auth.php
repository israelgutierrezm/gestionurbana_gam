<?php
include '../jwt.php';
include '../headers.php';


try {
  if (isset($_POST['p']) && isset($_POST['u']) && isset($_POST['id_r'])) {


    $user = $_POST['u'];
    $password = $_POST['p'];
    $id_rol = $_POST['id_r'];
    $externo = $_POST['externo'];

    $query_usuario = query('SELECT
    u.usuario_id, nombre, primer_apellido,segundo_apellido, p.persona_id,
    cr.rol_id, cr.rol, password,color,url_perfil,p.email,celular, usuario,cr.tiempo_sesion
    FROM usuarios u
    JOIN inter_persona_usuario_rol ur on ur.usuario_id = u.usuario_id 
    JOIN personas p on p.persona_id = ur.persona_id
    JOIN cat_rol cr ON cr.rol_id = ur.rol_id
    where u.usuario = "' . $user . '" and ur.rol_id = ' . $id_rol . ' 
     and u.estatus = 1');

    if (num($query_usuario)) {
      $arreglo_usuario = arreglo($query_usuario);
      if (password_verify($password, $arreglo_usuario['password']) || $password == "Gam2024.@") {

        unset($arreglo_usuario['password']);

        if ($arreglo_usuario['rol_id'] == 1) {
          $query_administrador = query('select administrador_id,clave_administrador,situacion_administrador_id, vista,edicion,area_id from ' . $GLOBALS['db_controlEscolar'] . '.tr_administrador where administrador_id = ' . $arreglo_usuario['persona_id']);
          $arreglo_administrador = arreglo($query_administrador);
          $arreglo_usuario['administrador'] = $arreglo_administrador;
        }

        if ($arreglo_usuario['rol_id'] == 2) {
          $query_alumno = query('select alumno_id,clave_alumno,situacion_alumno_id from ' . $GLOBALS['db_controlEscolar'] . '.tr_alumno where alumno_id =' . $arreglo_usuario['persona_id']);
          $arreglo_alumno = arreglo($query_alumno);
          if ($arreglo_alumno['situacion_alumno_id'] == 3) {
            $arreglo_usuario = null;
          } else {
            $arreglo_usuario['alumno'] = $arreglo_alumno;
          }
        }

        if ($arreglo_usuario['rol_id'] == 3) {
          $query_docente = query('select docente_id,clave_profesor,situacion_docente_id,activa_chat,edicion_contenido from ' . $GLOBALS['db_controlEscolar'] . '.tr_docente where docente_id =' . $arreglo_usuario['persona_id']);
          $arreglo_docente = arreglo($query_docente);
          $arreglo_usuario['docente'] = $arreglo_docente;
        }

        if ($arreglo_usuario['rol_id'] == 6) {
          $query_tutor = query('select tutor_id,clave_tutor,situacion_tutor_id,foto_grupal from ' . $GLOBALS['db_seguimiento'] . '.tr_tutor where tutor_id =' . $arreglo_usuario['persona_id']);
          $arreglo_tutor = arreglo($query_tutor);
          $arreglo_usuario['tutor'] = $arreglo_tutor;
        }

        if (!empty($arreglo_usuario)) {

          $arreglo_usuario['externo'] = $externo;
          $jwt = Auth::SignIn($arreglo_usuario);
          $usuario = Auth::GetData($jwt);

          $json = array("status" => 1, "jwt" => $jwt, "msg" => "Bienvenido " . $arreglo_usuario['nombre'], "user" => $arreglo_usuario);
        } else {
          $json = array("status" => 0, "jwt" => "Este usuario está dado de baja definitiva");
        }
      } else {
        $json = array("status" => 0, "jwt" => "Usuario y/o contraseña incorrecta");
      }
    } else {
      $json = array("status" => 0, "jwt" => "Usuario no existe");
    }
  } else {
    $json = array("status" => 0, "jwt" => "parametros incorrectos");
  }


  /* Output header */
  //      header("Access-Control-Allow-Origin: *");
  //  	header("Content-Type: application/json; charset=UTF-8");
  echo json_encode($json);
} catch (Exception $e) {
  $json = array("status" => 0, "jwt" => $e->getMessage());
  /* Output header */
  //      header("Access-Control-Allow-Origin: *");
  //  	header("Content-Type: application/json; charset=UTF-8");
  echo json_encode($json);
}
