<?php
include '../jwt.php';
include '../headers.php';

try {
  if (isset($_POST['id_u']) && isset($_POST['id_r'])) {
    $id_u = $_POST['id_u'];
    $id_rol = $_POST['id_r'];

    $query_usuario = query('SELECT
    u.usuario_id, nombre, primer_apellido,segundo_apellido, p.persona_id,
    cr.rol_id, cr.rol,color,p.email,celular,usuario,url_perfil,cr.tiempo_sesion
    FROM usuarios u
    JOIN inter_persona_usuario_rol ur on ur.usuario_id = u.usuario_id 
    JOIN personas p on p.persona_id = ur.persona_id
    JOIN cat_rol cr ON cr.rol_id = ur.rol_id
    where u.usuario_id = "' . $id_u . '" and ur.rol_id = ' . $id_rol . ' 
     and u.estatus = 1');

    if (num($query_usuario)) {

      $arreglo_usuario = arreglo($query_usuario);

      if ($arreglo_usuario['rol_id'] == 1) {
        $query_administrador = query('select administrador_id,clave_administrador,situacion_administrador_id, vista,edicion,area_id from ' . $GLOBALS['db_controlEscolar'] . '.tr_administrador where administrador_id = ' . $arreglo_usuario['persona_id']);
        $arreglo_administrador = arreglo($query_administrador);
        $arreglo_usuario['administrador'] = $arreglo_administrador;
      }

      if($arreglo_usuario['rol_id'] == 2){
        $query_alumno = query('select alumno_id,clave_alumno,situacion_alumno_id,situacion_pago_id from '.$GLOBALS['db_controlEscolar'].'.tr_alumno where alumno_id = '.$arreglo_usuario['persona_id']);
        $arreglo_alumno = arreglo($query_alumno);
        $arreglo_usuario['alumno'] = $arreglo_alumno;
      }

      if ($arreglo_usuario['rol_id'] == 3) {
        $query_docente = query('select docente_id,clave_profesor,situacion_docente_id,activa_chat,edicion_contenido from ' . $GLOBALS['db_controlEscolar'] . '.tr_docente where docente_id = ' . $arreglo_usuario['persona_id'] . '');
        $arreglo_docente = arreglo($query_docente);
        $arreglo_usuario['docente'] = $arreglo_docente;
      }

      $arreglo_usuario['externo'] = 0;

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
