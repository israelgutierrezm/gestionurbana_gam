<?php
include '../../jwt.php';
include "../../vendor/codigo_qr/phpqrcode/qrlib.php";
require('../../vendor/FPDF/fpdf.php');

$jwt = $_GET['jwt'];
$alumno_id = $_GET['alumno_id'];
$carrera_id = $_GET['carrera_id'];
$orden_jerarquico_id = $_GET['orden_jerarquico_id'];

$usuario = Auth::GetData(
    $jwt
);

error_reporting(E_ERROR | E_PARSE);

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $alumno_id = $_GET['alumno_id'];
$carrera_id = $_GET['carrera_id'];
        global $arreglo_persona;

        $arreglo_persona = arreglo(query('SELECT p.nombre, p.primer_apellido, p.segundo_apellido, ta.clave_alumno, tc.carrera , cne.url_imagen
from ' . $GLOBALS["db_datosGenerales"] . '.personas p 
join ' . $GLOBALS["db_controlEscolar"] . '.tr_alumno ta on ta.alumno_id = p.persona_id 
join ' . $GLOBALS["db_controlEscolar"] . '.inter_alumno_plan iap on iap.alumno_id = ta.alumno_id
join ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id 
join ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id 
join ' . $GLOBALS["db_datosGenerales"] . '.tr_carrera tc on tc.carrera_id = tpe.carrera_id 
join ' . $GLOBALS["db_datosGenerales"] . '.cat_nivel_estudios cne ON tc.nivel_estudios_id = cne.nivel_estudios_id 
where ta.alumno_id=' . $alumno_id . ' and tc.carrera_id = ' . $carrera_id . ''));

        //$this->Image('UMO.gif' ,145 , 7, 50, 30);
        $this->SetFont('Arial', 'B', 30);
        // Movernos a la derecha
        // $this->Cell(70,10,'Maqueta FPDF', 0, 0,'L');
        // Salto de línea
        $this->Ln(20);
        // print_r($arreglo_persona);
        // print_r($GLOBALS['url_front_assets'].$arreglo_persona['url_imagen']);
        // print_r('ok');
        // print_r($arreglo_persona['url_imagen']);
        if($arreglo_persona['url_imagen']==null || $arreglo_persona['url_imagen']==''){
            $this->Image($GLOBALS['url_front'].'assets/images/logo.png', 15, 10, 35, 15, 'png');
        }else{
            $this->Image($GLOBALS['url_front_assets'].$arreglo_persona['url_imagen'], 15, 10, 35, 15);}
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 5, utf8_decode('Page ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}


$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$fecha_actual = date("d") . '/' . date("m") . '/' . date("Y");

$pdf->SetFont('Arial', 'B', 10);
$pdf->SetY(7);
$pdf->SetTextColor(43, 63, 153);
$pdf->SetXY(52, 10);
$pdf->Cell(0, 10, 'DEPARTAMENTO DE SERVICIOS ESCOLARES', 0, 1, 'L', 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(97, 106, 107);
// $pdf->SetXY(52, 14);
// $pdf->Cell(0, 10, utf8_decode('Escuela Incorporado a la S.E.P. Clave: 21MSU1176X'), 0, 0, 'L', 0);
// $pdf->SetXY(52, 18);
// $pdf->Cell(0, 10, utf8_decode('5 Poniente 706 - 708 Centro CP 72000 Puebla'), 0, 0, 'L', 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 10, utf8_decode('BOLETA DE CALIFICACIONES'), 0, 0, 'R', 0);

$pdf->SetDrawColor(207, 10, 7);
$pdf->SetLineWidth(2);
$pdf->Line(15, 28, 200, 28);
$pdf->SetFont('Arial', '', 8);
$pdf->SetXY(130, 28);
$pdf->Cell(70, 10, utf8_decode($fecha_actual), 0, 1, 'R', 0);

$marco = 15;
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetTextColor(43, 63, 153);
$pdf->SetXY($marco, 30);
$pdf->Cell(0, 6, utf8_decode(strtoupper($arreglo_persona['carrera'])), 0, 1, 'L', 0);


$query_grado = query('SELECT th.historial_id, tpe.carrera_id, th.alumno_id, th.materia_id, coj.orden_jerarquico,
coj.orden_jerarquico_descripcion, coj.orden_jerarquico_id, tab1.suma_calificacion, tab1.num_materias
from ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th 
join ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa on ioa.orden_asignatura_id = th.orden_asignatura_id 
join ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo on ipo.plan_orden_id = ioa.plan_orden_id
join ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
join ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj on coj.orden_jerarquico_id = ipo.orden_jerarquico_id
join (select  th.historial_id,  sum(calificacion) as suma_calificacion, count(*) as num_materias 
from ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th
join ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa on ioa.orden_asignatura_id = th.orden_asignatura_id
join ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id
join ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo on ipo.plan_orden_id = ioa.plan_orden_id
join ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
join ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj2 on coj2.orden_jerarquico_id = ipo.orden_jerarquico_id 
where alumno_id = ' . $alumno_id . ' and tpe.carrera_id = ' . $carrera_id . ' and ca.estatus = 1 and th.estatus = 1 
group by alumno_id, coj2.orden_jerarquico_id) tab1 on tab1.historial_id = th.historial_id 
where th.alumno_id = ' . $alumno_id . '  and tpe.carrera_id = ' . $carrera_id . ' and coj.orden_jerarquico_id =' . $orden_jerarquico_id . '
 group by coj.orden_jerarquico_id order by coj.orden_jerarquico_id asc'); //Sólo trae los grados de las materias que tiene ese alumno

if (num($query_grado)) {
    $json_grado = array();
    while ($arreglo_grado = arreglo($query_grado)) {
        //trae todas las materias con ese grado
        $query_materias = query('select t2.ordinarios, t3.extraordinarios, th.alumno_id, ca.asignatura_clave, ca.asignatura,max(th.calificacion) as calificacion,th.historial_id,
        th.fecha_creacion, coj.orden_jerarquico_id, ca.asignatura_padre_id, th.situacion_reprobatoria_id, cscr.situacion_clave, grupo
        FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa ON ioa.orden_asignatura_id = th.orden_asignatura_id
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
        left join ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca2 on ca.asignatura_padre_id = ca2.asignatura_id 
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo ON ipo.plan_orden_id = ioa.plan_orden_id 
        JOIN ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg ON tg.plan_orden_id = ipo.plan_orden_id
        join ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj ON coj.orden_jerarquico_id = ipo.orden_jerarquico_id
        LEFT JOIN ' . $GLOBALS["db_controlEscolar"] . '.cat_situacion_calificacion_reprobatoria cscr ON th.situacion_reprobatoria_id = cscr.situacion_id
        LEFT JOIN (select COUNT(*) as ordinarios, ca.asignatura_id
        		   from ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th 
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa ON ioa.orden_asignatura_id = th.orden_asignatura_id
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo ON ipo.plan_orden_id = ioa.plan_orden_id 
                   join ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
        	       JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj ON coj.orden_jerarquico_id = ipo.orden_jerarquico_id
        		   where th.alumno_id = ' . $alumno_id . ' and tpe.carrera_id = ' . $carrera_id . ' and coj.orden_jerarquico = ' . $arreglo_grado['orden_jerarquico_id'] . '  and th.estatus_historial_id in(1,5)
        		   group by ca.asignatura_id) t2 on t2.asignatura_id = ca.asignatura_id
        LEFT JOIN (select COUNT(*) as extraordinarios, ca.asignatura_id
        		   from ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th 
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa ON ioa.orden_asignatura_id = th.orden_asignatura_id
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo ON ipo.plan_orden_id = ioa.plan_orden_id 
                   join ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
        	       JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj ON coj.orden_jerarquico_id = ipo.orden_jerarquico_id
        		   where th.alumno_id = ' . $alumno_id . ' and tpe.carrera_id = ' . $carrera_id . ' and coj.orden_jerarquico = ' . $arreglo_grado['orden_jerarquico_id'] . '  and th.estatus_historial_id in(3,7)
        		   group by ca.asignatura_id) t3 on t3.asignatura_id = ca.asignatura_id		   
        WHERE alumno_id = ' . $alumno_id . ' and tpe.carrera_id = ' . $carrera_id . ' AND ca.estatus = 1 AND th.estatus = 1 AND coj.orden_jerarquico_id = ' . $arreglo_grado['orden_jerarquico_id'] . ' 
        GROUP BY ca.asignatura_id');

        $json_materias = array();
        while ($arreglo_materias = arreglo($query_materias)) {
            array_push($json_materias, $arreglo_materias);
        }
        $arreglo_grado['materias'] = $json_materias;
        array_push($json_grado, $arreglo_grado);
    }
}

$y_cell = 37;

$actividad_nombre = array();
$i = 0;
foreach ($json_grado as $grado) {
    $y_cell += 8;
    $i++;
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(43, 63, 153);
    $pdf->SetX($marco);
    $pdf->Cell(50, 6, utf8_decode("MATRÍCULA:"), 0, 0, 'C', TRUE);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(50, 6,  utf8_decode(strtoupper($arreglo_persona['clave_alumno'])), 0, 0, 'L', TRUE);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(42.5, 6, utf8_decode("GRADO:"), 0, 0, 'C', TRUE);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(42.5, 6,  utf8_decode(strtoupper($grado['orden_jerarquico_descripcion'])), 0, 1, 'L', TRUE);
    $pdf->SetX($marco);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(50, 6, 'ALUMNO(A):', 0, 0, 'C', TRUE);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(50, 6, utf8_decode($arreglo_persona['nombre'] . ' ' . $arreglo_persona['primer_apellido'] . ' ' . $arreglo_persona['segundo_apellido']), 0, 0, 'L', TRUE);
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(42.5, 6, utf8_decode("GRUPO:"), 0, 0, 'C', TRUE);
    $pdf->SetFont('Arial', '', 9);
    $pdf->Cell(42.5, 6,  utf8_decode(strtoupper($grado['grupo'])), 0, 1, 'L', TRUE);
    $pdf->SetX($marco);
    $pdf->Cell(90, 6, ' ', 0, 0, 'C', TRUE);
    $pdf->Cell(0, 6,  ' ', 0, 1, 'L', TRUE);
    $pdf->SetFillColor(207, 10, 7);
    $pdf->SetX($marco);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(155, 9, 'ASIGNATURAS', 0, 0, 'C', TRUE);
    $pdf->Cell(30, 9, utf8_decode('CALIFICACIÓN FINAL'), 0, 1, 'C', TRUE);
    
    if ($grado['materias']) {
        foreach ($grado['materias'] as $materias) {
            $y_cell += 8;
            $i++;
            if (isset($materias['asignatura_padre_id'])) {
                $materia_padre = arreglo(query('SELECT ca.asignatura_clave as asignatura_clave_padre 
                from ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca where ca.asignatura_clave=' . $materias['asignatura_padre_id'] . ' group by ca.asignatura_clave'));
                $materias['seriacion'] = $materia_padre['asignatura_clave'];
            }
            if ($i == 24) {
                $pdf->AddPage();
                $y_cell = 30;
            } elseif ($i == 50) {
                $pdf->AddPage();
                $y_cell = 20;
            }
            $pdf->SetLineWidth(0);
            $pdf->SetDrawColor(43, 63, 153);
            $pdf->SetTextColor(43, 63, 153);
            $pdf->SetFont('Arial', '', 7);
            $pdf->SetXY($marco, 8 + $y_cell + 2);
            $pdf->Cell(155, 8, utf8_decode($materias['asignatura']), 1, 0, 'L', 0);
            $pdf->SetXY($marco + 155, 10 + $y_cell);
            $pdf->Cell(30, 8, utf8_decode($materias['situacion_reprobatoria_id'] != null ?
                $materias['situacion_clave'] : $materias['calificacion']), 1, 1, 'C', 0);
        }
    }
}

$resta_cell = $y_cell - 160;
$salto_page = $y_cell;
if ($salto_page >= 196 & $salto_page <= 198) {
    $pdf->AddPage();
    $y_cell = 35;
} elseif ($salto_page >= 194 & $salto_page <= 195) {
    $pdf->AddPage();
    $y_cell = 35;
} elseif ($salto_page >= 204 & $salto_page <= 205) {
    $pdf->AddPage();
    $y_cell = 35;
} elseif ($salto_page >= 228 & $salto_page <= 229) {
    $pdf->AddPage();
    $y_cell = 35;
} elseif ($salto_page >= 220 & $salto_page <= 221) {
    $pdf->AddPage();
    $y_cell = 35;
}

$materias_totales = arreglo(query('SELECT count(*) as materias_totales from ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa ON ioa.orden_asignatura_id = th.orden_asignatura_id 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo ON ipo.plan_orden_id = ioa.plan_orden_id 
join ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id 
where alumno_id = ' . $alumno_id . ' and tpe.carrera_id = ' . $carrera_id . ' and th.estatus =1'));

$sumaCalificaciones = 0;
$margen = $marco;
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetXY($marco, 12 + $y_cell + 6);
$pdf->Cell(155, 8, 'PROMEDIO', 1, 0, 'L', 0);
$pdf->SetXY($marco + 155, 18 + $y_cell);
$pdf->Cell(30, 8, round(($grado['suma_calificacion'] / $grado['num_materias']), 1, PHP_ROUND_HALF_DOWN), 1, 1, 'C', 0);
$sumaCalificaciones = $sumaCalificaciones + $grado['suma_calificacion'];

$pdf->SetTextColor(43, 63, 153);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetY(200);
$pdf->Cell(200, 10, utf8_decode(''), 0, 1, 'C', 0);
$pdf->SetY(205);
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(200, 10, utf8_decode('CONTROL ESCOLAR'), 0, 1, 'C', 0);
$pdf->SetFont('Arial', '', 8);
$pdf->SetTextColor(97, 106, 107);
$pdf->SetY(260);
$pdf->Cell(200, 10, utf8_decode(''), 0, 1, 'C', 0);
$pdf->SetY(265);
$pdf->Cell(200, 10, utf8_decode(''), 0, 1, 'C', 0);

$pdf->Output();
