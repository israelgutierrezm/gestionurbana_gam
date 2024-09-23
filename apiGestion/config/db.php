<?php

/*
 * Emmanuel Isaias Zamora Rivera
 * 2013
 */

$GLOBALS['produccion'] = 0;
$connection = null;

if ($GLOBALS['produccion'] == 1) {
    require_once "conf.php";
    require_once "global.php";
} else {
    require_once "conf_debug.php";
    require_once "global_debug.php";
}

function db($modulo)
{
    $GLOBALS['mysqldb_procomur'] = $GLOBALS["db_datosGenerales"];
    switch ($modulo) {
        case "controlTrabajo":
                $GLOBALS['mysqldb_procomur'] = $GLOBALS["db_controlTrabajo"];
                break;
    }
}

function conecta($tipo)
{
    $server = $GLOBALS['ip'];
    $port = $GLOBALS['port'];
    $db = $GLOBALS['mysqldb_procomur'];
    switch ($tipo) {
        case 1: //case que permite solo seleccionar
            $user = "";
            $pass = "";
            break;
        case 2: //case que permite actualizar e insertar, pero no borrar
            $user = "";
            $pass = "";
            break;
        case 3: //case que permite modificar todos los datos (insert, update, delete)
            $user = $GLOBALS['user_db'];
            $pass = $GLOBALS['pass_db'];
            break;
        default: //nada
            $user = "";
            $pass = "";
            break;
    }

    global $connection;
    $connection = mysqli_connect($server, $user, $pass, $db);

    if (mysqli_connect_errno()) {
        echo "Error de base de datos. <br>Intente m&aacute;s tarde o consulte al administrador." . mysqli_connect_error();
    } else {
// mysqli_query($connection, "SET NAMES 'utf8'");
        if (!mysqli_set_charset($connection, "utf8")) {
            printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($connection));
            exit();
        }
    }
}

function cierraConexionDb()
{
    global $connection;
    if (!empty($connection)) {
        mysqli_close($connection);
    }
}

function replace($tabla, $cols, $values)
{
    global $connection;
    if (empty($connection)) {
        conecta(3);
    }
    $ins = "replace into $tabla ($cols) values ($values);";
    $res = mysqli_query($connection, $ins) or die(mysqli_error($connection));
    return $res;

}

function inserta($tabla, $cols, $values)
{
    global $connection;
    if (empty($connection)) {
        conecta(3);
    }
    $ins = "insert into $tabla ($cols) values ($values);";
    $res = mysqli_query($connection, $ins) or die(mysqli_error($connection));
    return $res;

}

function inserta_last_id($tabla, $cols, $values,$show = 0)
{
    global $connection;
    if (empty($connection)) {
        conecta(3);
    }
    $ins = "insert into $tabla ($cols) values ($values);";
    if($show === 1){
        echo $ins;
    }
    mysqli_query($connection, $ins) or die(mysqli_error($connection));
    $last_id = mysqli_insert_id($connection);
    return $last_id;
}

function select($campos, $tabla, $cond)
{
    global $connection;
    if (empty($connection)) {
        conecta(3);
    }
    $sel = 'select ' . $campos . ' from ' . $tabla . ' where ' . $cond . '';
    $res = mysqli_query($connection, $sel) or die(mysqli_error($connection));
    return $res;
}

function update($tabla, $valor, $cond)
{
    global $connection;
    if (empty($connection)) {
        conecta(3);
    }
    $upd = "update $tabla set $valor where $cond;";
    $res = mysqli_query($connection, $upd) or die(json_encode(mysqli_error($connection)));
    return $res;
}

function delete($tabla, $cond)
{
    global $connection;
    if (empty($connection)) {
        conecta(3);
    }
    $del = "delete from $tabla where $cond;";
    $res = mysqli_query($connection, $del) or die(json_encode(mysqli_error($connection)));
    return $res;
}

function num($query)
{
    $res = mysqli_num_rows($query);
    return $res;
}

function arreglo($query)
{
    $res = mysqli_fetch_array($query, 1);
    return $res;
}

function escape_cara($query)
{

    // global $connection;
    // if (empty($connection)) {
    //     conecta(3);
    // }
    // $res = mysqli_real_escape_string($connection, $query);
    // return $res;
    if (function_exists('mb_ereg_replace')) {
        return mb_ereg_replace('[\x00\x0A\x0D\x1A\x22\x27\x5C]', '\\\0', $query);
    } else {
        return preg_replace('~[\x00\x0A\x0D\x1A\x22\x27\x5C]~u', '\\\$0', $query);
    }
    //TODO añadir condicional y parametro por si se empleará el valor por verificar en un query con LIKE, falta escapar el _ y % con 25 y 5F
    //          if (function_exists('mb_ereg_replace')) {
    // return mb_ereg_replace('[\x00\x0A\x0D\x1A\x22\x25\x27\x5C\x5F]', '\\\0', $query);
    // } else {
    // return preg_replace('~[\x00\x0A\x0D\x1A\x22\x25\x27\x5C\x5F]~u', '\\\$0', $query);
    // }

}

function error_base()
{
//$res = mysqli_error();
    //return $res;
}

function row($query)
{
    $res = mysqli_fetch_row($query);
    return $res;
}

function query($query,$show=0)
{
    global $connection;
    if (empty($connection)) {
        conecta(3);
    }
    if($show == 1){
        echo($query);
    }
    $res = mysqli_query($connection, $query) or die(json_encode(mysqli_error($connection)));
    return $res;
}
