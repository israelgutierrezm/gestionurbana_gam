<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('X-Frame-Options: SAMEORIGIN');
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");

header("Content-Type: application/json; charset=UTF-8");


function errHandle($errNo, $errStr, $errFile, $errLine) {
    $msg = "$errStr in $errFile on line $errLine";
    if ($errNo == E_NOTICE || $errNo == E_WARNING) {
        throw new ErrorException($msg, $errNo);
    } else {
        echo $msg;
    }
}

set_error_handler('errHandle');