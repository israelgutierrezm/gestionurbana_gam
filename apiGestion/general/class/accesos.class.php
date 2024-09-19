<?php

 class Accesos{

    
    public static function consultaSesionActiva($id_usuario,$horas){
        
        $query = query('select fecha_actualiza,now(), TIMESTAMPDIFF(hour,fecha_actualiza , now()) as horas_diferencia,conectado from '.$GLOBALS['db_datosGenerales'].'.usuarios where usuario_id ='.$id_usuario.' and estatus = 1');    

        if(num($query)){

            $arreglo_usuario = arreglo($query);

            if($arreglo_usuario['conectado'] == '1' && $arreglo_usuario['horas_diferencia'] >= $horas){ //si sigue conectado pero en realidad no esta conetado
                 
                update($GLOBALS['db_datosGenerales'].'.usuarios','conectado = 0','usuario_id = '.$id_usuario);

                $conectado = 0;

            }else{ //si no devuelve el estatus
                $conectado = $arreglo_usuario['conectado'];
            }

            return $conectado;
        
        }else{
            return -1; //EL USUARIO ESTA INACTIVO
        }
        

        
        
    }

    public static function consultasesionVentana($id_usuario,$ventana){
        
        $query = query('select bitacora_sesion_id, fecha_inicio, fecha_fin, now() as fecha, ventana,ip,dispositivo,navegador from tr_bitacora_sesion 
        where usuario_id = "'.$id_usuario.'" and ventana = "'.$ventana.'" order by bitacora_sesion_id desc limit 1');

        if(num($query)){
            $arreglo= arreglo($query);
        }else{
            $arreglo = null;
        }

        return $arreglo;
        
    }

    public static function cambiar_estado($id_usuario,$conectado){
        $update = query('update  '.$GLOBALS['db_datosGenerales'].'.usuarios  set conectado = '.$conectado.' where usuario_id = '.$id_usuario);

        return $update;

    }

    


    public static function consultasesion($id_usuario,$ventana){
        
        $query = query('select bitacora_sesion_id, fecha_inicio, fecha_fin, now() as fecha, ventana from tr_bitacora_sesion 
        where usuario_id = "'.$id_usuario.'" and ventana = "'.$ventana.'" order by bitacora_sesion_id desc limit 1');

        if(num($query)){
            $arreglo= arreglo($query);
        }else{
            $arreglo = null;
        }

        return $arreglo;
        
    }
    
    public static function nuevo_acceso($id_usuario,$ip_publica,$ventana,$dispositivo,$navegador,$horas){
        

        $query = query('select bitacora_sesion_id, fecha_inicio, fecha_fin,  TIMESTAMPDIFF(hour,fecha_inicio , now()) as horas_diferencia, ventana from tr_bitacora_sesion 
        where usuario_id = '.$id_usuario.' order by bitacora_sesion_id desc limit 1');

        
        $arreglo = arreglo($query);
       
          if(num($query) && $arreglo['fecha_fin']== ''){

            if($arreglo['horas_diferencia'] < $horas){
                $edita=update('tr_bitacora_sesion','fecha_fin=now()','bitacora_sesion_id='.$arreglo['bitacora_sesion_id']);        
            }else{
                $edita=update('tr_bitacora_sesion','fecha_fin= DATE_ADD("'.$arreglo['fecha_inicio'].'", INTERVAL 1 HOUR)','bitacora_sesion_id='.$arreglo['bitacora_sesion_id']);
            }

          }

            $inserta=inserta('tr_bitacora_sesion','usuario_id, fecha_inicio, ip,ventana, dispositivo, navegador',
            ''.$id_usuario.',now(),"'.$ip_publica.'", "'.$ventana.'","'.$dispositivo.'","'.$navegador.'"');
          
          

        if($inserta){
            self::cambiar_estado($id_usuario, 1);
            return 1;
        }
        else
          return 0;
  
    
    }

    public static function finaliza_sesion($id_usuario){

        $arreglo_alumno=arreglo(query('SELECT max(bitacora_sesion_id) as bitacora_sesion from tr_bitacora_sesion where usuario_id='.$id_usuario));        
        $edita = update('tr_bitacora_sesion',
        'fecha_fin = now()',
        'bitacora_sesion_id = '.$arreglo_alumno['bitacora_sesion']);

        self::cambiar_estado($id_usuario, 0);

        return $edita;
    }

    



 }