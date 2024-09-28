<?php
include '../../jwt.php';
include '../../headers.php';

try {

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        foreach ($_POST as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        // $usuario = Auth::GetData(
        //     $jwt  
        // );
        $frenteId = inserta_last_id(
            $GLOBALS["db_controlTrabajo"].'.tr_frente', 
            'cat_direccion_territorial_id, cat_colonia_id, nombre, area, dias_jornada, personal_necesario, estatus', 
            ''.$cat_direccion_territorial_id.', '.$cat_colonia_id.', "'.$nombre.'", '.$area.', '.$dias_jornada.', '.$personal_necesario.', 1'
        );

        if ($frenteId) {
            $tiposEspaciosFrentesArray = explode(',', $tiposEspaciosFrentes);
            foreach ($tiposEspaciosFrentesArray as $cat_tipo_espacio_frente_id) {
                $cat_tipo_espacio_frente_id = trim($cat_tipo_espacio_frente_id);            
                $insertaTipoEspacio = inserta(
                    $GLOBALS["db_controlTrabajo"].'.inter_frente_tipoespaciofrente', 
                    'frente_id, cat_tipo_espacio_frente_id, estatus', 
                    ''.$frenteId.', '.$cat_tipo_espacio_frente_id.', 1'
                );
            }
        }
        
        $json = array("estatus" => 1, "msg" => "Se guardó la información correctamente.");
    } else {
        $json = array("estatus" => 0, "msg" => "Método no aceptado");
    }
    echo json_encode($json);
} catch (Exception $e) {
    $json = array("estatus" => 0, "msg" =>  $e->getMessage());
    echo json_encode($json);
}



