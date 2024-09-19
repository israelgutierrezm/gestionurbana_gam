<?php

require_once 'config/db.php';
require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;

class Auth
{
    private static $secret_key = 'R1ch4rdOnE';
    private static $encrypt = ['HS256'];
    private static $aud = null;

    public static function SignIn($usuario)
    {
        if (
            $usuario['persona_id'] == '' ||
            $usuario['usuario_id'] == '' ||
            $usuario['nombre'] == '' ||
            //$usuario['primer_apellido'] == '' || // se comentó pues al hacer el vaciado de datos y no existir el apellido paterno lanza la excepcion
            $usuario['rol_id'] == '' ||
            $usuario['rol'] == '' ||
            $usuario['usuario'] == '' ||
            $usuario['tiempo_sesion'] == ''
        ) {
            throw new Exception("incorrect params.");
        }

        $time = time();

        $token = array(
            'exp' => $time + (60 * 60) * $usuario['tiempo_sesion'],
            'aud' => self::Aud(),
            'data' => [ // información del usuario
                'id' => $usuario['persona_id'],
                'id_usuario' => $usuario['usuario_id'],
                'aNombre' => $usuario['nombre'],
                'aPaterno' => $usuario['primer_apellido'],
                'aMaterno' => $usuario['segundo_apellido'],
                'id_rol' => $usuario['rol_id'],
                'id_institucion' => 1,
                'email' => $usuario['email'],
                'usuario' => $usuario['usuario'],
                'tiempo_sesion' => $usuario['tiempo_sesion'],
                'externo' => $usuario['externo']
            ]
        );

        return JWT::encode($token, self::$secret_key);
    }

    public static function SignInZoom($api_key, $api_secret, $tiempo_sesion)
    {

        if (
            !$api_key || !$api_secret
        ) {
            throw new Exception("incorrect params.");
        }

        $time = time();

        $token = array(
            'iss' => $api_key,
            'exp' => $time + (60 * 60) * $tiempo_sesion,
            'aud' => self::Aud()

        );

        return JWT::encode($token, $api_secret);
    }

    public static function Check($token)
    {
        if (empty($token)) {
            throw new Exception("Invalid token supplied.");
        }

        $decode = JWT::decode(
            $token,
            self::$secret_key,
            self::$encrypt
        );

        if ($decode->aud !== self::Aud()) {
            throw new Exception("Invalid user logged in.");
        }
    }

    public static function GetData($token)
    {

        $userData = JWT::decode(
            $token,
            self::$secret_key,
            self::$encrypt
        )->data;

        if (!self::verifyUserIdentity($userData)) {
            throw new Exception("Invalid user attempt. User doesnt exists.");
        }
        if (!self::verifyUserPermisions($userData)) {
            throw new Exception("Invalid access. User permisions denied. ");
        }
        return $userData;
    }

    private static function Aud()
    {
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

    /* --- Funciones de autorización a servicios --- */

    //verifica que el usuario exista en la base de datos de la universidad a la que está ingresando.
    private static function verifyUserIdentity($userData)
    {
        $statement = 'SELECT u.usuario_id, usuario FROM ' . $GLOBALS["db_datosGenerales"] . '.usuarios u WHERE u.usuario = "' . $userData->usuario . '" AND estatus = 1';
        $userResult = query($statement);

        if (!num($userResult)) {
            return false;
        } else {
            $arrayResult = arreglo($userResult);
            return ($arrayResult['usuario_id'] === $userData->id_usuario) ? true : false;
        }
    }

    //verifica que el usuario tenga un rol con permisos para consumir el servicio que está consultando.
    private static function verifyUserPermisions($userData)
    {
        //TODO revisar que el usuario tenga un rol con permisos para consumir el servicio que está consultando
        //es necesario hacer la lista de servicios y cual queda asignado a cada rol

        // $statement = query('SELECT
        // u.usuario_id,
        // cr.rol_id, cr.rol, usuario
        // FROM usuarios u
        // JOIN inter_persona_usuario_rol ur on ur.usuario_id = u.usuario_id
        // JOIN cat_rol cr ON cr.rol_id = ur.rol_id
        // where u.usuario = "'.$userData->id_usuario.'" and ur.rol_id = ' . $userData->id_rol .'
        //  and u.estatus = 1');
        //$userResult = query($statement);
        //if(){}
        return true;
    }
}
