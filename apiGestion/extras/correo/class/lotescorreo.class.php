<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require '../../vendor/PHPMailer/src/Exception.php';
require '../../vendor/PHPMailer/src/PHPMailer.php';
require '../../vendor/PHPMailer/src/SMTP.php';

class LotesCorreo{

  public static $mail;

  public static function inicializa_mail(){

    self::$mail = new PHPMailer(true);    
    
    //Server settings
    self::$mail->SMTPDebug = 0;                                       // Enable verbose debug output
    self::$mail->isSMTP();                                            // Set mailer to use SMTP
    self::$mail->Host       = 'smtp.gmail.com';  						// Specify main and backup SMTP servers
    self::$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    self::$mail->Username   = $GLOBALS['smtp_user'];             // SMTP username
    self::$mail->Password   = $GLOBALS['smtp_pass'];                       // SMTP password
    self::$mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
    self::$mail->Port       = 587;      
    self::$mail->SMTP       = array(                                  // TCP port to connect to
        'ssl' => array(
            'verify_peer' => false,             
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    ); 
    self::$mail->CharSet = 'UTF-8';  
    self::$mail->setFrom($GLOBALS['smtp_user'], $GLOBALS['smtp_username']);

  }


  public static function enviar_correo($asunto,$cuerpo,$email){
    

    $logo = $GLOBALS['url_front'].'assets/images/email/logo.png';
    $cuerpo = str_replace("**url**", $GLOBALS['url_front'], $cuerpo);
    $cuerpo = str_replace("**logo**", $logo, $cuerpo);

    
  
    //Recipients
    
    self::$mail->addAddress($email, "");

        // Content
        self::$mail->isHTML(true);                                  // Set email format to HTML
        self::$mail->Subject = $asunto;
        self::$mail->Body    = $cuerpo;
        self::$mail->AltBody = "No se pudo verificar";     
        
        self::$mail->send();
    
    return self::$mail;

  }

  public static function matriculacion_exitosa($nombre,$correo,$matricula){
    
    $asunto ='Bienvenido a la Universidad '; 
    $cuerpo= file_get_contents('../../extras/correo/bodies/matriculacionExitosa.html');
    
    $cuerpo = str_replace("**matricula**", $matricula, $cuerpo);
    
    $cuerpo = str_replace("**nombre**", $nombre, $cuerpo);
    $cuerpo = str_replace("**correo**", $correo, $cuerpo);
    

    $status_correo = self::enviar_correo($asunto,$cuerpo,$correo);

    return $status_correo;



  }





}

    
?>
