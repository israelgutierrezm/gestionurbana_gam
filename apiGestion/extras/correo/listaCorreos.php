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
    
    db('SMTP');
    $jwt = $_POST['jwt'];
    $id_grupo_correo = $_POST['id_grupo_correo'];
    $id_correo = $_POST['id_correo'];

    
    $usuario = Auth::GetData(
        $jwt  
    );

    $query_correo = query('SELECT * FROM tr_correo tc 
    join tr_configura_correo tcc on tcc.configura_correo_id = tc.configuracion_id where correo_id = '.$id_correo);


    if(num($query_correo) > 0 ){

        
      $arreglo_correo = arreglo($query_correo);

        //Server settings
        $mail->SMTPDebug = 0;
                
  $mail->SMTPOptions = array(
    'ssl' => array(
    'verify_peer' => false,
    'verify_peer_name' => false,
    'allow_self_signed' => true
    )
    );                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP

        $mail->Host       = $arreglo_correo['servidor'];  						// Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = $arreglo_correo['usuario_correo'];             // SMTP username
        $mail->Password   = $arreglo_correo['password_correo'];                       // SMTP password
        $mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = $arreglo_correo['puerto']; 

 
        $mail->setFrom($arreglo_correo['correo_from']);

        if($arreglo_correo['correo_cc'])
          $mail->addCC($arreglo_correo['correo_cc']);
        if($arreglo_correo['correo_bco'])
          $mail->addCC($arreglo_correo['correo_bco']);

        
        // Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');      // Add attachments
         //$archivo = '../../documentos_instituciones/1/manual/Manual_del_alumno.zip';       

         if($arreglo_correo['correo_attachment'])
            $mail->addAttachment($arreglo_correo['correo_attachment']);    // Optional name

      

        $query_grupo_correo = query('SELECT * FROM tr_grupo_correo tgc         
        join '.$GLOBALS["db_datosGenerales"].'.personas p on p.persona_id = tgc.persona_id
        join '.$GLOBALS["db_datosGenerales"].'.inter_persona_usuario_rol_institucion ipur on p.persona_id = ipur.persona_id
        join '.$GLOBALS["db_datosGenerales"].'.usuarios u on ipur.usuario_id = u.usuario_id
        where grupo_correo_id = '.$id_grupo_correo);
        


        if(num($query_grupo_correo) > 0 ){
          while ($arreglo_grupo_correo = arreglo($query_grupo_correo)){
        
            // Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $arreglo_correo['correo_subject'];


        $cambio = str_replace('USERUSER',$arreglo_grupo_correo['usuario'],$arreglo_correo['correo_body']);
        // $cambio_1 = str_replace('USER',$arreglo_grupo_correo['nombre'],$arreglo_correo['correo_body']);
        // $cambio_2= str_replace('PASSPASS',$arreglo_grupo_correo['curp'],$cambio_1);    
        
        $mail->Body    = $cambio;
        // echo $cambio;
        $mail->AltBody = "No se pudo verificar";     
        $mail->CharSet = 'UTF-8';
        
            if($arreglo_grupo_correo['email'])
              $mail->addAddress( $arreglo_grupo_correo['email'],$arreglo_grupo_correo['nombre']);
              $mail->send();
              $mail->clearAddresses();
            }


          if(1){
              $json = array("status" => 1, "msg" => "Se enviaron los correos correctamente");
          }else{
              $json = array("status" => 0, "msg" => "No se logró enviar");
          }


        }else{
          $json = array("status" => 0, "msg" => "No se encontró el grupo de correos");
        }


     }else{
      $json = array("status" => 0, "msg" => "No se encontró el correo a enviar");
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
