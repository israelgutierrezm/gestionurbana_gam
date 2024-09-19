<?php
include '../../jwt.php';
include '../../headers.php';

try {

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        foreach ($_POST as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        $usuario = Auth::GetData(
            $jwt
        );

        $carpeta = htmlspecialchars($id);
        $ruta = htmlspecialchars('../../../assets/CKEditor/');
        $directorio = $ruta . $nom_carpeta;
        $directorio1 = $directorio . '/' . $carpeta;

        if (!is_dir($directorio1)) {
            $crear1 = mkdir($directorio1, 0775, true);
            if (!$crear1) {
                return 7;
            }
        }

        $uploadOk = 1;

        $basename = basename($_FILES["file"]["name"]);
        $imageFileType = strtolower(pathinfo($basename, PATHINFO_EXTENSION));
        $target_dir = $directorio1;
        $target_file = $target_dir . '/' . rand(1, 999999) . '_' . $basename;
        // echo $target_file;
        // echo $directorio;

        // Check file size
        if ($_FILES["file"]["size"] > 10000000) {
            $json = array("status" => 0, "msg" => "Archivo demasiado grande.");
            //echo "Archivo demasiado grande.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            //echo "No se logro subir archivo.";
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                $res = substr($target_file, 8);

                $json = array("status" => 1, "msg" => "Se guardó el archivo correctamente.", "url" => $res);
                //echo "El archivo ". basename( $_FILES["fileToUpload"]["name"]). " se subio correctamente.";
            } else {
                //echo "Sorry, there was an error uploading your file.";
                $json = array("status" => 0, "msg" => "No se logró subir el archivo.");
            }
        }

        /* Output header */
    } else {
        $json = array("status" => 0, "msg" => "Método no aceptado");
    }

    echo json_encode($json);
} catch (Exception $e) {
    $json = array("status" => 0, "msg" =>  $e->getMessage());

    echo json_encode($json);
}
