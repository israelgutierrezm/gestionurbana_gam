<?php
include '../jwt.php';
include '../headers.php';


try {
  if (isset($_POST['clave']) && isset($_POST['rol']) && isset($_POST['externo'])) {


    $clave = $_POST['clave'];
    $rol = $_POST['rol'];
    $externo = $_POST['externo'];

    if ($rol == 2) {
      $query_usuario = query('SELECT  nombre, primer_apellido,segundo_apellido, p.persona_id,curp,
      cr.rol_id, cr.rol, u.usuario_id,color,p.email,celular,usuario,url_perfil,cr.tiempo_sesion
      FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_alumno ta
      JOIN personas p on p.persona_id = ta.alumno_id
      JOIN inter_persona_usuario_rol ur on ur.persona_id = p.persona_id
      join usuarios u on u.usuario_id = ur.usuario_id
      JOIN cat_rol cr ON cr.rol_id = ur.rol_id
      where ta.clave_alumno= "' . $clave . '" and ur.rol_id = ' . $rol . ' and u.estatus = 1');

      $res = num($query_usuario);
    } else if ($rol == 3) {
      $query_usuario = query('SELECT nombre, primer_apellido,segundo_apellido, p.persona_id,curp,
        cr.rol_id, cr.rol, u.usuario_id,color,p.email,celular,usuario,url_perfil,cr.tiempo_sesion
        FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_docente ta
        JOIN personas p on p.persona_id = ta.docente_id 
        JOIN inter_persona_usuario_rol ur on ur.persona_id = p.persona_id
              join usuarios u on u.usuario_id = ur.usuario_id
        JOIN cat_rol cr ON cr.rol_id = ur.rol_id
        where ta.clave_profesor = "' . $clave . '" and ur.rol_id = ' . $rol . '  and u.estatus = 1 and ta.estatus=1');

      $res = num($query_usuario);
    } else if ($rol == 1) {
      $query_usuario = query('SELECT nombre, primer_apellido,segundo_apellido, p.persona_id,curp,
          cr.rol_id, cr.rol, u.usuario_id,color,p.email,celular,usuario,url_perfil,cr.tiempo_sesion,vista,edicion,area_id
          FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_administrador ta
          JOIN personas p on p.persona_id = ta.administrador_id 
          JOIN inter_persona_usuario_rol ur on ur.persona_id = p.persona_id
                join usuarios u on u.usuario_id = ur.usuario_id
          JOIN cat_rol cr ON cr.rol_id = ur.rol_id
          where ta.clave_administrador = "' . $clave . '" and ur.rol_id = ' . $rol . '  and u.estatus = 1');

      $res = num($query_usuario);
    } else {
      $res  = 0;
    }

    if ($res) {
      $arreglo_usuario = arreglo($query_usuario);
      $arreglo_usuario['externo'] = $externo;

      if($arreglo_usuario['rol_id'] == 2){
        $query_alumno = query('select alumno_id,clave_alumno,situacion_alumno_id,situacion_pago_id from '.$GLOBALS['db_controlEscolar'].'.tr_alumno where clave_alumno = "'.$clave.'"');
        $arreglo_alumno = arreglo($query_alumno);
        $arreglo_usuario['alumno'] = $arreglo_alumno;
      }

      if ($arreglo_usuario['rol_id'] == 3) {
        $query_docente = query('select docente_id,clave_profesor,situacion_docente_id,activa_chat,edicion_contenido from ' . $GLOBALS['db_controlEscolar'] . '.tr_docente where clave_profesor = "' . $clave . '" and estatus = 1');
        $arreglo_docente = arreglo($query_docente);
        $arreglo_usuario['docente'] = $arreglo_docente;
      }

      $jwt = Auth::SignIn($arreglo_usuario);
      $usuario = Auth::GetData($jwt);

      $json = array("status" => 1, "jwt" => $jwt, "user" => $arreglo_usuario);
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