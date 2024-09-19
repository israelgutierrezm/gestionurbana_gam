<?php

class Archivo {

    public static $default_sizefile = 10; //tamaño (EN MEGAS) base
    public static $default_url = '../../../'; //url para acceder a los arvhic    
    public static $extensiones_disponibles; //url para la base
    public static $maxfilesize;

    
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

    public static function elimina_archivo($url){
        if($url != "" & $url != null){
            $directorio = self::$default_url.'/'.$url;

            if(unlink($directorio))
                return 1;
            else
                return 0;
        }else{
            return 0;
        }
        
            
          
    }

    public static function guardar_archivo_main(
        $ruta, // la ruta del archivo
        $id_ruta, //el id de la ruta
        $file,//la variable tipo file donde viene el archivo
        $nombre_archivo, //el nombre que va a llevar el archivo a guardar
        $tipo_archivo, //extensiones (imagen, documentos,*)
        $tamanio, //tamaño de la extension
        $ruta_main, //ruta del propietario
        $id_main//id del propietario
        ) {

    
        $directorio_limpio ='assets/'.$ruta_main.'/'.$id_main.'/'.$ruta.'/'.$id_ruta;
        $directorio = self::$default_url.$directorio_limpio;

        $dirOK = self::carpeta($directorio);

        if(!$dirOK){
            return 7;
        }

        //if($nombre_archivo == null ) $nombre_archivo = basename($file["name"]);

        $archivoOK = self::guarda_archivo_servidor($directorio,$file,$nombre_archivo,$tipo_archivo,$tamanio);
        
        if ($archivoOK == 1) {

            $basename = basename($file["name"]);
            $imageFileType = strtolower(pathinfo($basename,PATHINFO_EXTENSION));
            $url_db = '/'.$directorio_limpio.'/'.$nombre_archivo.'.'.$imageFileType;;

            return array("status" => 1, "msg" => "archivo guardado en el servidor","url" =>$url_db);
            
        }else{
            return self::mensaje_estatus($archivoOK);
        } 

    }

    public static function guardar_archivo(
        $ruta, // la ruta del archivo
        $id_ruta, //el id de la ruta
        $file,//la variable tipo file donde viene el archivo
        $nombre_archivo, //el nombre que va a llevar el archivo a guardar
        $tipo_archivo, //tipo de archivo
        $tamanio //tamaño máximo del archivo
        ) {

            

        $directorio_limpio = 'assets/'.$ruta.'/'.$id_ruta;
        $directorio = self::$default_url.$directorio_limpio;
        
        $dirOK = self::carpeta($directorio);

        if(!$dirOK){
            return 7;
        }

        //if($nombre_archivo == null ) $nombre_archivo = basename($file["name"]);

        $archivoOK = self::guarda_archivo_servidor($directorio,$file,$nombre_archivo,$tipo_archivo,$tamanio);
        
        if ($archivoOK == 1) {


        $basename = basename($file["name"]);
        $imageFileType = strtolower(pathinfo($basename,PATHINFO_EXTENSION));
        $url_db = '/'.$directorio_limpio.'/'.$nombre_archivo.'.'.$imageFileType;

        return array("status" => 1, "msg" => "archivo guardado en el servidor","url" =>$url_db);
            
        }else{
            return self::mensaje_estatus($archivoOK);
        } 

    }

    //revisa que la carpeta exista, si no existe la crea, sino solo marca que existe
    public static function carpeta(
        $directorio
        ){
        if( !is_dir($directorio)){
            $crear = mkdir($directorio,0777,true);  
            if($crear){
                return 1;     
            }else{
                return 0;
            }
                
        }else{
            return 1;
        }
    }



    public static function guarda_archivo_servidor(
        $directorio,
        $file,
        $nombre_archivo,
        $tipo_formato,
        $maxfilesize //máximo tamaño de archivo en MegaBytes
        ){

            $basename = basename($file["name"]);
            $extension = strtolower(pathinfo($basename,PATHINFO_EXTENSION));

            $target_file = $directorio ."/".$nombre_archivo.".".$extension;

            // Check if image file is a actual image or fake image
            /*$check = getimagesize($file["tmp_name"]);
            if($check !== false) {
                    $uploadOk = 1;
            } else {
                $uploadOk = 0;
                return 6;
            }*/

            self::$extensiones_disponibles = self::consulta_extension($tipo_formato);

            //FORMATO DE ARCHIVO
            $formato_valido = self::valida_extension($extension,self::$extensiones_disponibles);
            if(!$formato_valido){
                return 8;
            }

            //TAMAÑO DE ARCHIVO
            $tamanio_valido = self::revisar_tamanio($file["size"],$maxfilesize);
            if(!$tamanio_valido){
                return 5;
            }

            $archivo_servidorOK = move_uploaded_file($file["tmp_name"], $target_file);
            
            if($archivo_servidorOK)
                return 1;
            else
                return 3;
            
        

    }

    public static function consulta_extension($tipo_extension){
        if($tipo_extension == null) $tipo_extension = 100;

        $query_extension =  query('select * from '.$GLOBALS['db_datosGenerales'].'.cat_formatos where cat_formato_id='.$tipo_extension);
        if(num($query_extension)){
            $arreglo_extension=arreglo($query_extension);
            $extensiones = $arreglo_extension['tipo_extension'];
        }
        else 
            $extensiones = null;

        return $extensiones;
    }

    public static function valida_extension($extension,$extensiones_disponibles){
        

        $formato_real=strpos($extensiones_disponibles, $extension,0);

        if ($formato_real === false){
            return 0;
        }else{
            return 1;
        }
    }

    public static function revisar_tamanio($size,$maxfilesize){

        if($maxfilesize == null){//por defecto dejamos 10 m
            self::$maxfilesize  =  self::$default_sizefile;
            $maxfilesize_MB = self::$default_sizefile*1024*1024;    
        }else{
            self::$maxfilesize  =  $maxfilesize;
            $maxfilesize_MB = $maxfilesize *1024*1024;
        }
        

        if ($size  < $maxfilesize_MB ) {
            return 1;
        }else{
            return 0;
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
                return array("status" => 0, "msg" => "El archivo no debe exceder ".self::$maxfilesize." MegaBytes");
            break;
            case 6:
                return array("status" => 0, "msg" => "El archivo no es real (mimetype).");
            break;
            case 7:
                return array("status" => 0, "msg" => "No se logró crear la carpeta");
            break;
            case 8:
                return array("status" => 0, "msg" => "El archivo debe tener alguno de los siguientes formatos: ".self::$extensiones_disponibles."");    
            break;
            

        }
    }



} 
