<?php
// include '../../../jwt.php';

class Frentes
{
    public function consultaFrentes()
    {
        $queryPersonas = query('SELECT 
            tf.frente_id, direccion_territorial, colonia, nombre, area, dias_jornada, personal_necesario 
            FROM '.$GLOBALS["db_controlTrabajo"].'.tr_frente  tf
            JOIN '.$GLOBALS["db_controlTrabajo"].'.cat_colonias  cc ON tf.cat_colonia_id = cc.cat_colonia_id
            JOIN '.$GLOBALS["db_controlTrabajo"].'.cat_direccion_territorial  cdt ON tf.cat_direccion_territorial_id = cdt.cat_direccion_territorial_id 

            WHERE tf.estatus = 1');
        while ($persona = arreglo($queryPersonas)) {
            $arregloPersonas[] = $persona;
        }
        return $arregloPersonas;
    }

}
