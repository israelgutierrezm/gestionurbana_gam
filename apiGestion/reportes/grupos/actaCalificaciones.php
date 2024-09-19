<?php
include '../../jwt.php';
include "../../vendor/codigo_qr/phpqrcode/qrlib.php";
require('../../vendor/FPDF/fpdf.php');

$jwt = $_GET['jwt'];
$id_asignatura_grupo = $_GET['id_asignatura_grupo'];
$id_actividad = $_GET['id_actividad'];
$fecha_inicio = $_GET['fecha_inicio'];
$fecha_fin = $_GET['fecha_fin'];

    $usuario = Auth::GetData(
        $jwt  
    );

    
// print_r($usuario);
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        global $arreglo_cabecera;
        $id_asignatura_grupo = $_GET['id_asignatura_grupo'];

        $arreglo_cabecera = arreglo(query('select tpe.rvoe, iag.asignatura_grupo_id, cc.ciclo_desc,
        coj.orden_jerarquico_descripcion, tg.nombre_grupo, cne.url_imagen,
        ca.asignatura_clave, tc.carrera, ca.asignatura, p.nombre, p.primer_apellido, p.segundo_apellido
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
        where iag.estatus=1 and tg.estatus=1 and cc.estatus=1 and ipo.estatus=1 and tpe.estatus=1 and
        tc.estatus=1 and coj.estatus=1 and ioa.estatus=1 and ca.estatus=1 and idag.estatus=1 and td.estatus=1 and p.estatus=1 and iag.asignatura_grupo_id = '.$id_asignatura_grupo));

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

$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(90, 13);
$pdf->Cell(70, 10, 'INSTITUTO '.$GLOBALS['nombre_institucion'], 0, 1, 'C', 0);
$pdf->SetXY(90, 22);
$pdf->Cell(70, 10, 'ACTA DE CALIFICACIONES ORDINARIAS', 0, 1, 'C', 0);

$marco = 15;
$pdf->Ln(11);
$pdf->SetFont('Arial', '', 8);
$pdf->SetX(37);
$pdf->Cell(30, 6, '', 0, 0, 'C', 0);
$pdf->SetX(110);
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetXY(100, 38);
$pdf->Cell(30, 6, 'RVOE SEP NO.', 0, 0, 'C', 0);
$pdf->Cell(30, 6, $arreglo_cabecera['rvoe'], 0, 0, 'L', 0);

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 6);
$pdf->SetX($marco);
$pdf->Cell(18, 6, 'LICENCIATURA:', 0, 0, 'L', 0);
$pdf->Cell(72, 6, utf8_decode(strtoupper($arreglo_cabecera['carrera'])), 0, 0, 'L', 0);
$pdf->Cell(8, 6, 'CICLO:', 0, 0, 'L', 0);
$pdf->Cell(63, 6, utf8_decode(strtoupper($arreglo_cabecera['ciclo_desc'])), 0, 1, 'L', 0);

$pdf->SetX($marco);
$pdf->Cell(11, 6, 'MATERIA:', 0, 0, 'L', 0);
$pdf->Cell(79, 6, utf8_decode(strtoupper($arreglo_cabecera['asignatura'])), 0, 0, 'L', 0);
$pdf->Cell(18, 6, 'CUATRIMESTRE:', 0, 0, 'L', 0);
$pdf->Cell(50, 6, utf8_decode(strtoupper($arreglo_cabecera['orden_jerarquico_descripcion'])), 0, 1, 'L', 0);

$pdf->SetX($marco);
$pdf->Cell(13, 6, 'PROFESOR:', 0, 0, 'L', 0);
$pdf->Cell(77, 6, utf8_decode(strtoupper($arreglo_cabecera['nombre'] . ' ' . $arreglo_cabecera['primer_apellido'] . ' ' . $arreglo_cabecera['segundo_apellido'])), 0, 0, 'L', 0);
$pdf->Cell(9, 6, 'GRUPO:', 0, 0, 'L', 0);
$pdf->Cell(62, 6, utf8_decode(strtoupper($arreglo_cabecera['nombre_grupo'])), 0, 1, 'L', 0);

$pdf->SetX($marco);
$pdf->Cell(33, 6, '', 0, 0, 'L', 0);
$pdf->Cell(57, 5, '', 0, 0, 'L', 0);
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell(19, 6, 'CLAVE MATERIA:', 0, 0, 'L', 0);
$pdf->Cell(52, 6, utf8_decode(strtoupper($arreglo_cabecera['asignatura_clave'])), 0, 1, 'L', 0);
$pdf->Cell(49, 5, '', 0, 1, 'L', 0);


$pdf->SetXY(15,61);
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell(33, 6, 'FECHA DE EXAMEN:', 0, 0, 'L', 0);
$pdf->line(40, 65, 70, 65);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(63, 5, "", 0, 0, 'L', 0);

$pdf->SetXY(15,66);
$pdf->SetFont('Arial', 'B', 6);
$pdf->Cell(33, 6, 'FECHA DE ENTREGA:', 0, 0, 'L', 0);
$pdf->line(40, 70, 70, 70);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(49, 5, "", 0, 1, 'L', 0);

$pdf->SetFont('Arial', 'B', 6);
$pdf->Ln(9);
$pdf->SetX($marco);
$pdf->Cell(10, 8, 'N/L', 1, 0, 'C', 0);
$pdf->Cell(60, 8, 'NOMBRE', 1, 0, 'C', 0);
$pdf->MultiCell(23, 4, utf8_decode('CALIFICACIÓN NUMERO'), 1, 'C');
$pdf->SetXY(108, 80);
$pdf->MultiCell(23, 4, utf8_decode('CALIFICACIÓN LETRA'), 1, 'C');
$pdf->SetXY(131, 80);
$pdf->Cell(25, 8, 'INASISTENCIAS', 1, 0, 'C');
$pdf->Cell(35, 8, 'FIRMA ALUMNO', 1, 1, 'C');

$query_alumnos = query('select tm.alumno_id, p.nombre, p.primer_apellido, p.segundo_apellido,
     ta.actividad_nombre,tmfa.calificacion
    from '.$GLOBALS['db_learning'].'.tr_materia tm 
    join '.$GLOBALS['db_datosGenerales'].'.personas p on p.persona_id = tm.alumno_id
    join '.$GLOBALS['db_learning'].'.inter_modulos_asignatura_grupo imag on imag.asignatura_grupo_id = tm.asignatura_grupo_id
    join '.$GLOBALS['db_learning'].'.tr_actividad ta on ta.modulo_id = imag.modulo_id
    left join '.$GLOBALS['db_learning'].'.tr_materia_fecha_actividad tmfa on tmfa.materia_id = tm.materia_id and tmfa.actividad_id = ta.actividad_id
    where tm.asignatura_grupo_id= '.$id_asignatura_grupo.' and ta.actividad_id  ='.$id_actividad.' 
    and tm.estatus=1 and imag.estatus=1 and ta.estatus=1 order by p.primer_apellido, p.segundo_apellido, p.nombre');


$y_cell = 82;
$y_multicell = 4;
$id_alumno = 1;
$actividad_nombre = array();
while ($arreglo_alumno = arreglo($query_alumnos)) {
    array_push($actividad_nombre, $arreglo_alumno);
    $query_conferencia = query('select videoconferencia_id from '.$GLOBALS['db_learning'].'.tr_videoconferencia where  asignatura_grupo_id = '.$id_asignatura_grupo.' and date(fecha_inicio) 
        between "'.$fecha_inicio.'" and "'.$fecha_fin.'" and estatus != 0');
    $num_conferencias = num($query_conferencia);
    $asistencias = 0;
    while ($arreglo_conferencia = arreglo($query_conferencia)) {
        $conferencias_alumno = query('select acceso_videoconferencia_id from '.$GLOBALS['db_learning'].'.tr_acceso_videoconferencia where videoconferencia_id  = ' . $arreglo_conferencia['videoconferencia_id'] . '
            and persona_id = ' . $arreglo_alumno['alumno_id']);
        if (num($conferencias_alumno)) {
            $asistencias++;
        }
    }

    $asistencias_alumnos = $num_conferencias - $asistencias;
    $pdf->SetXY($marco, 6 + $y_cell);
    $pdf->Cell(10, 6, $id_alumno, 1, 0, 'C', 0);
    $pdf->SetXY(15, 6 + $y_cell);
    $pdf->MultiCell(70, 6, utf8_decode($arreglo_alumno['nombre'] . ' ' . $arreglo_alumno['primer_apellido'] . ' ' . $arreglo_alumno['segundo_apellido']), 1, 'C');
    $pdf->SetXY(85, 6 + $y_cell);
    $pdf->MultiCell(23, 6, utf8_decode($arreglo_alumno['calificacion']), 1, 'C');

    switch ($arreglo_alumno['calificacion']) {
        case 1:
            $arreglo_alumno['calificacion'] = "Cero";
            break;
        case 2:
            $arreglo_alumno['calificacion'] = "Uno";
            break;
        case 3:
            $arreglo_alumno['calificacion'] = "Tres";
            break;
        case 4:
            $arreglo_alumno['calificacion'] = "Cuatro";
            break;
        case 5:
            $arreglo_alumno['calificacion'] = "Cinco";
            break;
        case 6:
            $arreglo_alumno['calificacion'] = "Seis";
            break;
        case 7:
            $arreglo_alumno['calificacion'] = "Siete";
            break;
        case 8:
            $arreglo_alumno['calificacion'] = "Ocho";
            break;
        case 9:
            $arreglo_alumno['calificacion'] = "Nueve";
            break;
        case 10:
            $arreglo_alumno['calificacion'] = "Diez";
            break;
    }

    $pdf->SetXY(108, 6 + $y_cell);
    $pdf->MultiCell(23, 6, utf8_decode($arreglo_alumno['calificacion']), 1, 'C');
    $pdf->SetXY(131, 6 + $y_cell);
    $pdf->Cell(25, 6, $asistencias_alumnos, 1, 0, 'C');
    $pdf->Cell(35, 6, '', 1, 1, 'C');

    $y_cell += 6;
    $y_multicell += 3;
    $id_alumno += 1;
    if( $id_alumno ==30) {
        $y_cell = 50;
        $pdf->AddPage();

    }
        
}
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY(20, 35);
// $pdf->Cell(30, 6, utf8_decode($actividad_nombre[0]['actividad_nombre']), 0, 0, 'L', 0);

$pdf->SetX($marco);
$pdf->line(65, $y_cell+21, 130, $y_cell+21);
$pdf->SetY($y_cell+20);
$pdf->Cell(176, 6, utf8_decode('Firma del Catedrático.'), 0, 1, 'C', 0);




$pdf->Output();
?>