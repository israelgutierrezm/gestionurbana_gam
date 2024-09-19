<?php
include '../../jwt.php';
include "../../vendor/codigo_qr/phpqrcode/qrlib.php";
require('../../vendor/FPDF/fpdf.php');

$id_asignatura_grupo = $_GET['id_asignatura_grupo'];
// $id_actividad = $_GET['id_actividad'];
/* $jwt = $_GET['jwt'];

    $usuario = Auth::GetData(
        $jwt  
    );*/

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {

        global $arreglo_cabecera;
        $id_asignatura_grupo = $_GET['id_asignatura_grupo'];

        $arreglo_cabecera = arreglo(query('select tpe.rvoe, iag.asignatura_grupo_id, cc.ciclo_desc,
coj.orden_jerarquico_descripcion, tg.nombre_grupo, DATE_FORMAT(now(), "%d / %m / %Y") as dia ,
ca.asignatura_clave, tc.carrera, ca.asignatura, p.nombre, p.primer_apellido, p.segundo_apellido,
coj.orden_jerarquico, cne.url_imagen
from '.$GLOBALS['db_controlEscolar'].'.inter_asignatura_grupo iag
join '.$GLOBALS['db_controlEscolar'].'.tr_grupo tg on tg.grupo_id = iag.grupo_id
join '.$GLOBALS['db_controlEscolar'].'.cat_ciclo cc on cc.ciclo_id = tg.ciclo_id
join '.$GLOBALS['db_datosGenerales'].'.inter_plan_orden ipo on ipo.plan_orden_id = tg.plan_orden_id
join '.$GLOBALS['db_datosGenerales'].'.tr_plan_estudios tpe on tpe.plan_estudio_id  = ipo.plan_estudio_id
join '.$GLOBALS['db_datosGenerales'].'.tr_carrera tc on tc.carrera_id = tpe.carrera_id
join '.$GLOBALS['db_datosGenerales'].'.cat_nivel_estudios cne ON tc.nivel_estudios_id = cne.nivel_estudios_id 
join '.$GLOBALS['db_datosGenerales'].'.cat_orden_jerarquico coj on coj.orden_jerarquico_id = ipo.orden_jerarquico_id
join '.$GLOBALS['db_datosGenerales'].'.inter_orden_asignatura ioa on ioa.orden_asignatura_id = iag.orden_asignatura_id
join '.$GLOBALS['db_datosGenerales'].'.cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id 
join '.$GLOBALS['db_controlEscolar'].'.inter_docente_asignatura_grupo idag on idag.asignatura_grupo_id = iag.asignatura_grupo_id
join '.$GLOBALS['db_controlEscolar'].'.tr_docente td on td.docente_id = idag.docente_id
join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = td.docente_id 
where iag.asignatura_grupo_id = '.$id_asignatura_grupo));

        //$this->Image('UMO.gif' ,145 , 7, 50, 30);
        $this->SetFont('Arial', 'B', 30);
        // Movernos a la derecha
        // $this->Cell(70,10,'Maqueta FPDF', 0, 0,'L');
        // Salto de línea
        $this->Ln(20);

        if(!isset($arreglo_cabecera['url_imagen']) || $arreglo_cabecera['url_imagen']==null || $arreglo_cabecera['url_imagen']==''){
            $this->Image($GLOBALS['url_front'].'assets/images/logo.png', 15, 10, 35, 15, 'png');
            }else{
            $this->Image($GLOBALS['url_front_assets'].$arreglo_cabecera['url_imagen'], 15, 10, 35, 15, '');}     
        }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        // $this->Cell(0,5,utf8_decode('Page ').$this->PageNo().'/{nb}',0,0,'C');
    }
}


$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();


$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(70, 13);
$pdf->Cell(70, 10, 'INSTITUTO '.$GLOBALS['nombre_institucion'], 0, 1, 'C', 0);
$pdf->SetFont('Arial', '', 12);
$pdf->SetXY(70, 26);
$pdf->Cell(70, 10, 'Lista de Asistencia', 0, 1, 'C', 0);

$marco = 15;
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 8);
$pdf->SetX($marco);
$pdf->Cell(23, 6, 'LICENCIATURA :', 0, 0, 'L', 0);
$pdf->Cell(70, 6, utf8_decode(strtoupper( $arreglo_cabecera['carrera'])), 0, 0, 'L', 0);
$pdf->Cell(10, 6, "NIVEL:", 0, 0, 'L', 0);
$pdf->Cell(8, 6, utf8_decode(strtoupper($arreglo_cabecera['orden_jerarquico'])), 0, 1, 'L', 0);

$pdf->SetX($marco);
$pdf->Cell(13, 6, 'GRUPO :', 0, 0, 'L', 0);
$pdf->Cell(80, 6, utf8_decode(strtoupper($arreglo_cabecera['nombre_grupo'])), 0, 0, 'L', 0);
$pdf->Cell(15, 6, "MODULO:", 0, 0, 'L', 0);
$pdf->Cell(50, 6, "", 0, 1, 'L', 0);

$pdf->SetX($marco);
$pdf->Cell(12, 6, 'TURNO :', 0, 0, 'L', 0);
$pdf->Cell(81, 6, "", 0, 0, 'L', 0);
$pdf->Cell(25, 6, "CICLO ESCOLAR :", 0, 0, 'L', 0);
$pdf->Cell(50, 6, utf8_decode(strtoupper($arreglo_cabecera['ciclo_desc'])), 0, 1, 'L', 0);

$pdf->SetX($marco);
$pdf->Cell(18, 5, 'PROFESOR:', 0, 0, 'L', 0);


$nombre= strlen($arreglo_cabecera['nombre']);
$apellido1= strlen($arreglo_cabecera['primer_apellido']);
$apellido2= strlen($arreglo_cabecera['segundo_apellido']);

$num_nombre = $nombre+$apellido1+$apellido2;
if($num_nombre > 40){
 $pdf->SetFont('Arial', '', 7);
}elseif($num_nombre > 45){
    $pdf->SetFont('Arial', '', 6);
}
$pdf->Cell(75, 5,strtoupper(utf8_decode($arreglo_cabecera['nombre'].' '.$arreglo_cabecera['primer_apellido'].' '.$arreglo_cabecera['segundo_apellido'])), 0, 0, 'L', 0);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(37, 5, "PERIODO DE ASISTENCIA:", 0, 0, 'L', 0);
$pdf->Cell(50, 5, "Prueba", 0, 1, 'L', 0);

$pdf->SetX($marco);
$pdf->Ln(1);
$pdf->SetX(15);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(15, 6, 'MATERIA :', 0, 0, 'C', 0);
$pdf->Cell(70, 6, strtoupper(utf8_decode($arreglo_cabecera['asignatura'])), 0, 0, 'L', 0);

$pdf->Ln(13);
$pdf->SetX(7);
$pdf->Cell(10, 15, 'N/L', 1, 0, 'C', 0);
$pdf->Cell(80, 15, utf8_decode('Nombre'), 1, 0, 'C', 0);

$row_asistencias = 15;
for ($i=0; $i < $row_asistencias; $i++) { 
    $pdf->Cell(4.5, 15, "",1, 0, 'C', 0);
}
$pdf->Cell(40, 15, utf8_decode('EVALUACIÓN CONTINUA'), 1, 1, 'C', 0);
$pdf->SetXY(97,73);
$pdf->Cell(67.5, 5, utf8_decode('Asistencia'), 1,0, 'C', 0);

$query_alumnos = query('select tm.alumno_id, p.nombre, p.primer_apellido, p.segundo_apellido,clave_alumno
from '.$GLOBALS['db_learning'].'.tr_materia tm 
join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = tm.alumno_id
join '.$GLOBALS['db_controlEscolar'].'.tr_alumno ta on ta.alumno_id = p.persona_id
where tm.asignatura_grupo_id='.$id_asignatura_grupo.'
and tm.estatus=1 and ta.situacion_alumno_id = 1 and p.estatus=1 and ta.estatus=1');



$y_cell = 86;
$id_alumno = 1;
$actividad_nombre = array();
// echo num($query_alumnos);
while ($arreglo_alumno = arreglo($query_alumnos)) {
    $pdf->SetXY(7, 7 + $y_cell);
    $pdf->Cell(10, 7, $id_alumno, 1, 0, 'C', 0);
    $pdf->Cell(80, 7, utf8_decode($arreglo_alumno['nombre'] . ' ' . $arreglo_alumno['primer_apellido'] . ' ' . $arreglo_alumno['segundo_apellido']), 1, 0, 'C', 0);

    for ($i=0; $i < $row_asistencias; $i++){ 
        $pdf->Cell(4.5, 7, "",1, 0, 'C', 0);
    }

    $row_evaluacion = 4;
    for ($i=0; $i < $row_evaluacion; $i++){ 
        $pdf->Cell(10, 7, "",1, 0, 'C', 0);
    }

    $y_cell += 7;
    $id_alumno += 1;
    if ($id_alumno == 25) {
        $y_cell = 45;
        $pdf->AddPage();
    }

}
$pdf->SetXY(87,$y_cell+35);
$pdf->Cell(35, 35, '', 1, 0, 'C', 0);
$pdf->SetXY($marco,$y_cell+90);
$pdf->Cell(90, 6, utf8_decode('Firma del Profesionista.'), 0, 0, 'C', 0);
$pdf->Cell(90, 6, utf8_decode('Firma de Servicios Escolares.'), 0, 0, 'C', 0);
$pdf->line(35, $y_cell+100, 85, $y_cell+100);
$pdf->line(125, $y_cell+100, 175, $y_cell+100);

$pdf->Output();
?>