<?php
include '../../jwt.php';
include "../../vendor/codigo_qr/phpqrcode/qrlib.php";
require('../../vendor/FPDF/fpdf.php');

class PDF extends FPDF
{
    function Hoja1()
    {
        global $arreglo_persona;
        global $foto;
        $this->Image($GLOBALS['url_front'] . 'assets/credencial/frente.jpeg', 0, 0, 54, 86);
        $infoPersona = getInfoPersona();
        if($infoPersona['url_foto']){
            $this->Image($GLOBALS['url_front_assets'] . $infoPersona['url_foto'], 4, 12, 21, 28);
        }
    }

    function Hoja2()
    {
        global $arreglo_persona;
        
        $this->Image($GLOBALS['url_front'] . 'assets/credencial/atras.jpeg', 0, 0, 54, 86);
        
        $año_actual = date("y");
        global $id;
        global $nivel_estudios_id;

//         $dir = '../../../assets/qr/';
// if (!file_exists($dir)) {
//     mkdir($dir);
// }

$filename = 'qr.png';
$tamanio = 5;
$level = 'M';
$frameSize = 1;
// $urlRedirect = $GLOBALS['url_front_assets'].'apiEstudy/admin/tramites/datosAlumno.php';
// $urlRedirect .= '?alumno_id=' . $id  . '&nivel_estudios_id=' . $nivel_estudios_id;

// QRcode::png('Hola mundo', $filename, $level, $tamanio, $frameSize);
// // $pdf->SetXY(30, 140);

//     $this->SetXY(12, 28);
//     $this->Image($filename, 16.7, 34.5, 20.4, 20.4);
    }
}


$pdf = new PDF('P', 'mm', array(54, 86));
$pdf->SetAutoPageBreak(true, 5);

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Hoja1();
$pdf->AddPage();
$pdf->Hoja2();

$pdf->Output();

function getInfoPersona(){
    $usuarioId = $_GET['usuarioId'];
    include '../../administrador/personas/class/personas.class.php';
    $personaClass = new Personas();
    $infoPersona = $personaClass->consultaEspPersona($usuarioId);
    return $infoPersona;
}