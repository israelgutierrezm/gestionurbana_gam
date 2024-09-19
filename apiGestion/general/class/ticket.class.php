<?php

 class Ticket{

    public static function ver_ticket_bloqueo($id_persona,$id_rol){
        $query = query('select ticket_id,asunto_ticket,descripcion_ticket from '.$GLOBALS['db_controlEscolar'].'.tr_ticket tt where persona_id ='.$id_persona.' and rol_id = '.$id_rol.' and tt.estatus = 1 and tipo_ticket_id in (4,5,7) order by tt.ticket_id desc');
        if(num($query)){
            $arreglo = arreglo($query);
        }else{
            $arreglo = null;
        }
        return $arreglo;
    }


    public static function crea_notificacion($id_area,$id_persona,$id_rol,$id_tipo_ticket,$descripcion_ticket){

        switch($id_tipo_ticket){
            case 3:
                    $asunto_ticket = 'Observación en tu expediente';
                break;
            case 4:
                    $asunto_ticket = 'Situación de Baja temporal';
                break;
            case 5:
                    $asunto_ticket = 'Baja definitiva';
            break;
            case 7:
                    $asunto_ticket = 'Bloqueado';
            break;
            default:
                    $asunto_ticket = '';
        }

        $crea_ticket = inserta($GLOBALS['db_controlEscolar'].'.tr_ticket','asunto_ticket, descripcion_ticket,visto,area_id,persona_id, rol_id, tipo_ticket_id, estatus_ticket_id, estatus'
    ,'"'.$asunto_ticket.'","'.$descripcion_ticket.'",0,'.$id_area.','.$id_persona.','.$id_rol.','.$id_tipo_ticket.',4,1');


        return $crea_ticket;
    }
    
    public static function crea_solo_lectura($asunto_ticket,$descripcion_ticket,$id_area,$id_persona,$id_rol,$id_tipo_ticket){

        
        $crea_ticket = inserta($GLOBALS['db_controlEscolar'].'tr_ticket','asunto_ticket, descripcion_ticket,visto,area_id,persona_id, rol_id, tipo_ticket_id, estatus_ticket_id, estatus'
    ,'"'.$asunto_ticket.'","'.$descripcion_ticket.'",0,'.$id_area.','.$id_persona.','.$id_rol.','.$id_tipo_ticket.',4,1');


        return $crea_ticket;
    }



 }