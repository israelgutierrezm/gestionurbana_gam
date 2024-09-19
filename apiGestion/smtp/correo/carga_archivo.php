<?php
include '../../jwt.php';
include '../../headers.php';

try {
 db ('SMTP');

  if($_SERVER['REQUEST_METHOD'] == "POST"){
    foreach($_POST as $clave => $valor){
      ${$clave} = escape_cara($valor);
    }
    
      $usuario = Auth::GetData(
           $jwt  
        );

$carpeta = htmlspecialchars($id_correo);
$ruta = htmlspecialchars('../../../assets/archivos_correos/');
$directorio = $ruta.$carpeta;

if( !is_dir($directorio) )
{
    $crear = mkdir($directorio);
    if(!$crear){
        
        $json = array("status" => 0, "msg" => "No se logró insertar");
     }
}


$uploadOk = 1;

$basename = basename($_FILES["fileToUpload"]["name"]);
$imageFileType = strtolower(pathinfo($basename,PATHINFO_EXTENSION));
$target_dir = $directorio;
$target_file = $target_dir ."/adjunto.".$imageFileType;




// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    $json = array("status" => 0, "msg" => "Archivo demasiado grande.");
    //echo "Archivo demasiado grande.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    //echo "No se logro subir archivo.";
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $res = substr($target_file,8);
        $edita = update('tr_correo',
        'correo_attachment = "'.$res.'"',
        'correo_id = '.$id_correo);
        $json = array("status" => 1, "msg" => "Se guardó el archivo correctamente.");
        //echo "El archivo ". basename( $_FILES["fileToUpload"]["name"]). " se subio correctamente.";
    } else {
        //echo "Sorry, there was an error uploading your file.";
        $json = array("status" => 0, "msg" => "No se logró subir el archivo.");
    }
}

/* Output header */
}else{
    $json = array("status" => 0, "msg" => "Método no aceptado");
}

echo json_encode($json);

} catch (Exception $e) {
    $json = array("status" => 0, "msg" =>  $e->getMessage());

    echo json_encode($json);
}


 
?>