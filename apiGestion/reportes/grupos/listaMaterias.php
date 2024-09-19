<?php
include '../../jwt.php';
include "../../vendor/codigo_qr/phpqrcode/qrlib.php";
require('../../vendor/FPDF/fpdf.php');

$jwt = $_GET['jwt'];
$alumno_id = $_GET['alumno_id'];
$carrera_id = $_GET['carrera_id'];

$usuario = Auth::GetData(
    $jwt
);

error_reporting(E_ERROR | E_PARSE);

function agregarPromedioGeneral($sumaCalificaciones, $materias_totales)
{
    global $pdf, $size_page_without_margins, $margin_x, $current_y;
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY(($size_page_without_margins / 3) + $margin_x, $current_y);
    $pdf->Cell($size_page_without_margins / 3, 7, ('Promedio general'), 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetXY(($size_page_without_margins / 3) + $margin_x, $current_y + 7);
    $promedio_general = is_nan($sumaCalificaciones) || $sumaCalificaciones == 0 ? 0 : round(($sumaCalificaciones / $materias_totales), 1, PHP_ROUND_HALF_DOWN);
    $pdf->Cell($size_page_without_margins / 3, 6, $promedio_general, 1, 0, 'C', 0);
}

class PDF extends FPDF
{
    function Header()
    {
        global $arreglo_persona;
        $margin_x = 15;
        $width_page = $this->GetPageWidth();
        $middle_width_page = ($width_page / 2);
        $current_y = 33;
        $size_page_without_margins = $width_page - ($margin_x * 2);
        $fecha_actual = date("d") . '/' . date("m") . '/' . date("Y");
        if(isset($arreglo_persona['url_imagen']) || $arreglo_persona['url_imagen']!=null || $arreglo_persona['url_imagen']!=''){
            $this->Image($GLOBALS['url_front_assets'].$arreglo_persona['url_imagen'], 13.2, 10, 25, 20);
        }else{
            $this->Image($GLOBALS['url_front'].'assets/images/logo.png', 13.2, 10, 25, 20, 'png');}
        $this->SetXY($margin_x, $current_y);
        $this->Cell($size_page_without_margins, 20, '', 1, 1, 'C', 0);
        $this->SetFont('Arial', 'B', 10);
        $this->SetXY($middle_width_page / 2, 13);
        $this->Cell($middle_width_page, 10, utf8_decode('DEPARTAMENTO DE SERVICIOS ESCOLARES'), 0, 1, 'C', 0);
        $this->SetFont('Arial', '', 10);
        $this->SetXY(($size_page_without_margins / 3) + $margin_x, 20);
        $this->Cell($size_page_without_margins / 3, 10, utf8_decode('KARDEX'), 0, 1, 'C', 0);
        $this->SetFont('Arial', '', 8);
        $this->SetXY((($size_page_without_margins / 3) * 2) + $margin_x, 20);
        $this->Cell(($size_page_without_margins / 3), 10, utf8_decode('Fecha de expedición: ' . $fecha_actual), 0, 1, 'R', 0);
        // Salto de línea
        // $this->Ln(20);
        // $this->Image($GLOBALS['url_front'] . 'assets/images/logo.jpg', 15, 10, 35, 15, 'jpg');
        $this->Ln(7);
        $this->SetFont('Arial', '', 8);
        $this->SetX($margin_x);
        $this->Cell(26, 6, 'Nombre del alumno:', 0, 0, 'L', 0);
        $this->Cell($middle_width_page - 26, 6, utf8_decode($arreglo_persona['nombre'] . ' ' . $arreglo_persona['primer_apellido'] . ' ' . $arreglo_persona['segundo_apellido']), 0, 0, 'L', 0);
        $this->Cell(13, 6, utf8_decode("Matrícula:"), 0, 0, 'L', 0);
        $this->Cell(20, 6,  utf8_decode(strtoupper($arreglo_persona['clave_alumno'])), 0, 1, 'L', 0);
        $this->SetX($margin_x);
        $this->Cell(11, 6, 'Carrera:', 0, 0, 'L', 0);
        $this->Cell($middle_width_page - 11, 6, utf8_decode(strtoupper($arreglo_persona['carrera'])), 0, 0, 'L', 0);
        $this->Cell(35, 6, "", 0, 0, 'L', 0);
        $this->Cell(50, 6, "", 0, 1, 'L', 0);
    }

    // Pie de página
    function Footer()
    {
        // $this->SetXY(70, 240);
        // $this->Cell(32, 7, ('Promedio general'), 1, 0, 'C', 0);
        // $this->SetXY(102, 240);
        // $this->Cell(20, 7, round(($sumaCalificaciones / $materias_totales['materias_totales']), 1, PHP_ROUND_HALF_DOWN), 1, 0, 'C', 0);
        $width_page = $this->GetPageWidth();
        $size_page_without_margins = $width_page - (15 * 2);
        $this->SetXY(15, -24);

        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        $this->MultiCell($size_page_without_margins, 5, utf8_decode(''), 0, 'C');
        $this->Ln(2);
        // Número de página
        $this->Cell(0, 5, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}


$arreglo_persona = arreglo(query('SELECT p.nombre, p.primer_apellido, p.segundo_apellido, ta.clave_alumno, tc.carrera, ctp.periodo, cne.url_imagen
FROM ' . $GLOBALS["db_datosGenerales"] . '.personas p 
JOIN ' . $GLOBALS["db_controlEscolar"] . '.tr_alumno ta on ta.alumno_id = p.persona_id 
JOIN ' . $GLOBALS["db_controlEscolar"] . '.inter_alumno_plan iap on iap.alumno_id = ta.alumno_id
JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_carrera tc on tc.carrera_id = tpe.carrera_id 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_nivel_estudios cne ON tc.nivel_estudios_id = cne.nivel_estudios_id 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_tipo_periodo ctp ON ctp.tipo_periodo_id = tpe.tipo_periodo_id
WHERE ta.alumno_id=' . $alumno_id . ' and tc.carrera_id = ' . $carrera_id . ''));

$materias_totales = arreglo(query('SELECT count(*) as materias_totales FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa ON ioa.orden_asignatura_id = th.orden_asignatura_id 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo ON ipo.plan_orden_id = ioa.plan_orden_id 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id 
WHERE alumno_id = ' . $alumno_id . ' and tpe.carrera_id = ' . $carrera_id . ' and th.estatus = 1'));

$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$margin_x = 15;
$width_page = $pdf->GetPageWidth();
$middle_width_page = ($width_page / 2);
$current_y = 60;
$size_page_without_margins = $width_page - ($margin_x * 2);
$fecha_actual = date("d") . '/' . date("m") . '/' . date("Y");


$pdf->SetXY($margin_x, $current_y);
$pdf->Cell(80, 15, 'Materia', 1, 0, 'C', 0);
$pdf->Cell(17.5, 15, utf8_decode('Clave'), 1, 0, 'C', 0);
$pdf->Cell(17.5, 15, utf8_decode('Seriación'), 1, 0, 'C', 0);

$pdf->SetXY(130, $current_y);
$pdf->Cell(65, 5, utf8_decode('Calificación definitiva'), 1, 0, 'C', 0);

$pdf->SetFont('Arial', '', 7);
$pdf->SetXY(130, $current_y + 5);
$pdf->MultiCell(22.5, 10, utf8_decode("Fecha"), 1, 'C');

$pdf->SetXY(152.5, $current_y + 5);
$pdf->Cell(22.5, 10, utf8_decode('Calificación'), 1, 0, 'C', 0);

$pdf->SetXY(175, $current_y + 5);
$pdf->Cell(10, 10, utf8_decode('Ord'), 1, 0, 'C', 0);

$pdf->SetXY(185, $current_y + 5);
$pdf->Cell(10, 10, utf8_decode('Ext'), 1, 0, 'C', 0);

$query_grados_alumno = query('SELECT th.historial_id, tpe.carrera_id, th.alumno_id, th.materia_id, coj.orden_jerarquico,
coj.orden_jerarquico_descripcion, coj.orden_jerarquico_id, tab1.suma_calificacion, tab1.num_materias
FROM (select th.historial_id , th.orden_asignatura_id , th.ciclo_id , th.alumno_id, th.estatus_historial_id,
max(th.calificacion) as calificacion , th.materia_id , th.situacion_reprobatoria_id , th.estatus  from ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th 
    where th.estatus = 1 and th.alumno_id = ' . $alumno_id . '  GROUP by orden_asignatura_id) th
JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa on ioa.orden_asignatura_id = th.orden_asignatura_id 
JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo on ipo.plan_orden_id = ioa.plan_orden_id
JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj on coj.orden_jerarquico_id = ipo.orden_jerarquico_id
JOIN (SELECT  th.historial_id,  sum(calificacion) as suma_calificacion, count(*) as num_materias 
FROM (select th.historial_id , th.orden_asignatura_id , th.ciclo_id , th.alumno_id, th.estatus_historial_id,
max(th.calificacion) as calificacion , th.materia_id , th.situacion_reprobatoria_id , th.estatus  from ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th 
where th.estatus = 1 and th.alumno_id = ' . $alumno_id . '  GROUP by orden_asignatura_id) th
JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa on ioa.orden_asignatura_id = th.orden_asignatura_id
JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id
JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo on ipo.plan_orden_id = ioa.plan_orden_id
JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj2 on coj2.orden_jerarquico_id = ipo.orden_jerarquico_id 
WHERE alumno_id = ' . $alumno_id . ' and tpe.carrera_id = ' . $carrera_id . ' and ca.estatus = 1 and th.estatus = 1 
GROUP BY alumno_id, coj2.orden_jerarquico_id) tab1 on tab1.historial_id = th.historial_id 
WHERE th.alumno_id = ' . $alumno_id . '  and tpe.carrera_id = ' . $carrera_id . ' GROUP BY coj.orden_jerarquico_id order by coj.orden_jerarquico_id asc'); //Sólo trae los grados de las materias que tiene ese alumno

if (num($query_grados_alumno)) {
    $sumaCalificaciones = 0;
    $lista_grados_alumno = array();
    $cantidad_grados = num($query_grados_alumno);
    $ancho_de_celda = ($size_page_without_margins / $cantidad_grados);
    while ($arreglo_grado = arreglo($query_grados_alumno)) {
        //trae todas las materias con ese grado
        $query_materias = query('select t2.ordinarios, t3.extraordinarios, th.alumno_id, ca.asignatura_clave, ca.asignatura, 
        IF((max(th.calificacion) IS NOT NULL AND max(th.calificacion) != 0), max(th.calificacion), "NA") as calificacion,th.historial_id,
        th.fecha_creacion, coj.orden_jerarquico_id, ca.asignatura_padre_id, th.situacion_reprobatoria_id, cscr.situacion_clave
        FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa ON ioa.orden_asignatura_id = th.orden_asignatura_id
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
        left JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca2 on ca.asignatura_padre_id = ca2.asignatura_id 
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo ON ipo.plan_orden_id = ioa.plan_orden_id 
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj ON coj.orden_jerarquico_id = ipo.orden_jerarquico_id
        LEFT JOIN ' . $GLOBALS["db_controlEscolar"] . '.cat_situacion_calificacion_reprobatoria cscr ON th.situacion_reprobatoria_id = cscr.situacion_id
        LEFT JOIN (select COUNT(*) as ordinarios, ca.asignatura_id
        		   FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th 
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa ON ioa.orden_asignatura_id = th.orden_asignatura_id
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo ON ipo.plan_orden_id = ioa.plan_orden_id 
                   JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
        	       JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj ON coj.orden_jerarquico_id = ipo.orden_jerarquico_id
        		   WHERE th.estatus= 1 and th.alumno_id = ' . $alumno_id . ' and tpe.carrera_id = ' . $carrera_id . ' and coj.orden_jerarquico = ' . $arreglo_grado['orden_jerarquico_id'] . '  and th.estatus_historial_id in(1,5)
        		   GROUP BY ca.asignatura_id) t2 on t2.asignatura_id = ca.asignatura_id
        LEFT JOIN (select COUNT(*) as extraordinarios, ca.asignatura_id
        		   FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_historial th 
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa ON ioa.orden_asignatura_id = th.orden_asignatura_id
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
        		   JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo ON ipo.plan_orden_id = ioa.plan_orden_id 
                   JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
        	       JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj ON coj.orden_jerarquico_id = ipo.orden_jerarquico_id
        		   WHERE th.estatus= 1 and th.alumno_id = ' . $alumno_id . ' and tpe.carrera_id = ' . $carrera_id . ' and coj.orden_jerarquico = ' . $arreglo_grado['orden_jerarquico_id'] . '  and th.estatus_historial_id in(3,7)
        		   GROUP BY ca.asignatura_id) t3 on t3.asignatura_id = ca.asignatura_id		   
        WHERE th.estatus = 1 and alumno_id = ' . $alumno_id . ' and tpe.carrera_id = ' . $carrera_id . ' AND ca.estatus = 1 AND th.estatus = 1 AND coj.orden_jerarquico_id = ' . $arreglo_grado['orden_jerarquico_id'] . ' 
        GROUP BY ca.asignatura_id');

        $json_materias = array();
        while ($arreglo_materias = arreglo($query_materias)) {
            array_push($json_materias, $arreglo_materias);
        }
        $arreglo_grado['materias'] = $json_materias;
        $sumaCalificaciones +=  $arreglo_grado['suma_calificacion'];
        array_push($lista_grados_alumno, $arreglo_grado);
    }
}

// $current_y = 60;
$actividad_nombre = array();
$i = 0;
foreach ($lista_grados_alumno as $grado) {
    $current_y += 8;
    $i++;
    // echo $i;
    if ($current_y > 228) {
        $current_y += 15;
        agregarPromedioGeneral($sumaCalificaciones, $materias_totales['materias_totales']);
        $pdf->AddPage();
        $current_y = 52;
    }
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetXY($margin_x, 7 + $current_y);
    $pdf->Cell(80, 8, utf8_decode($grado['orden_jerarquico_descripcion']), 1, 0, 'C', 0);
    $pdf->Cell(17.5, 8, (''), 1, 0, 'C', 0);
    $pdf->Cell(17.5, 8, (''), 1, 0, 'C', 0);
    $pdf->Cell(22.5, 8, (''), 1, 0, 'C', 0);
    $pdf->Cell(22.5, 8, (''), 1, 0, 'C', 0);
    $pdf->Cell(10, 8, (''), 1, 0, 'C', 0);
    $pdf->Cell(10, 8, (''), 1, 0, 'C', 0);
    if ($grado['materias']) {
        // for ($j = 0; $j < 2; $j++) {
            foreach ($grado['materias'] as $materias) {
                $current_y += 8;
                $i++;
                if (isset($materias['asignatura_padre_id'])) {
                    $materia_padre = arreglo(query('SELECT ca.asignatura_clave as asignatura_clave_padre 
                    FROM ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca 
                    WHERE ca.asignatura_clave=' . $materias['asignatura_padre_id'] . ' GROUP BY ca.asignatura_clave'));
                    $materias['seriacion'] = $materia_padre['asignatura_clave'];
                }
                if ($current_y > 228) {
                    $current_y += 15;
                    // ya
                    agregarPromedioGeneral($sumaCalificaciones, $materias_totales['materias_totales']);
                    $pdf->AddPage();
                    $current_y = 52;
                }

                $pdf->SetFont('Arial', '', 7);
                $pdf->SetXY($margin_x, 7 + $current_y);
                $pdf->Cell(80, 8, '', 1, 0, 'C', 0);
                $pdf->SetXY($margin_x, 7 + $current_y + 1);
                $pdf->MultiCell(80, 8, utf8_decode($materias['asignatura']));
                $pdf->SetXY($margin_x + 80, 7 + $current_y);
                $pdf->Cell(17.5, 8, utf8_decode($materias['asignatura_clave']), 1, 0, 'C', 0);
                $pdf->Cell(17.5, 8, utf8_decode($materias['seriacion']), 1, 0, 'C', 0);
                $pdf->Cell(22.5, 8, utf8_decode(strftime("%d/%m/%Y", strtotime($materias['fecha_creacion']))), 1, 0, 'C', 0);
                $pdf->Cell(22.5, 8, utf8_decode($materias['situacion_reprobatoria_id'] != null ?
                    $materias['situacion_clave'] : $materias['calificacion']), 1, 0, 'C', 0);
                $pdf->Cell(10, 8, $materias['ordinarios'], 1, 0, 'C', 0);
                $pdf->Cell(10, 8, $materias['extraordinarios'], 1, 0, 'C', 0);
            }
        // }
    }
}

$current_y += 18;
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetXY($margin_x, $current_y);
$pdf->Cell(180, 6, ('Promedios por ' . strtolower($arreglo_persona['periodo'] . '.')), 0, 0, 'L', 0);

$margen = $margin_x;

foreach ($lista_grados_alumno as $grado) {
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetXY($margen, 7 + $current_y);
    $pdf->Cell($ancho_de_celda, 7, ($grado['orden_jerarquico_descripcion']), 1, 0, 'C', 0);
    $pdf->SetFont('Arial', '', 9);
    $pdf->SetXY($margen, 14 + $current_y);
    $pdf->Cell($ancho_de_celda, 6, round(($grado['suma_calificacion'] / $grado['num_materias']), 1, PHP_ROUND_HALF_DOWN), 1, 0, 'C', 0);
    $margen += $ancho_de_celda;
}

$current_y += 28;

// agregarPromedioGeneral($sumaCalificaciones, $materias_totales['materias_totales']);
$current_y += 13;

$rest_page = 257 - $current_y;
$new_y = 228 - $current_y;
if ($rest_page < 45) {
    $pdf->AddPage();
}

$pdf->SetXY($margin_x + 10, $current_y + $new_y + 10);
$pdf->Cell($middle_width_page, 10, utf8_decode('Nombre'), 'T', 0, 'C');
$pdf->SetXY($margin_x + 10, $current_y + $new_y + 29);
$pdf->Cell($middle_width_page, 10, utf8_decode('Firma'), 'T', 0, 'C');

$pdf->SetXY($size_page_without_margins + $margin_x - 35, $current_y + $new_y - 6);
$pdf->Cell(35, 35, '', 1, 0, 'C', 0);
$pdf->SetXY($size_page_without_margins + $margin_x - 35, $current_y + $new_y + 31);
$pdf->MultiCell(35, 4, 'Sello de Control Escolar', 0, 'C');

$pdf->Output('', 'historial.pdf', true);
?>
