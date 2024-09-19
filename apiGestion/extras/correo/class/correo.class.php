<?php

 use PHPMailer\PHPMailer\PHPMailer;
 use PHPMailer\PHPMailer\OAuth;
 use PHPMailer\PHPMailer\Exception;

require '../../vendor/PHPMailer/src/Exception.php';
require '../../vendor/PHPMailer/src/PHPMailer.php';
require '../../vendor/PHPMailer/src/SMTP.php';
require '../../vendor/PHPMailer/src/OAuth.php';
use League\OAuth2\Client\Provider\Google;

class Correo{
  public static function enviar_correo_pasado($asunto, $cuerpo, $id_persona)
  {

    $arreglo_persona = arreglo(query('SELECT persona_id, nombre, primer_apellido, segundo_apellido,email from ' . $GLOBALS["db_datosGenerales"] . '.personas where persona_id=' . $id_persona));


    $logo = $GLOBALS['url_front'] . 'assets/images/logo.png';
    $cuerpo = str_replace("*url*", $GLOBALS['url_front'], $cuerpo);
    $cuerpo = str_replace("*logo*", $logo, $cuerpo);
    $cuerpo = str_replace("*correo*", $arreglo_persona['email'], $cuerpo);
    $cuerpo = str_replace("*nombre*", $arreglo_persona['nombre'] . ' ' . $arreglo_persona['primer_apellido'] . ' ' . $arreglo_persona['segundo_apellido'], $cuerpo);


    // Instantiation and passing `true` enables exceptions
    $mail = new PHPMailer(true);

    //Server settings
    $mail->SMTPDebug = 0;                                       // Enable verbose debug output
    $mail->isSMTP();                                            // Set mailer to use SMTP
    $mail->Host       = 'smtp.gmail.com';              // Specify main and backup SMTP servers
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = $GLOBALS['smtp_user'];             // SMTP username
    $mail->Password   = $GLOBALS['smtp_pass'];                       // SMTP password
    $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
    $mail->Port       = 587;
    $mail->SMTP       = array(                                  // TCP port to connect to
      'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
      )
    );

    //Recipients
    $mail->setFrom($GLOBALS['smtp_user'], $GLOBALS['smtp_username']);
    $mail->addAddress($arreglo_persona['email'], "");
    //$mail->addAddress($correo, $destinatario);                  // Add a recipient    
    // $mail->addAddress('ellen@example.com');                  // Name is optional
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    // Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');      // Add attachments
    // $archivo = '../../documentos_instituciones/1/manual/Manual_del_alumno.zip';       
    // $mail->addAttachment($archivo, 'Manual_del_alumno.zip');    // Optional name


    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = $asunto;
    $mail->Body    = $cuerpo;
    $mail->AltBody = "No se pudo verificar";
    $mail->CharSet = 'UTF-8';
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    /*if ($mail->send())
          echo "correo enviado";
          else 
          echo "no se pudo enviar";*/
    $mail->send();
    // echo '<div class="alert alert-success"> <strong>Emails mandados correctamente.</strong></div>';
    // } catch (Exception $e) {
    // echo "Error al enviar el mensaje:' {$mail->ErrorInfo}";
    // }
    return $mail;
  }


  public static function enviar_correo($asunto, $cuerpo, $id_persona)
  {
    $arreglo_persona = arreglo(query('SELECT persona_id, nombre, primer_apellido, segundo_apellido,email from ' . $GLOBALS["db_datosGenerales"] . '.personas where persona_id=' . $id_persona));

    if ($GLOBALS['produccion'] == 1) {
      include '../../../../../global/tokenPhpmail.php';
      // llaves del phpmailer
      $GLOBALS['id_cliente'] = '21837924399-rjpsvn3moca4fsbl3ik9oegvrlc24ars.apps.googleusercontent.com';
      $GLOBALS['cliente_secret'] = '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';
      $GLOBALS['refresh_token'] = $refresh_token;
    }


    $logo = $GLOBALS['url_front'] . 'assets/images/logo.png';
    $cuerpo = str_replace("*url*", $GLOBALS['url_front_assets'], $cuerpo);
    $cuerpo = str_replace("*logo*", $logo, $cuerpo);
    $cuerpo = str_replace("*correo*", $arreglo_persona['email'], $cuerpo);
    $cuerpo = str_replace("*nombre*", $arreglo_persona['nombre'] . ' ' . $arreglo_persona['primer_apellido'] . ' ' . $arreglo_persona['segundo_apellido'], $cuerpo);
    $name = $arreglo_persona['nombre'] . ' ' . $arreglo_persona['primer_apellido'] . ' ' . $arreglo_persona['segundo_apellido'];
    $email = $arreglo_persona['email'];
    $subject = $asunto;
    $content = $cuerpo;
    // echo $logo;
    // $recipientArray = explode(",", Config::RECIPIENT_EMAIL);

    require_once '../../../vendor/autoload.php';
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'smtp.gmail.com';;
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->AuthType = 'XOAUTH2';

    //Fill in authentication details here
    //Either the gmail account owner, or the user that gave consent
    $oauthUserEmail = $GLOBALS['smtp_user'];
    $clientId = $GLOBALS['id_cliente'];
    $clientSecret = $GLOBALS['cliente_secret'];

    //Obtained by configuring and running get_oauth_token.php
    //after setting up an app in Google Developer Console.
    $refreshToken = $GLOBALS['refresh_token'];

    //Create a new OAuth2 provider instance
    $provider = new Google(
      [
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
      ]
    );

    //Pass the OAuth provider instance to PHPMailer
    $mail->setOAuth(
      new OAuth(
        [
          'provider' => $provider,
          'clientId' => $clientId,
          'clientSecret' => $clientSecret,
          'refreshToken' => $refreshToken,
          'userName' => $oauthUserEmail,
        ]
      )
    );

    // Recipients
    $mail->setFrom($GLOBALS['smtp_user'], $GLOBALS['smtp_username']);
    $mail->addAddress($arreglo_persona['email'], "");

    // $mail->addReplyTo($email, $name);
    //Replace the plain text body with one created manually

    $mail->isHTML(true);
    $mail->Subject = $asunto;
    $mail->Body    = $cuerpo;
    $mail->AltBody = "No se pudo verificar";
    $mail->CharSet = 'UTF-8';


    // if (!$mail->send()) {
    //     $output = json_encode(array('type'=>'error', 'text' => '<b>'.$from.'</b> is invalid.'));
    //     $output = json_encode(array('type'=>'error', 'text' => 'Server error. Please mail vincy@phppot.com'));
    // } else {
    //     $output = json_encode(array('type'=>'message', 'text' => 'Thank you, I will get back to you shortly.'));
    // }
    // return $output;
    $mail->send();
    return $mail;
  }

  public static function enviar_correo_multiple($asunto, $cuerpo, $lista_personas)
  {

    if ($GLOBALS['produccion'] == 1) {
      include '../../../../../global/tokenPhpmail.php';
      // llaves del phpmailer
      $GLOBALS['id_cliente'] = '21837924399-rjpsvn3moca4fsbl3ik9oegvrlc24ars.apps.googleusercontent.com';
      $GLOBALS['cliente_secret'] = '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';
      $GLOBALS['refresh_token'] = $refresh_token;
    }

    $logo = $GLOBALS['url_front'] . 'assets/images/logo.png';
    $cuerpo = str_replace("**url**", $GLOBALS['url_front_assets'], $cuerpo);
    $cuerpo = str_replace("**logo**", $logo, $cuerpo);
    // $cuerpo = str_replace("*correo*", $arreglo_persona['email'], $cuerpo);
    // $cuerpo = str_replace("*nombre*", $arreglo_persona['nombre'] . ' ' . $arreglo_persona['primer_apellido'] . ' ' . $arreglo_persona['segundo_apellido'], $cuerpo);

    require_once '../../../vendor/autoload.php';
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host = 'smtp.gmail.com';;
    $mail->Port = 587;
    $mail->SMTPSecure = 'tls';
    $mail->SMTPAuth = true;
    $mail->AuthType = 'XOAUTH2';

    //Fill in authentication details here
    //Either the gmail account owner, or the user that gave consent
    $oauthUserEmail = $GLOBALS['smtp_user'];
    $clientId = $GLOBALS['id_cliente'];
    $clientSecret = $GLOBALS['cliente_secret'];

    //Obtained by configuring and running get_oauth_token.php
    //after setting up an app in Google Developer Console.
    $refreshToken = $GLOBALS['refresh_token'];

    //Create a new OAuth2 provider instance
    $provider = new Google(
      [
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
      ]
    );

    //Pass the OAuth provider instance to PHPMailer
    $mail->setOAuth(
      new OAuth(
        [
          'provider' => $provider,
          'clientId' => $clientId,
          'clientSecret' => $clientSecret,
          'refreshToken' => $refreshToken,
          'userName' => $oauthUserEmail,
        ]
      )
    );

    // Recipients
    $mail->setFrom($GLOBALS['smtp_user'], $GLOBALS['smtp_username']);
    foreach ($lista_personas as $persona) {
      $mail->addAddress($persona['email'], $persona['persona_nombre']);
    }

    //Replace the plain text body with one created manually

    $mail->isHTML(true);
    $mail->Subject = $asunto;
    $mail->Body    = $cuerpo;
    $mail->AltBody = "No se pudo verificar";
    $mail->CharSet = 'UTF-8';

    if (!$mail->send()) {
      return json_encode(array('status' => 0, 'type' => 'error', 'text' => '<b> Prueba error </b> is invalid.'));
    }
    return json_encode(array('status' => 1, 'type' => 'message', 'text' => 'Thank you, I will get back to you shortly.'));
  }

  public static function test_body()
  {
    return self::usuario_password(1, 'richard', 'richard123');
  }


  public static function  registro_exitoso($id_persona, $usuario, $password, $carrera)
  {

    $asunto = 'Estás a unos pasos de completar tu registro a la Universidad.';
    $cuerpo = file_get_contents('../../extras/correo/bodies/registroExitoso.html');

    $cuerpo = str_replace("*usuario*", $usuario, $cuerpo);
    $cuerpo = str_replace("*password*", $password, $cuerpo);
    $cuerpo = str_replace("*carrera*", $carrera, $cuerpo);

    $status_correo = self::enviar_correo($asunto, $cuerpo, $id_persona);

    return $status_correo;
  }

  public static function  usuario_password($id_persona, $usuario, $password)
  {

    $asunto = 'Accesos a la plataforma';
    $cuerpo = file_get_contents('../../extras/correo/bodies/usuarioPassword.html');

    $cuerpo = str_replace("*usuario*", $usuario, $cuerpo);
    $cuerpo = str_replace("*password*", $password, $cuerpo);

    $status_correo = self::enviar_correo($asunto, $cuerpo, $id_persona);

    return $status_correo;
  }


  public static function matriculacion_exitosa($id_persona, $clave_alumno, $carrera)
  {

    $asunto = 'Bienvenido a tu Universidad ';
    $cuerpo = file_get_contents('../../extras/correo/bodies/matriculacionExitosa.html');

    if ($GLOBALS['matriculacion_especial'] == 0) {
      $password_encriptado = 'Alumno' . $clave_alumno;
    } else {
      $password_encriptado = $clave_alumno;
    }

    $cuerpo = str_replace("*matricula*", $password_encriptado, $cuerpo);
    $cuerpo = str_replace("*carrera*", $carrera, $cuerpo);


    $status_correo = self::enviar_correo($asunto, $cuerpo, $id_persona);

    return $status_correo;
  }

  public static function pago_exitoso($id_persona)
  {
    $asunto = 'Tu pago ha sido confirmado.';
    $cuerpo = file_get_contents('../../extras/correo/bodies/pagoExitoso.html');
    $status_correo = self::enviar_correo($asunto, $cuerpo, $id_persona);
    return $status_correo;
  }
  public static function actividad_reactivada($lista_personas, $asignatura_nombre, $grupo_nombre)
  {
    $asunto = 'Tienes una actividad reactivada.';
    $cuerpo = file_get_contents('../../extras/correo/bodies/ActividadReactivada.html');
    $cuerpo = str_replace("**asignatura**", $asignatura_nombre, $cuerpo);
    $cuerpo = str_replace("**grupo**", $grupo_nombre, $cuerpo);
    $status_correo = self::enviar_correo_multiple($asunto, $cuerpo, $lista_personas);
    return $status_correo;
  }
  public static function nueva_actividad($lista_personas, $asignatura_nombre, $grupo_nombre)
  {
    $asunto = 'Tienes una nueva actividad.';
    $cuerpo = file_get_contents('../../extras/correo/bodies/nuevaActividad.html');
    $cuerpo = str_replace("**asignatura**", $asignatura_nombre, $cuerpo);
    $cuerpo = str_replace("**grupo**", $grupo_nombre, $cuerpo);
    $status_correo = self::enviar_correo_multiple($asunto, $cuerpo, $lista_personas);
    return $status_correo;
  }
  public static function actividad_calificada($id_persona, $actividad_nombre, $grupo_nombre, $asignatura_nombre)
  {
    $asunto = 'Tienes una actividad calificada.';
    $cuerpo = file_get_contents('../../extras/correo/bodies/actividadCalificada.html');
    $cuerpo = str_replace("**actividad**", $actividad_nombre, $cuerpo);
    $cuerpo = str_replace("**asignatura**", $asignatura_nombre, $cuerpo);
    $cuerpo = str_replace("**grupo**", $grupo_nombre, $cuerpo);
    $status_correo = self::enviar_correo($asunto, $cuerpo, $id_persona);
    return $status_correo;
  }

  public static function reestablecePass($id_persona, $codigo)
  {
    $asunto = 'Código de seguridad de tu cuenta.';
    $cuerpo = file_get_contents('../../extras/correo/bodies/reestablecerPass.html');
    $cuerpo = str_replace("*codigo*", $codigo, $cuerpo);
    $status_correo = self::enviar_correo($asunto, $cuerpo, $id_persona);
    return $status_correo;
  }

  public static function correccion($id_persona)
  {

    $asunto = 'Observaciones en su expediente.';
    $cuerpo = file_get_contents('../../extras/correo/bodies/correccion.html');
    $status_correo = self::enviar_correo($asunto, $cuerpo, $id_persona);
    return $status_correo;
  }

  public static function pendientesDocente($id_persona, $retroalimentacion, $mensajesPendientes, $actividades_pendientes)
  {

    $asunto = 'Tiene pendientes por revisar.';
    $cuerpo = file_get_contents('../../extras/correo/bodies/pendientesDocente.html');
    $cuerpo = str_replace("*retroalimentacion*", $retroalimentacion, $cuerpo);
    $cuerpo = str_replace("*mensajes_pendientes*", $mensajesPendientes, $cuerpo);
    $cuerpo = str_replace("*actividades_pendintes*", $actividades_pendientes, $cuerpo);
    $status_correo = self::enviar_correo($asunto, $cuerpo, $id_persona);
    return $status_correo;
  }

  public static function documentacionCompleta($id_persona)
  {

    $asunto = 'Documentación validada.';
    $cuerpo = file_get_contents('../../extras/correo/bodies/documentacionCompleta.html');

    // $cuerpo = str_replace("*retroalimentacion*", $retroalimentacion, $cuerpo);    
    // $cuerpo = str_replace("*mensajes_pendientes*", $mensajesPendientes, $cuerpo);    
    // $cuerpo = str_replace("*actividades_pendintes*", $actividades_pendientes, $cuerpo);    


    $status_correo = self::enviar_correo($asunto, $cuerpo, $id_persona);

    return $status_correo;
  }
}


$cabeceras = 'From: ricardo.ruiz@estudy.mx \r\n' .
  'Reply-To: ricardo.ruiz@estudy.mx \r\n' .
  'MIME-Version: 1.0 \r\n' .
  'Content-type: text/html; charset=iso-8859-1 \r\n';
