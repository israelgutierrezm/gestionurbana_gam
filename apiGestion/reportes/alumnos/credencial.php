<?php
include '../../jwt.php';
include "../../vendor/codigo_qr/phpqrcode/qrlib.php";
require('../../vendor/FPDF/fpdf.php');

/* $jwt = $_GET['jwt'];

    $usuario = Auth::GetData(
        $jwt  
    );*/

class PDF extends FPDF
{
    // Cabecera de página
    function Hoja1()
    {
        $id = $_GET['id'];
// $id = 13188;
$arreglo_persona = arreglo(query('SELECT p.persona_id, p.nombre, p.primer_apellido, p.segundo_apellido, p.estatus, u.url_perfil, tc.carrera, tpe.rvoe, ta.clave_alumno
from personas p
join inter_persona_usuario_rol ipur on ipur.persona_id = p.persona_id 
join usuarios u on ipur.usuario_id = u.usuario_id
join '.$GLOBALS['db_controlEscolar'].'.tr_alumno ta on ta.alumno_id = ipur.persona_id 
        left join '.$GLOBALS['db_controlEscolar'].'.inter_alumno_plan iap on iap.alumno_id = ta.alumno_id 
        left join .inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id 
        left join .tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id 
        left join .tr_carrera tc on tc.carrera_id = tpe.carrera_id 
WHERE p.persona_id = ' . $id . ' and p.estatus=1'));

$this->Image($GLOBALS['url_front'] . '/assets/images/credencial/bg_credencial_1_frente.jpg',0, 0, 54, 86);								
$this->SetFont('Arial', '', 5);
$this->Image($GLOBALS['url_front'] . 'assets/images/logo.png', 23, 5, 8, 10);
if ($arreglo_persona['url_perfil']) {
    $this->Image($GLOBALS['url_front_assets'].$arreglo_persona['url_perfil'],15, 16.5, 24, 28.5);
} else {
    $this->MultiCell(0, 3, '', 0, 'C');
}
$this->SetXY(5, 48);
$this->SetTextColor(255,255,255);
$this->MultiCell(0, 3, utf8_decode(strtoupper($arreglo_persona['segundo_apellido'] . ' ' . $arreglo_persona['primer_apellido'] . ' ' . $arreglo_persona['nombre'])), 0, 'C');
$this->SetXY(5, 54);
$this->MultiCell(0, 3, 'MATRICULA: '.$arreglo_persona['clave_alumno'], 0, 'C');
$this->SetXY(5, 70);
$this->SetTextColor(0,0,0);
$this->MultiCell(0,3, $arreglo_persona['carrera'], 0, 'C');
$this->SetXY(5, 80);
$this->MultiCell(0, 0, 'RVOE: '.$arreglo_persona['rvoe'], 0, 'C');
    }

    function Hoja2()
    {
        $id = $_GET['id'];
        // $id = 13188;
        $arreglo_persona = arreglo(query('SELECT p.persona_id, p.nombre, p.primer_apellido, p.segundo_apellido, p.estatus, u.url_perfil, tc.carrera, tpe.rvoe, ta.clave_alumno, cc.ciclo_desc
        from personas p
        join inter_persona_usuario_rol ipur on ipur.persona_id = p.persona_id 
        join usuarios u on ipur.usuario_id = u.usuario_id
        join '.$GLOBALS['db_controlEscolar'].'.tr_alumno ta on ta.alumno_id = ipur.persona_id 
                left join '.$GLOBALS['db_controlEscolar'].'.inter_alumno_plan iap on iap.alumno_id = ta.alumno_id 
                left join .inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id 
                left join .tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id 
                left join .tr_carrera tc on tc.carrera_id = tpe.carrera_id 
                left join '.$GLOBALS['db_controlEscolar'].'.tr_historial th on th.alumno_id = ta.alumno_id 
                left join '.$GLOBALS['db_controlEscolar'].'.cat_ciclo cc on cc.ciclo_id = th.ciclo_id 
        WHERE p.persona_id = ' . $id . ' and p.estatus=1'));
        $this->Image($GLOBALS['url_front'] . '/assets/images/credencial/bg_credencial_1_vuelta.jpg',0, 0, 54, 86);								
        // $this->SetFont('Arial', '', 8);
$this->Image($GLOBALS['url_front'] . 'assets/images/logo.png', 23, 5, 8, 10);
        // $this->Image('assets/images/credencial/firma.png',0, 0, 10, 15);								
$this->SetXY(10, 20);
$this->MultiCell(0, 3, 'AUTORIZA', 0, 'C');
$this->SetXY(10, 40);
$this->MultiCell(0, 3, 'NOMBRE DE LA PERSONA QUE AUTORIZA', 0, 'C');
$this->SetXY(10, 70);
$this->SetTextColor(255,255,255);
$this->MultiCell(0, 3, 'CICLO ESCOLAR:'.$arreglo_persona['ciclo_desc'], 0, 'C');
    }
}


$pdf = new PDF('P', 'mm', array(54,86));
$pdf->SetAutoPageBreak(true,5); 

$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Hoja1();
$pdf->AddPage();
$pdf->Hoja2();

$pdf->Output();
?>