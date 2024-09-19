<?php
require_once 'config/db.php';
require_once 'vendor/autoload.php';

class OAuth
{
    public static function signInZoomOauth($account_id, $client_id, $client_secret)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://zoom.us/oauth/token",
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => "grant_type=account_credentials&account_id=".$account_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array(
                "Host: zoom.us",
                'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret)
            ),
        ));
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($http_code == 200) {
            $json = json_decode($response, true);
            $access_token = $json['access_token'];
            return $access_token;
        } else {
            echo 'Error al obtener el access_token: ' . $response;
            exit();
        }
    }
}
