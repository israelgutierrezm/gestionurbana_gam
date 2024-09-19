<?php
include '../../jwt.php';
include "../../vendor/codigo_qr/phpqrcode/qrlib.php";
require('../../vendor/FPDF/fpdf.php');

// $jwt = $_GET['jwt'];
$id_asignatura_grupo = $_GET['id_asignatura_grupo'];
// $id_actividad = $_GET['id_actividad'];
// $fecha_inicio = $_GET['fecha_inicio'];
// $fecha_fin = $_GET['fecha_fin'];

// $usuario = Auth::GetData(
//     $jwt
// );


// print_r($usuario);
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        global $arreglo_cabecera;
        $id_asignatura_grupo = $_GET['id_asignatura_grupo'];

        $arreglo_cabecera = arreglo(query('select tpe.rvoe, iag.asignatura_grupo_id, cc.ciclo_desc,
    coj.orden_jerarquico_descripcion, coj.orden_jerarquico_id, tg.nombre_grupo, 
    ca.asignatura_clave, tc.carrera, tc.nivel_estudios_id, ca.asignatura, p.nombre, p.primer_apellido, p.segundo_apellido
    from ' . $GLOBALS['db_controlEscolar'] . '.inter_asignatura_grupo iag
    join ' . $GLOBALS['db_controlEscolar'] . '.tr_grupo tg on tg.grupo_id = iag.grupo_id
    join ' . $GLOBALS['db_controlEscolar'] . '.cat_ciclo cc on cc.ciclo_id = tg.ciclo_id
    join ' . $GLOBALS['db_datosGenerales'] . '.inter_plan_orden ipo on ipo.plan_orden_id = tg.plan_orden_id
    join ' . $GLOBALS['db_datosGenerales'] . '.tr_plan_estudios tpe on tpe.plan_estudio_id  = ipo.plan_estudio_id
    join ' . $GLOBALS['db_datosGenerales'] . '.tr_carrera tc on tc.carrera_id = tpe.carrera_id
    join ' . $GLOBALS['db_datosGenerales'] . '.cat_orden_jerarquico coj on coj.orden_jerarquico_id = ipo.orden_jerarquico_id
    join ' . $GLOBALS['db_datosGenerales'] . '.inter_orden_asignatura ioa on ioa.orden_asignatura_id = iag.orden_asignatura_id
    join ' . $GLOBALS['db_datosGenerales'] . '.cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id 
    join ' . $GLOBALS['db_controlEscolar'] . '.inter_docente_asignatura_grupo idag on idag.asignatura_grupo_id = iag.asignatura_grupo_id
    join ' . $GLOBALS['db_controlEscolar'] . '.tr_docente td on td.docente_id = idag.docente_id
    join ' . $GLOBALS['db_datosGenerales'] . '.personas p on p.persona_id = td.docente_id 
    where iag.estatus=1 and tg.estatus=1 and cc.estatus=1 and ipo.estatus=1 and tpe.estatus=1 and
    tc.estatus=1 and coj.estatus=1 and ioa.estatus=1 and ca.estatus=1 and idag.estatus=1 and td.estatus=1 and p.estatus=1 and iag.asignatura_grupo_id = ' . $id_asignatura_grupo));

        //$this->Image('UMO.gif' ,145 , 7, 50, 30);
        $this->SetFont('Arial', 'B', 30);
        // Movernos a la derecha
        // $this->Cell(70,10,'Maqueta FPDF', 0, 0,'L');
        // Salto de línea
        $this->Ln(20);

        if(!isset($arreglo_cabecera['url_imagen']) || $arreglo_cabecera['url_imagen']==null || $arreglo_cabecera['url_imagen']==''){
            $this->Image($GLOBALS['url_front'].'assets/images/logo.png', 25, 10, 35, 25, 'png');
            }else{
            $this->Image($GLOBALS['url_front_assets'].$arreglo_cabecera['url_imagen'], 25, 10, 35, 25, '');} 
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

$mid_x = $pdf->GetPageWidth() / 2;
$tercera_parte_ancho = ($pdf->GetPageWidth() / 3) - 8;

$nivel_estudios_id = $arreglo_cabecera['nivel_estudios_id'];
$pdf->SetFont('Arial', 'B', 9);
// $pdf->SetXY(0, 13);
$pdf->SetY(13);
$pdf->Cell(0, 10, utf8_decode(mb_strtoupper($GLOBALS['nombre_institucion'])), 0, 1, 'C', 0);

$pdf->SetFont('Arial', '', 9);

$pdf->SetY(18);
$pdf->Cell(0, 10, utf8_decode(mb_strtoupper($arreglo_cabecera['carrera'])), 0, 0, 'C', false);
$pdf->SetY(22);
$pdf->Cell(0, 10, utf8_decode('ACTA DE CALIFICACIONES DE SEMESTRE '.$_GET['semestre']), 0, 0, 'C', false);
$pdf->SetY(26);
$pdf->Cell(0, 10, utf8_decode(mb_strtoupper('ASIGNATURA: ' . $arreglo_cabecera['asignatura'])), 0, 0, 'C', false);

// $pdf->SetFont('Arial', 'B', 6);

$pdf->SetDrawColor(160, 160, 160);

$ancho_columna_grande = ($tercera_parte_ancho * 2) + 10;
$ancho_columna_1 = $ancho_columna_grande / 4;
$ancho_columna_2 = $ancho_columna_1 * 3;
$ancho_columna_3 = ($tercera_parte_ancho / 2) - 6;
$pdf->Ln(10);
$pdf->SetX(12);
$pdf->Cell($tercera_parte_ancho + 5, 7, 'CICLO ESCOLAR', 0, 0, 'L', false);
$pdf->Cell($tercera_parte_ancho + 15, 7, '', 0, 0, 'C', false);
$pdf->Cell($tercera_parte_ancho - 22, 7, 'CLAVE', 0, 0, 'L', false);

$pdf->Ln(5);
$pdf->SetX(12);
$pdf->Cell($tercera_parte_ancho + 5, 7, utf8_decode(isset($_GET['ciclo']) ? $_GET['ciclo'] : $arreglo_cabecera['ciclo_desc']), 0, 0, 'L', false);
$pdf->Cell($tercera_parte_ancho + 15, 7, utf8_decode('           ' . $arreglo_cabecera['nombre_grupo']), 0, 0, 'L', false);
$pdf->Cell($tercera_parte_ancho - 22, 7, utf8_decode(mb_strtoupper($arreglo_cabecera['asignatura_clave'])), 0, 0, 'L', false);

$pdf->Ln(10);
$pdf->SetX(12);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Rect(12, 51, $ancho_columna_grande, 16);
$pdf->Cell($ancho_columna_1, 24, utf8_decode('MATRÍCULA'), 0, 0, 'L', false);
$pdf->Cell($ancho_columna_2, 24, utf8_decode('NOMBRE'), 0, 0, 'L', false);

$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell($tercera_parte_ancho - 12, 8, utf8_decode('CALIFICACIÓN'), 1, 0, 'C', false);
$pdf->SetXY(($tercera_parte_ancho * 2) + 22, 59);
$pdf->Cell($ancho_columna_3, 8, utf8_decode('NÚMERO'), 1, 0, 'C', false);
$pdf->SetX((($tercera_parte_ancho * 2) + 16) + ($tercera_parte_ancho / 2));
$pdf->Cell($ancho_columna_3, 8, utf8_decode('CON LETRA'), 1, 1, 'C', false);

$query_alumnos = query('SELECT tm.alumno_id, p.nombre, p.primer_apellido, p.segundo_apellido, tal.clave_alumno as matricula, tm.calificacion
FROM '.$GLOBALS['db_learning'].'.tr_materia tm 
JOIN '.$GLOBALS['db_datosGenerales'].'.personas p ON p.persona_id = tm.alumno_id
JOIN '.$GLOBALS['db_controlEscolar'].'.tr_alumno tal ON tal.alumno_id = tm.alumno_id
WHERE tm.asignatura_grupo_id = '.$id_asignatura_grupo.'
AND tm.estatus=1 and p.estatus = 1 and tm.tipo_materia_id in (1,2,3) and tal.estatus = 1 order by p.primer_apellido, p.segundo_apellido, p.nombre');
$pdf->SetFont('Arial', '', 9);
$y_pos = 67;
// $ARREGLO_PRUEBA = array_fill(0, 20, array('nombre' => 'MIGUEL ANGEL', 'primer_apellido' => 'gonzález', 'segundo_apellido' => 'espejel', 'calificacion' => 9, 'matricula' => 'abjdnedbf929'));

// print('<pre>'.print_r($arreglo_cabecera, true). '</pre>');

while ($arreglo_alumno = arreglo($query_alumnos)) {
// foreach ($ARREGLO_PRUEBA as $arreglo_alumno) {
    $pdf->SetXY(12, $y_pos);
    $pdf->Rect(12, $y_pos, $ancho_columna_grande, 6);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell($ancho_columna_1, 6, utf8_decode(mb_strtoupper($arreglo_alumno['matricula'])), 0, 0, 'L');
    $pdf->Cell($ancho_columna_2, 6, utf8_decode(mb_strtoupper($arreglo_alumno['primer_apellido'] . ' ' . $arreglo_alumno['segundo_apellido'] . ' ' .$arreglo_alumno['nombre'])), 0, 0, 'L');

    $pdf->SetFont('Arial', '', 9);

    if($arreglo_alumno['calificacion'] > 5 && $nivel_estudios_id == 6){
        $pdf->Cell($ancho_columna_3, 6, utf8_decode($arreglo_alumno['calificacion']), 1, 0, 'C');
    }
    
    else if($arreglo_alumno['calificacion'] <= 5 && $arreglo_alumno['calificacion'] != 0 && $nivel_estudios_id == 6){
        $pdf->Cell($ancho_columna_3, 6, utf8_decode('NA'), 1, 0, 'C');
        $arreglo_alumno['calificacion'] = -1;
    }else if($arreglo_alumno['calificacion'] == 0 && $arreglo_alumno['calificacion'] != ''){
        $pdf->Cell($ancho_columna_3, 6, utf8_decode('NP'), 1, 0, 'C');
    }else if($arreglo_alumno['calificacion'] > 6 && $nivel_estudios_id != 6){
        $pdf->Cell($ancho_columna_3, 6, utf8_decode($arreglo_alumno['calificacion']), 1, 0, 'C');
    }else if($arreglo_alumno['calificacion'] <= 6 && $nivel_estudios_id != 6 && $arreglo_alumno['calificacion'] != ''){
        $pdf->Cell($ancho_columna_3, 6, utf8_decode('NA'), 1, 0, 'C');
        $arreglo_alumno['calificacion'] = -1;
    }
    else if($arreglo_alumno['calificacion']==''){
        $pdf->Cell($ancho_columna_3, 6, '', 1, 0, 'C');
    }

    switch ($arreglo_alumno['calificacion']) {
        case '':
            $arreglo_alumno['calificacion_letra'] = "";
            break;
        case -1:
            $arreglo_alumno['calificacion_letra'] = "NA";
            break;
        case 0:
            $arreglo_alumno['calificacion_letra'] = "NP";
            break;
        case 1:
            $arreglo_alumno['calificacion_letra'] = "Uno";
            break;
        case 2:
            $arreglo_alumno['calificacion_letra'] = "Dos";
            break;
        case 3:
            $arreglo_alumno['calificacion_letra'] = "Tres";
            break;
        case 4:
            $arreglo_alumno['calificacion_letra'] = "Cuatro";
            break;
        case 5:
            $arreglo_alumno['calificacion_letra'] = "Cinco";
            break;
        case 6:
            $arreglo_alumno['calificacion_letra'] = "Seis";
            break;
        case 7:
            $arreglo_alumno['calificacion_letra'] = "Siete";
            break;
        case 8:
            $arreglo_alumno['calificacion_letra'] = "Ocho";
            break;
        case 9:
            $arreglo_alumno['calificacion_letra'] = "Nueve";
            break;
        case 10:
            $arreglo_alumno['calificacion_letra'] = "Diez";
            break;
    }

    $pdf->Cell($ancho_columna_3, 6, utf8_decode(mb_strtoupper($arreglo_alumno['calificacion_letra'])), 1, 1, 'C');
    $y_pos += 6;

    if ($y_pos >= 266) {
        $pdf->AddPage();
        $y_pos = 20;
    }
}

if ($y_pos >= 224) {
    $pdf->AddPage();
    $y_pos = 20;
}
// $pdf->Ln(5);
$pdf->SetXY(10.5, $y_pos + 5);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 6, '****************************************************************************************************************', 0, 0, 'L', 0);

$pdf->SetX(12);

$y_pos += 40;

$linea_profesor = $mid_x / 2;
$linea_coord_curricular = ($mid_x / 2) * 3;
$pdf->line($linea_profesor - 30, $y_pos, $linea_profesor + 30, $y_pos);
$pdf->line($linea_coord_curricular - 30, $y_pos, $linea_coord_curricular + 30, $y_pos);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY($linea_profesor - 30, $y_pos);
$pdf->Cell($linea_profesor + 8, 6, isset($_GET['nombre_docente']) ? utf8_decode(mb_strtoupper($_GET['nombre_docente'])) : utf8_decode(mb_strtoupper($arreglo_cabecera['nombre'] . ' ' . $arreglo_cabecera['primer_apellido'] . ' ' . $arreglo_cabecera['segundo_apellido'])), 0, 0, 'C', 0);

// $pdf->SetXY($linea_coord_curricular - 30, $y_pos);
// $pdf->Cell($linea_profesor + 8, 6, utf8_decode(mb_strtoupper('ADRIANA CORELLA LEÓN')), 0, 0, 'C', 0);

$pdf->SetXY($linea_profesor - 30, $y_pos + 4);
$pdf->Cell($linea_profesor + 8, 6, utf8_decode(mb_strtoupper('PROFESOR TITULAR')), 0, 0, 'C', 0);

$pdf->SetXY($linea_coord_curricular - 30, $y_pos + 4);
$pdf->Cell($linea_profesor + 8, 6, utf8_decode(mb_strtoupper('COORD. CURRICULAR')), 0, 0, 'C', 0);

$pdf->SetXY(12, $y_pos + 12);

function getMes($indiceMes)
{
    $indiceMes = $indiceMes - 1;
    $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    $mes = $meses[$indiceMes];
    return $mes;
}

if (isset($_GET['fecha'])) {
    $fecha = explode('-', $_GET['fecha']);
} else {
    date_default_timezone_set('America/Mexico_City');
    $fecha = explode('-', date('Y') . '-' . date('n') . '-' . date('d'));
}

$pdf->Cell(0, 6, utf8_decode(mb_strtoupper("CIUDAD DE MÉXICO A " .$fecha[2]." DE ".getMes($fecha[1])." DE ".$fecha[0])), 0, 0, 'L', 0);

$pdf->Output();
