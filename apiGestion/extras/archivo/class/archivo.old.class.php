<?php

class Archivo {
    

    public static function guarda_archivo_alumno(
        $carpeta,//nombre de la carpeta donde se va guardar
        $ruta, // la ruta del archivo
        $file,//la variable tipo file donde viene el archivo
        $nombre_tabla, //el nombre de la tabla
        $nombre_columna, //el nombre de la columna a editar
        $nombre_columna_id, // el nombre de la columna id de la tabla
        $id, //el id de la columna
        $prefijo_nombre_archivo, //el nombre que va a llevar el archivo a guardar
        $tipo_archivo, //extensiones (imagen, documentos,*)
        $tamanio //tamaño de la extension
        ) {
        $directorio = $ruta.$id;
        $directorio1 = $directorio.'/'.$carpeta;    
        if( !is_dir($directorio)){
                $crear = mkdir($directorio);
                    if(!is_dir($directorio1)){
                        $crear1 = mkdir($directorio1);
                            
                            if(!$crear1){
                            return 7;
                                    }
                    }          
        }
        $uploadOk = 1;
        $basename = basename($file["name"]);
        $imageFileType = strtolower(pathinfo($basename,PATHINFO_EXTENSION));
        $target_file = $directorio1 ."/".$prefijo_nombre_archivo.".".$imageFileType;

        // Check if image file is a actual image or fake image
        /*$check = getimagesize($file["tmp_name"]);
        if($check !== false) {
                $uploadOk = 1;
        } else {
            $uploadOk = 0;
            return 6;
        }*/


        if ($file["size"] > $tamanio) {
            $uploadOk = 0;
            return 5;
            //echo "Archivo demasiado grande.";

        }

        // Allow certain file formats
        switch($tipo_archivo){
            case "imagen";
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != 
            "jpeg" && $imageFileType != "gif" ) {
                $uploadOk = 0;
                return 21;
            }
            break;

            case "archivo";
            if ($imageFileType != "docx" && $imageFileType != "pdf" && $imageFileType != 
            "xml" && $imageFileType != "ppt" ) {
                $uploadOk = 0;
                return 22;    
            }
            break;
            case "*";
            if ($imageFileType != "docx" && $imageFileType != "pdf" && $imageFileType != 
            "xml" && $imageFileType != "ppt" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != 
            "jpeg" && $imageFileType != "gif" ) {
                $uploadOk = 0;
                return 22;    
            }
            break;

            default:
                $uploadOk = 0;
                return 20;
        }

        if ($uploadOk == 0) {
            return 4;
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                    $res = substr($target_file,8);

                    $update = update(
                        $nombre_tabla,
                        $nombre_columna .'= "'.$res.'"', 
                        $nombre_columna_id.' = '. $id);

                    if($update){
                        return 1;
                    }else{
                        return 2;
                    }
            } else {
                return 3;
            }
        }
     


    }

    public static function mensaje_estatus($estatus){
        switch($estatus){
            case 1:
                return array("status" => 1, "msg" => "El archivo se guardó correctamente.");
            break;
            case 2:
                return array("status" => 0, "msg" => "No se logró guardar el registro");
            break;
            case 3:
                return array("status" => 0, "msg" => "No se logro cargar el archivo al servidor");
            break;
            case 4:
                return array("status" => 0, "msg" => "No se logro subir archivo.");
            break;
            case 5:
                return array("status" => 0, "msg" => "Archivo demasiado grande.");
            break;
            case 6:
                return array("status" => 0, "msg" => "El archivo no es real (mimetype).");
            break;
            case 7:
                return array("status" => 0, "msg" => "No se logró crear la carpeta");
            break;
            case 20:
                return array("status" => 0, "msg" => "no se especificó el tipo de archivo");    
            break;
            case 21:
                return array("status" => 0, "msg" => "Solo están permitidos JPG, JPEG, PNG y GIF.");
            break;
            case 22:
                return array("status" => 0, "msg" => "Solo están permitidos Word, Excel, PowerPoint y PDF.");
            break;
            case 23:
            case 24:
            case 25:
            case 26:
            case 27:
                return array("status" => 0, "msg" => "Formato de archivo no permitido.");
            break;

        }
    }

    public static function guarda_archivo(
        $ruta, // la ruta del archivo
        $file,//la variable tipo file donde viene el archivo
        $nombre_tabla, //el nombre de la tabla
        $nombre_columna, //el nombre de la columna a editar
        $nombre_columna_id, // el nombre de la columna id de la tabla
        $id, //el id de la columna
        $prefijo_nombre_archivo, //el nombre que va a llevar el archivo a guardar
        $tipo_archivo, //extensiones (imagen, documentos,*)
        $tamanio //tamaño de la extension
        ) {
        $directorio = $ruta.$id;
            
        if( !is_dir($directorio)){
                $crear = mkdir($directorio);                                
                    if(!$crear){
                    return 7;
                    }        
        }
        $uploadOk = 1;
        $basename = basename($file["name"]);
        $imageFileType = strtolower(pathinfo($basename,PATHINFO_EXTENSION));
        $target_file = $directorio ."/".$prefijo_nombre_archivo.".".$imageFileType;

        // Check if image file is a actual image or fake image
        /*$check = getimagesize($file["tmp_name"]);
        if($check !== false) {
                $uploadOk = 1;
        } else {
            $uploadOk = 0;
            return 6;
        }*/


        if ($file["size"] > $tamanio) {
            $uploadOk = 0;
            return 5;
            //echo "Archivo demasiado grande.";

        }

        // Allow certain file formats
        switch($tipo_archivo){
            case "imagen";
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != 
            "jpeg" && $imageFileType != "gif" ) {
                $uploadOk = 0;
                return 21;
            }
            break;

            case "archivo";
            if ($imageFileType != "docx" && $imageFileType != "pdf" && $imageFileType != 
            "xml" && $imageFileType != "ppt" ) {
                $uploadOk = 0;
                return 22;    
            }
            break;
            case "*";
            if ($imageFileType != "docx" && $imageFileType != "pdf" && $imageFileType != 
            "xml" && $imageFileType != "ppt" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != 
            "jpeg" && $imageFileType != "gif" ) {
                $uploadOk = 0;
                return 22;    
            }
            break;

            default:
                $uploadOk = 0;
                return 20;
        }

        if ($uploadOk == 0) {
            return 4;
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                    $res = substr($target_file,8);

                    $update = update(
                        $nombre_tabla,
                        $nombre_columna .'= "'.$res.'"', 
                        $nombre_columna_id.' = '. $id);

                    if($update){
                        return 1;
                    }else{
                        return 2;
                    }
            } else {
                return 3;
            }
        }
     


    }


    public static function guarda_archivo_adjunto(
        $carpeta,//nombre de la carpeta donde se va guardar
        $carpeta1, //nombre de la carpeta donde se va guardar la carpeta anterior
        $id, // usuario
        $ruta, // la ruta del archivo
        $file,//la variable tipo file donde viene el archivo
        $nombre_tabla, //el nombre de la tabla
        $nombre_columna, //el nombre de la columna a editar
        $nombre_columna_id, // el nombre de la columna id de la tabla
        $id_parametro, //el id de la columna
        $prefijo_nombre_archivo, //el nombre que va a llevar el archivo a guardar
        $tipo_archivo, //extensiones (imagen, documentos,*)
        $tamanio //tamaño de la extension
        ) {
        $directorio = $ruta.$id;
        $directorio1 = $directorio.'/'.$carpeta;  
        $directorio2 = $directorio1.'/'.$carpeta1;  
        
        if( !is_dir($directorio)){
                $crear = mkdir($directorio);                        
        }   
        if(!is_dir($directorio1)){
        $crear1 = mkdir($directorio1);
        }  

        if(!is_dir($directorio2)){
        $crear2 = mkdir($directorio2);  
        if(!$crear2){
            return 7;
                }                  
        }
        $uploadOk = 1;
        $basename = basename($file["name"]);
        $imageFileType = strtolower(pathinfo($basename,PATHINFO_EXTENSION));
        $target_file = $directorio2 ."/".$prefijo_nombre_archivo.".".$imageFileType;

        // Check if image file is a actual image or fake image
        /*$check = getimagesize($file["tmp_name"]);
        if($check !== false) {
                $uploadOk = 1;
        } else {
            $uploadOk = 0;
            return 6;
        }*/


        if ($file["size"] > $tamanio) {
            $uploadOk = 0;
            return 5;
            //echo "Archivo demasiado grande.";

        }

        // Allow certain file formats
        switch($tipo_archivo){
            case "imagen";
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != 
            "jpeg" && $imageFileType != "gif" ) {
                $uploadOk = 0;
                return 21;
            }
            break;

            case "archivo";
            if ($imageFileType != "docx" && $imageFileType != "pdf" && $imageFileType != 
            "xml" && $imageFileType != "ppt" ) {
                $uploadOk = 0;
                return 22;    
            }
            break;
            case "*";
            if ($imageFileType != "docx" && $imageFileType != "pdf" && $imageFileType != 
            "xml" && $imageFileType != "ppt" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != 
            "jpeg" && $imageFileType != "gif" ) {
                $uploadOk = 0;
                return 22;    
            }
            break;

            default:
                $uploadOk = 0;
                return 20;
        }

        if ($uploadOk == 0) {
            return 4;
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                    $res = substr($target_file,8);

                    $update = update(
                        $nombre_tabla,
                        $nombre_columna .'= "'.$res.'"', 
                        $nombre_columna_id.' = '. $id_parametro);

                    if($update){
                        return 1;
                    }else{
                        return 2;
                    }
            } else {
                return 3;
            }
        }
     


    }

    
    public static function guarda_tipo_archivo(
        $carpeta,//nombre de la carpeta donde se va guardar
        $carpeta1, //nombre de la carpeta donde se va guardar la carpeta anterior
        $id, // usuario
        $ruta, // la ruta del archivo
        $file,//la variable tipo file donde viene el archivo
        $nombre_tabla, //el nombre de la tabla
        $nombre_columna, //el nombre de la columna a editar
        $nombre_columna_id, // el nombre de la columna id de la tabla
        $id_parametro, //el id de la columna
        $prefijo_nombre_archivo, //el nombre que va a llevar el archivo a guardar
        $tamanio //tamaño de la extension
        ) {
        $directorio = $ruta.$id;
        $directorio1 = $directorio.'/'.$carpeta;  
        $directorio2 = $directorio1.'/'.$carpeta1;  
        
        if( !is_dir($directorio)){
                $crear = mkdir($directorio);                        
        }   
        if(!is_dir($directorio1)){
        $crear1 = mkdir($directorio1);
        }  

        if(!is_dir($directorio2)){
        $crear2 = mkdir($directorio2);  
        if(!$crear2){
            return 7;
                }                  
        }
        $uploadOk = 1;
        $basename = basename($file["name"]);
        $imageFileType = strtolower(pathinfo($basename,PATHINFO_EXTENSION));
        $target_file = $directorio2 ."/".$prefijo_nombre_archivo.".".$imageFileType;

        // Check if image file is a actual image or fake image
        /*$check = getimagesize($file["tmp_name"]);
        if($check !== false) {
                $uploadOk = 1;
        } else {
            $uploadOk = 0;
            return 6;
        }*/


        if ($file["size"] > $tamanio) {
            $uploadOk = 0;
            return 5;
            //echo "Archivo demasiado grande.";

        }
        
        if ($uploadOk == 0) {
            return 4;
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                    $res = substr($target_file,8);

                    $update = update(
                        $nombre_tabla,
                        $nombre_columna .'= "'.$res.'"', 
                        $nombre_columna_id.' = '. $id_parametro);

                    if($update){
                        return 1;
                    }else{
                        return 2;
                    }
            } else {
                return 3;
            }
        }
     


    }

    public static function guarda_archivo_expediente(
        $ruta, // la ruta del archivo
        $file,//la variable tipo file donde viene el archivo
        $nombre_tabla, //el nombre de la tabla
        $nombre_columna, //el nombre de la columna a editar
        $nombre_columna_id, // el nombre de la columna id de la tabla
        $id, //el id de la columna
        $prefijo_nombre_archivo, //el nombre que va a llevar el archivo a guardar
        $tipo_archivo, //extensiones (imagen, documentos,*)
        $tamanio //tamaño de la extension
        ) {
        $directorio = $ruta.$id;
            
        if( !is_dir($directorio)){
                $crear = mkdir($directorio);                                
                    if(!$crear){
                    return 7;
                    }        
        }
        $uploadOk = 1;
        $basename = basename($file["name"]);
        $imageFileType = strtolower(pathinfo($basename,PATHINFO_EXTENSION));
        $target_file = $directorio ."/".$prefijo_nombre_archivo.".".$imageFileType;

        // Check if image file is a actual image or fake image
        /*$check = getimagesize($file["tmp_name"]);
        if($check !== false) {
                $uploadOk = 1;
        } else {
            $uploadOk = 0;
            return 6;
        }*/


        if ($file["size"] > $tamanio) {
            $uploadOk = 0;
            return 5;
            //echo "Archivo demasiado grande.";

        }

        // Allow certain file formats
        switch($tipo_archivo){
            case "imagen";
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != 
            "jpeg" && $imageFileType != "gif" ) {
                $uploadOk = 0;
                return 21;
            }
            break;

            case "archivo";
            if ($imageFileType != "docx" && $imageFileType != "pdf" && $imageFileType != 
            "xml" && $imageFileType != "ppt" ) {
                $uploadOk = 0;
                return 22;    
            }
            break;
            case "*";
            if ($imageFileType != "docx" && $imageFileType != "pdf" && $imageFileType != 
            "xml" && $imageFileType != "ppt" && $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != 
            "jpeg" && $imageFileType != "gif" ) {
                $uploadOk = 0;
                return 22;    
            }
            break;

            default:
                $uploadOk = 0;
                return 20;
        }

        if ($uploadOk == 0) {
            return 4;
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                    $res = substr($target_file,8);

                    $update = update($nombre_tabla,
                        $nombre_columna .'= "'.$res.'"', 
                        $nombre_columna_id.' = '. $prefijo_nombre_archivo);

                    if($update){
                        return 1;
                    }else{
                        return 2;
                    }
            } else {
                return 3;
            }
        }
     


    }



} 
