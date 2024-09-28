<?php
// include '../../../jwt.php';

class Frentes
{
    public function consultaFrentes()
    {
        $queryPersonas = query('SELECT 
            tf.frente_id, tf.cat_direccion_territorial_id, direccion_territorial, tf.cat_colonia_id, colonia, nombre, area, dias_jornada, personal_necesario ,     
            (
                SELECT GROUP_CONCAT(itf.cat_tipo_espacio_frente_id SEPARATOR ",")
                FROM '.$GLOBALS["db_controlTrabajo"].'.inter_frente_tipoespaciofrente itf
                WHERE itf.frente_id = tf.frente_id
            ) AS tipos_espacios_ids
            FROM '.$GLOBALS["db_controlTrabajo"].'.tr_frente  tf
            JOIN '.$GLOBALS["db_controlTrabajo"].'.cat_colonias  cc ON tf.cat_colonia_id = cc.cat_colonia_id
            JOIN '.$GLOBALS["db_controlTrabajo"].'.cat_direccion_territorial  cdt ON tf.cat_direccion_territorial_id = cdt.cat_direccion_territorial_id 
            WHERE tf.estatus = 1');
        while ($persona = arreglo($queryPersonas)) {
            $arregloPersonas[] = $persona;
        }
        return $arregloPersonas;
    }

    public function editaFrente($datosFrente)
    {
        $editaFrente = update(
            $GLOBALS["db_controlTrabajo"].'.tr_frente',
            'cat_direccion_territorial_id = ' . $datosFrente['cat_direccion_territorial_id'] . ',
            area = ' . $datosFrente['area'] . ',
            nombre = "' . $datosFrente['nombre'] . '",
            dias_jornada = ' . $datosFrente['dias_jornada'] . ',
            personal_necesario = "' . $datosFrente['personal_necesario'] . '"',
            'frente_id =' . $datosFrente['frenteId']
        );

    return $editaFrente;

    }

}
