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
        guardaImagenPerfil($usuarioId);
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

function guardaImagenPerfil($usuarioId){
    include '../../extras/archivo/class/archivo.class.php';
    $archivo = new Archivo();
    $imagen = $archivo::guardar_archivo_main(
        'imagen_perfil',
        $usuarioId,
        $_FILES["imagen"],//la variable tipo file donde viene el archivo
        "perfil", //el nombre de la tabla
        1,
        null,//tamaño de la extension
        'archivos_usuario',//carpeta propietario
        1 //archivo propietario
      ); 

        
          if($imagen['status'] == 1 ){
            update('usuarios','url_perfil ="'.$imagen['url'].'"','usuario_id ='.$usuarioId);

          $json = array("status" => 1, "msg" => "Se inserto la imagen correctamente", "url" => $imagen['url']);
         }else{
          $json = array("status" => 0, "msg" => "No se logró insertar la imagen");
         }

}