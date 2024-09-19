<?php

include '../../jwt.php';
include '../../headers.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/PHPMailer/src/Exception.php';
require '../../vendor/PHPMailer/src/PHPMailer.php';
require '../../vendor/PHPMailer/src/SMTP.php';

//function enviarCorreo($correo,$destinatario,$body){
// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);    

try {
        
  if($_SERVER['REQUEST_METHOD'] == "POST"){
    foreach($_POST as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }
    
      $usuario = Auth::GetData(
            $jwt  
        );

        //Server settings
        $mail->SMTPDebug = 0;                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host       = 'smtp.gmail.com';  						// Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = 'educacion@unisant.edu.mx';             // SMTP username
        $mail->Password   = 'Unisan2020.@';                       // SMTP password
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
        $mail->setFrom('vicomarin26@gmail.com', '');
        $mail->addAddress("victor.ornelas@estudy.com.mx", "");
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
        $mail->Subject = 'Correo de prueba PHPMailer';
        $mail->Body    = $body;
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
    if($mail){
        $json = array("status" => 1, "msg" => "Se enviaron los mail correctamente");
     }else{
        $json = array("status" => 0, "msg" => "No se logró enviar");
     }

}else{
  $json = array("status" => 0, "msg" => "Método no aceptado");
}

/* Output header */

echo json_encode($json);

} catch (Exception $e) {
$json = array("status" => 0, "msg" =>  $e->getMessage());

echo json_encode($json);
}
    
?>
