<?php
include '../../jwt.php';
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    include '../../administrador/personas/class/personas.class.php';
    include '../../reportes/credencial/class/credencial.class.php';
    $usuarioId = $_GET['usuarioId'];
    $personaClass = new Personas();
    $arregloUsuario = $personaClass->consultaEspPersona($usuarioId);
    $credencialClass = new Credencial($arregloUsuario);
    $credencialClass->generaCredencial();
    // print_r($infoUsuario);
}