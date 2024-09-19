<?php
class bitacoraAccion{

    public static $query;


    public static function insertaAccion($id_accion,$id_usuario){

        $inserta = inserta('tr_bitacora_accion','accion_id, usuario_id, fecha_inicio, fecha_fin',
        ''.$id_accion.', '.$id_usuario.', now(), now()');

        return $inserta;

    }









}



?>