<?php
include '../../admin/class/persona.class.php';


class Usuario extends Persona { 
    

    public static function consulta_usuario($id_persona,$id_rol){
        $query_usuario = query('SELECT u.usuario_id, u.usuario, p.persona_id, nombre, primer_apellido, segundo_apellido, curp, p.email,
            usuario, u.url_perfil, sexo_id, rfc, fecha_nacimiento,color,celular
            FROM personas p
            join inter_persona_usuario_rol ipu on p.persona_id = ipu.persona_id
            join usuarios u on u.usuario_id = ipu.usuario_id
            WHERE ipu.persona_id ='.$id_persona.' and ipu.rol_id = '.$id_rol.' and u.estatus = 1');

        if(num($query_usuario)){
            $arreglo = arreglo($query_usuario);
        }else{
            $arreglo = null;
        }

        return $arreglo;
    }

    
    

    public static function crea_usuario($nombre,$primer_apellido,$segundo_apellido,$curp,$email,$celular,$rfc,$fecha_nacimiento, $usuario,$password, $color,$id_rol){

        $existe_usuario = self::valida_usuario($curp,$email,null);

        if(!$existe_usuario){
            $inserta_persona = self::inserta_persona($nombre,$primer_apellido,$segundo_apellido,$curp,$rfc,$fecha_nacimiento,$email,$celular,1);

            if($inserta_persona){
          

                $res_usuario_rol = self::crear_usuario_rol($usuario,$password,$color,$inserta_persona,$id_rol);
                return $res_usuario_rol;        

            }else{
                //no se pudo insertar la persona
                return array("status" => 0, "msg" => "No se pudo insertar la persona");    
            }
            
        }else{
            //usuario ya existe
            if($existe_usuario == 1)
                $msg = "CURP ya existe";
            if($existe_usuario == 2)
                $msg = "Correo ya existe";

            return array("status" => 0, "tipo_error"=> 1, "msg"=> $msg);
        }

        
    }

    public static function edita_usuario($usuario,$password,$color,$id_usuario){

        $encriptada =new Encriptacion();
        $password=$encriptada::hash($password);   

        $edita = query('update usuarios  
        set usuario = "'.$usuario.'",
        password = "'.$password.'",
        color = "'.$color.'"
        where usuario_id  = '.$id_usuario);

        return $edita;
    }
    
    public static function delete_usuario($id_persona){

        $query_usuario_rol = query('select rol_id from inter_persona_usuario_rol where persona_id='.$id_persona.' and estatus=1');
        if(num($query_usuario_rol)){
           while($arreglo_rol = arreglo($query_usuario_rol))
            switch ($arreglo_rol['rol_id']) {
                case 1:
                    // admin    
                $persona_rol = query('update '.$GLOBALS['db_controlEscolar'].'.tr_administrador set estatus = 0 where administrador_id  = '.$id_persona);
                break;
                case 2:
                    //alumno 
                    $persona_rol = query('update '.$GLOBALS['db_controlEscolar'].'.tr_alumno set estatus = 0 where alumno_id  = '.$id_persona);
                break;
                case 3:
                    // docente
                    $persona_rol = query('update '.$GLOBALS['db_controlEscolar'].'.tr_docente set estatus = 0 where docente_id  = '.$id_persona);
                break;   
                case 4:
                    // aspirante
                    $persona_rol = query('update '.$GLOBALS['db_seguimiento'].'.tr_aspirante set estatus = 0 where aspirante_id  = '.$id_persona);
                break;
                case 5:
                    // asesor
                    $persona_rol = query('update '.$GLOBALS['db_seguimiento'].'.tr_asesor set estatus = 0 where asesor_id  = '.$id_persona);
                break;
                case 6:
                    // tutor
                    $persona_rol = query('update '.$GLOBALS['db_seguimiento'].'.tr_tutor set estatus = 0 where tutor_id  = '.$id_persona);
                break;       
                
            }
            if($persona_rol){
                $elimina = query('update usuarios set estatus = 0 where usuario_id  = '.$id_persona);
                
                    if($elimina){
                    $elimina_upur = query('update inter_persona_usuario_rol  
                    set estatus = 0
                    where usuario_id  = '.$id_persona);    
                    if($elimina_upur){
                    $elimina_persona = self::delete_persona($id_persona);
                    }
                }
            }

        }else{
            $elimina_persona = 0;
        }
        


        return $elimina_persona; 
    }
        
} 
