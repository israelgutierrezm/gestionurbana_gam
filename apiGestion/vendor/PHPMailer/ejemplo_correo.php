<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
// Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);
try {
    //Server settings
    $mail->SMTPDebug = 0;                                       // Enable verbose debug output
    $mail->isSMTP();                                            // Set mailer to use SMTP
    $mail->Host       = 'smtp.gmail.com';  						// Specify main and backup SMTP servers
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'vicomarin26@gmail.com';                     // SMTP username
    $mail->Password   = 'Nacho2017';                               // SMTP password
    $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
    $mail->Port       = 587;                                    // TCP port to connect to

    $correos = array("Victor Ornelas", "", "");
    $emails = array("naxuuu_@hotmail.com", "", "");    
    //Recipients
    $mail->setFrom('vicomarin26@gmail.com', 'Victor');
    for ($i=0; $i < 3; $i++) { 
    $mail->addAddress($emails[$i],$correos[$i]);     // Add a recipient    
    echo $correos[$i];
    }
    // $mail->addAddress('ellen@example.com');               // Name is optional
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    // Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

    // Content
    $mail->isHTML(true);                                  // Set email format to HTML
    $mail->Subject = 'Correo de prueba PHPMailer';
    $mail->Body    = 'Mensaje enviado desde PHPMailer';
    $mail->CharSet = 'UTF-8';
    // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    // echo 'Mensaje enviado';
} catch (Exception $e) {
    echo "Error al enviar el mensaje:' $mail->ErrorInfo}";
}

?>
