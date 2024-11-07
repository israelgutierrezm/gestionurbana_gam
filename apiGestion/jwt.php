<?php

require 'config/db.php';
require 'vendor/autoload.php';

use Firebase\JWT\JWT;

class Auth
{
    private static $secret_key = 'QfDtP4WnrY';
    private static $encrypt = ['HS256'];
    private static $tiempoSesion = 24;

    public static function SignIn($usuario)
    {
        if (
            $usuario['usuario_id'] == '' ||
            $usuario['nombre'] == '' ||
            $usuario['usuario'] == ''
        ) {
            throw new Exception("incorrect params.");
        }

        $time = time();
        $aud = self::aud();
        $token = array(
            // 'exp' => $time + 5, //PRUEBAS 5 SEGUNDOS
            'exp' => $time + (60 * 60) * self::$tiempoSesion,
            'aud' => $aud,
            'data' => [ // información del usuario
                'usuario_id' => $usuario['usuario_id'],
                'nombre' => $usuario['nombre'],
                'aPaterno' => $usuario['ap_pat'],
                'aMaterno' => $usuario['ap_mat'],
                'usuario' => $usuario['usuario'],
                'rol_id' => $usuario['rol_id']
            ]
        );

        return JWT::encode($token, self::$secret_key);
    }

    public static function getDataJWT($jwt)
    {

        $userData = JWT::decode(
            $jwt,
            self::$secret_key,
            self::$encrypt
        )->data;

        return $userData;
    }

    private static function aud()
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
}
