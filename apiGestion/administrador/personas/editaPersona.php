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
            'oficio' => $oficio,
            'edoCivil' => $edoCivil,
            'numeroTelefono' => $numeroTelefono,
            'numeroCelular' => $numeroCelular,
            'email' => $email,
            'nombreContacto' => $nombreContacto,
            'apellidoContacto' => $apellidoContacto,
            'parentescoContacto' => $parentescoContacto,
            'numeroContacto' => $numeroContacto,
            'enfermedades' => $enfermedades,
            'alergias' => $alergias,
            'estatura' => $estatura,
            'complexion' => $complexion,
            'medicamentos' => $medicamentos,
            'tipoSangre' => $tipoSangre,
            'sSocial' => $sSocial,
            'tipoSeguro' => isset($tipoSeguro) && $tipoSeguro != '' ? $tipoSeguro : 'NULL',
            'numeroSeguro' => isset($numeroSeguro) && $numeroSeguro != '' ? $numeroSeguro : 'NULL'
        ];

        $editaPersona = $personaClass->editaPersona($datosUsuario);
        if(isset($_FILES["imagen"])){
            guardaImagenPerfil($usuarioId);
        }
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
    $img = $_FILES["imagen"];
    $archivo = new Archivo();
    $url = 'archivos_usuario/'.$usuarioId.'/imagen_perfil';
    $imagen = $archivo::guardar_archivo_main(
        $img,//la variable tipo file donde viene el archivo
        'perfil',
        $url
      ); 
        
          if($imagen['status'] == 1 ){
            $basename = basename($img["name"]);
            $extension = strtolower(pathinfo($basename,PATHINFO_EXTENSION));
            update('usuario','url_foto ="assets/'.$url.'/perfil.'.$extension.'"','usuario_id ='.$usuarioId);

          $json = array("status" => 1, "msg" => "Se inserto la imagen correctamente", "url" => $imagen['url']);
         }else{
          $json = array("status" => 0, "msg" => "No se logró insertar la imagen");
         }

}