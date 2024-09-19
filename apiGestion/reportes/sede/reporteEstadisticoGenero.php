<?php
include '../../jwt.php';
include '../../headers.php';
include "../../vendor/codigo_qr/phpqrcode/qrlib.php";
require('../../vendor/FPDF/fpdf.php');

class PDF extends FPDF
{
    function Header()
    {
        // Logo
        $this->Image($GLOBALS['url_front'] . 'assets/images/logo.png', 10, 13, 30, 30);
        $this->SetFont('Times', 'B', 18);
        $this->Cell(80);
        // Título
        $this->SetTextColor(22, 21, 90);
        $this->Cell(70, 10, utf8_decode('Universidad Católica Lumen Gentium, A.C. Plantel Talpan'), 0, 1, 'C');
        $this->SetFont('Times', 'B', 11);
    }

    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Times', 'I', 8);
        // Número de página
        $this->Cell(0, 10, utf8_decode('Página') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

function getGeneroByCurp($curp)
{
    if (isset($curp) && strlen($curp) > 16) {
        $sexo = substr($curp, 10, 1);
        return $sexo;
    }
    return null;
}
//_____________________________________________________________________________________________________________________________________________________________________
function reportePorCiclo($cicloId)
{
    global $yActual, $yInicioPagina, $pdf, $ySede;
    $ySede = 45;
    $pdf->SetY($ySede);
    $pdf->SetFont('Times', 'B', 12);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(110, 0, utf8_decode('Sede: 1 Tlalpan '), 0, 0, 'R');
    $queryCiclo = query('SELECT ciclo_desc FROM ' . $GLOBALS["db_controlEscolar"] . '.cat_ciclo WHERE estatus = 1 and ciclo_id = ' . $cicloId);

    if (num($queryCiclo)) {
        //Consulta grupos en ciclo
        // $queryAlumnosEnGrupo = query('SELECT tg.grupo_id
        // FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg 
        // JOIN ' . $GLOBALS["db_controlEscolar"] . '.cat_ciclo cc ON cc.ciclo_id = tg.ciclo_id 
        // WHERE cc.ciclo_id = ' . $cicloId . ' AND tg.estatus = 1');

        $ciclo = arreglo($queryCiclo)['ciclo_desc'];
        pintarCabeceraCiclo($ciclo);
        //Consulta el ciclo
        $queryCarrerasEnCiclo = query('SELECT cc.ciclo_desc, tc.carrera, tc.carrera_id, tpe.plan_estudio_id 
        FROM ' . $GLOBALS["db_controlEscolar"] . '.cat_ciclo cc 
        JOIN ' . $GLOBALS["db_controlEscolar"] . '.inter_carrera_ciclo icc ON icc.ciclo_id = cc.ciclo_id 
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_carrera tc  ON tc.carrera_id = icc.carrera_id 
        join ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.carrera_id = tc.carrera_id 
        WHERE cc.ciclo_id = ' . $cicloId . ' AND cc.estatus = 1  AND icc.estatus =1 AND tc.estatus =1 AND tpe.estatus =1');
        while ($arregloCarrerasEnCiclo = arreglo($queryCarrerasEnCiclo)) {
            if ($yActual >= 250) {
                $yActual = $yInicioPagina;
                $pdf->AddPage();
                $pdf->SetY($yActual);
            }
            reportePorPlan($arregloCarrerasEnCiclo['plan_estudio_id'],$cicloId);
        }
    }
}

function reportePorPlan($planEstudioId,$cicloId)
{
    global $tipoReporte;
    $queryCarreraDelPlan = query('SELECT tc.carrera
        FROM ' . $GLOBALS["db_datosGenerales"] . '.tr_carrera tc 
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.carrera_id = tc.carrera_id
        WHERE tpe.plan_estudio_id =' . $planEstudioId . ' AND tpe.estatus = 1 AND tc.estatus = 1');

    if (num($queryCarreraDelPlan)) {
        $carrera = arreglo($queryCarreraDelPlan)['carrera'];
        if ($tipoReporte === 2) {
            //Consultar el ciclo de la carrera y pintarlo
        $queryCiclo = query('SELECT distinct  cc.ciclo_desc  
            FROM ' . $GLOBALS["db_datosGenerales"] . '.tr_carrera tc  
            JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe ON tpe.carrera_id  = tc.carrera_id 
            JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo ON ipo.plan_estudio_id  = tpe.plan_estudio_id  
            JOIN ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg ON tg.plan_orden_id  = ipo.plan_orden_id
            JOIN ' . $GLOBALS["db_controlEscolar"] . '.cat_ciclo cc ON cc.ciclo_id = tg.ciclo_id 
            WHERE tpe.plan_estudio_id =' . $planEstudioId .' and cc.ciclo_id = '.$cicloId.' and tc.estatus =1 and ipo.estatus = 1 and tg.estatus = 1 AND tpe.estatus=1
             AND cc.estatus =1 AND tc.estatus =1 AND ipo.estatus =1');
            if (num($queryCiclo) > 0) {
                $ciclo = arreglo($queryCiclo)['ciclo_desc'];
                pintarCabeceraCiclo($ciclo);
            }
            pintarCabeceraCarrera($carrera);
        }
        pintarTituloCarrera($carrera);
        $queryGruposEnPlan = query('SELECT tg.grupo_id
        FROM ' . $GLOBALS["db_datosGenerales"] . '.tr_carrera tc 
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.carrera_id = tc.carrera_id 
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo  on ipo.plan_estudio_id = tpe.plan_estudio_id 
        JOIN ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg on tg.plan_orden_id = ipo.plan_orden_id 
        WHERE tpe.plan_estudio_id =' . $planEstudioId . ' and tg.ciclo_id = '.$cicloId.' AND tpe.estatus = 1 AND tc.estatus =1 AND ipo.estatus =1 AND tg.estatus =1');
        if (num($queryGruposEnPlan)>0) {
            $totalHombresEnPlan = 0;
            $totalMujeresEnPlan = 0;
            while ($arregloGrupoEnPlan = arreglo($queryGruposEnPlan)) {
                $datosReportePorGrupo = reportePorGrupo($arregloGrupoEnPlan['grupo_id'],'');
                $totalHombresEnPlan += $datosReportePorGrupo['totalHombresEnGrupo'];
                $totalMujeresEnPlan += $datosReportePorGrupo['totalMujeresEnGrupo'];
            }
            pintarTotales($totalHombresEnPlan, $totalMujeresEnPlan);
        }else{
            reportePorGrupo(-1,'');
        }
    }
}


function reportePorGrupo($grupoId, $cicloId)
{
    global $pdf, $arregloGrupo, $yActual, $tipoReporte, $yInicioPagina, $sumaTotalHombres, $sumaTotalMujeres;
    if ($grupoId != -1) {
        $queryGrupo = query('SELECT grupo FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo WHERE estatus = 1 and grupo_id = ' . $grupoId);
    }
    if ($grupoId != -1 && num($queryGrupo) > 0) {
        $arregloGrupo = arreglo($queryGrupo);
        if ($tipoReporte === 3) {
            //Consultar cabecera de ciclo e imprime 
            $queryCiclo = query('SELECT cc.ciclo_desc  
            FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg 
            JOIN ' . $GLOBALS["db_controlEscolar"] . '.cat_ciclo cc on cc.ciclo_id = tg.ciclo_id 
            WHERE tg.grupo_id =' . $grupoId .' and cc.ciclo_id = '.$cicloId.' AND tg.estatus = 1 AND cc.estatus = 1');
            if (num($queryCiclo) > 0) {
                $ciclo = arreglo($queryCiclo)['ciclo_desc'];
                pintarCabeceraCiclo($ciclo);
            }
            //Consulta los grupos e imprime
            $queryCarreraDelGrupo = query('SELECT tc.carrera 
            FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg 
            JOIN ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo  on ipo.plan_orden_id = tg.plan_orden_id 
            JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id 
            JOIN ' . $GLOBALS["db_datosGenerales"] . '.tr_carrera tc on tc.carrera_id = tpe.carrera_id 
            WHERE tg.grupo_id = ' . $grupoId . ' and tg.ciclo_id = '.$cicloId.' AND  tc.estatus = 1 AND ipo.estatus =1 AND tg.estatus =1 AND tpe.estatus = 1');
            if (num($queryCarreraDelGrupo) > 0) {
                $carrera = arreglo($queryCarreraDelGrupo)['carrera'];
                pintarTituloCarrera($carrera);
            }
            //Consulta grupos e imprime 
            if (num($queryGrupo) > 0) {
                $grupo = arreglo($queryGrupo)['grupo'];
                pintarCabeceraGrupo($grupo, $carrera, $ciclo);
            }
        }
        //Consulta los grupos
        $queryAlumnosEnGrupo = query('SELECT tg.grupo, tg.nombre_grupo, cc.ciclo_desc, p.curp, cs.sexo_id 
        FROM ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg 
        JOIN ' . $GLOBALS["db_controlEscolar"] . '.inter_alumno_grupo iag ON iag.grupo_id = tg.grupo_id 
        JOIN ' . $GLOBALS["db_controlEscolar"] . '.cat_ciclo cc ON cc.ciclo_id = tg.ciclo_id 
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.personas p  ON p.persona_id = iag.alumno_id 
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_sexo cs on cs.sexo_id = p.sexo_id 
        WHERE tg.grupo_id = ' . $grupoId . ' AND tg.estatus = 1 AND iag.estatus = 1 AND cc.estatus = 1 AND p.estatus = 1 AND cs.estatus = 1');

        $totalHombresEnGrupo = 0;
        $totalMujeresEnGrupo = 0;
        if (num($queryAlumnosEnGrupo) > 0) {
            $totalHombresEnGrupo = 0;
            $totalMujeresEnGrupo = 0;
            while ($alumno = arreglo($queryAlumnosEnGrupo)) {
                if (isset($alumno['sexo_id']) && $alumno['sexo_id'] != 0) {
                    switch ($alumno['sexo_id']) {
                        case '1':
                            $totalHombresEnGrupo++;
                            $sumaTotalHombres++;
                            break;
                        case '2':
                            $totalMujeresEnGrupo++;
                            $sumaTotalMujeres++;
                            break;
                        default:
                            break;
                    }
                } else {
                    $sexo = getGeneroByCurp($alumno['curp']);
                    switch ($sexo) {
                        case 'H':
                            $totalHombresEnGrupo++;
                            $sumaTotalHombres++;
                            break;
                        case 'M':
                            $totalMujeresEnGrupo++;
                            $sumaTotalMujeres++;
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        $sumaTotalAlumnosEnGrupo = $totalHombresEnGrupo + $totalMujeresEnGrupo;
        if ($sumaTotalAlumnosEnGrupo > 0) {
            $porcentajeHombres = round(($totalHombresEnGrupo / $sumaTotalAlumnosEnGrupo) * 100);
            $porcentajeMujeres = round(($totalMujeresEnGrupo / $sumaTotalAlumnosEnGrupo) * 100);
        } else {
            $porcentajeHombres = 0;
            $porcentajeMujeres = 0;
        }
        if($grupoId != null){
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Times', '', 8);
            $pdf->Cell(80, 6, utf8_decode($arregloGrupo['grupo']), 0, 0, 'C');
            $pdf->Cell(30, 6, utf8_decode($totalHombresEnGrupo . ' (' . $porcentajeHombres . '%)'), 0, 0, 'C');
            $pdf->Cell(35, 6, utf8_decode($totalMujeresEnGrupo . ' (' . $porcentajeMujeres . '%)'), 0, 0, 'C');
            $pdf->Cell(45, 6, utf8_decode($sumaTotalAlumnosEnGrupo), 0, 1, 'C');
        }
        
        if ($yActual >= 262) {
            $yActual = $yInicioPagina;
            $pdf->AddPage();
            $pdf->SetY($yActual);
        } else {
            $yActual += 6;
        }
        
        if ($tipoReporte === 3) {
            pintarTotales($totalHombresEnGrupo, $totalMujeresEnGrupo);
        }
        
        return ['totalHombresEnGrupo' => $totalHombresEnGrupo, 'totalMujeresEnGrupo' => $totalMujeresEnGrupo];
    }else{
        if ($yActual >= 262) {
            $yActual = $yInicioPagina;
            $pdf->AddPage();
            $pdf->SetY($yActual);
        } else {
            $yActual += 6;
        }
        $pdf->Cell(120, 6, utf8_decode('Esta carrera no cuenta con grupos'), 0, 1, 'R');    }
    
}
//_____________________________________________________________________________________________________________________________________________________________________
function pintarCabeceraCiclo($ciclo)
{
    global $pdf, $yCabecera;
    $pdf->SetY($yCabecera);
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(80);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(70, 0, utf8_decode($ciclo), 0, 0, 'C');
    $pdf->Ln(2);
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(80);
    $pdf->SetTextColor(48, 56, 218);
    $pdf->Cell(70, 10, utf8_decode('RESUMEN DE LA POBLACION ESCOLAR'), 0, 0, 'C');
    $yCabecera += 16;
    $pdf->SetTextColor(0, 0, 0);
}
function pintarCabeceraCarrera($carrera)
{
    global $pdf, $gActual;
    $pdf->SetY($gActual);
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(80);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(30, 10, utf8_decode($carrera . ',' . ' ' . 'Sede: 1 Tlalpan '), 0, 1, 'C');
    $pdf->ln(18);
}

function pintarCabeceraGrupo($grupo, $carrera, $ciclo)
{
    global $pdf, $gActual;
    $pdf->SetY($gActual);

    $pdf->Cell(80);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(30, 10, utf8_decode('Grupo' . '" ' . $carrera . '"' . ' de ' . $grupo . $ciclo), 0, 1, 'C');
    $pdf->ln(18);
}

function pintarTituloCarrera($carrera)
{
    global $pdf, $yActual;
    $pdf->SetY($yActual);
    $pdf->SetFont('Times', 'BU', 13);
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(190, 10, utf8_decode($carrera), 'lTrb', 1, 'C', 1);
    $pdf->SetFont('Times', 'BU', 10);
    $pdf->SetTextColor(22, 21, 90);
    $pdf->Cell(80, 6, utf8_decode('Grupo'), 0, 0, 'C', 1);
    $pdf->Cell(30, 6, utf8_decode('Hombres'), 0, 0, 'C', 1);
    $pdf->Cell(35, 6, utf8_decode('Mujeres'), 0, 0, 'C', 1);
    $pdf->Cell(45, 6, utf8_decode('TOTAL'), 0, 1, 'C', 1);
    $yActual += 16;
}

function pintarTotales($sumaTotalHombres, $sumaTotalMujeres, $esResumenGeneral = false)
{
    $sumaTotalAlumnos = $sumaTotalHombres + $sumaTotalMujeres;
    if ($sumaTotalAlumnos > 0) {
        $porcentajeTotalHombres = round(($sumaTotalHombres / $sumaTotalAlumnos) * 100);
        $porcentajeTotalMujeres = round(($sumaTotalMujeres / $sumaTotalAlumnos) * 100);
    } else {
        $porcentajeTotalHombres = 0;
        $porcentajeTotalMujeres = 0;
    }
    $arregloDatos = [
        'sumaTotalHombres' => $sumaTotalHombres, 'porcentajeTotalHombres' => $porcentajeTotalHombres,
        'sumaTotalMujeres' => $sumaTotalMujeres, 'porcentajeTotalMujeres' => $porcentajeTotalMujeres,
        'sumaTotalAlumnos' => $sumaTotalAlumnos
    ];
    pintarValores($arregloDatos, $esResumenGeneral);
}

function pintarValores($datos, $esResumenGeneral)
{
    global $pdf, $yActual;
    $pdf->SetY($yActual);
    if ($esResumenGeneral) {
        $pdf->SetFont('Times', 'B', 11);
        $pdf->Cell(80, 10, utf8_decode('Resumen General'), 0, 0, 'C');
    } else {
        $pdf->SetFont('Times', 'B', 8);
        $pdf->Cell(80, 6, utf8_decode('TOTALES:'), 0, 0, 'R');
    }
    $pdf->Cell(30, 6, utf8_decode($datos['sumaTotalHombres'] . ' (' . $datos['porcentajeTotalHombres'] . '%)'), 0, 0, 'C');
    $pdf->Cell(35, 6, utf8_decode($datos['sumaTotalMujeres'] . ' (' . $datos['porcentajeTotalMujeres'] . '%)'), 0, 0, 'C');
    $pdf->Cell(45, 6, utf8_decode($datos['sumaTotalAlumnos']), 0, 1, 'C');
    $yActual += 15;
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$anchoPagina = $pdf->GetPageWidth();
$arregloGrupo = null;
$sumaTotalHombres = 0;
$sumaTotalMujeres = 0;
$yInicioPagina = 50;
$yActual = $yInicioPagina;
$yCabecera = 25;
$gActual = 39;
$pdf->SetY($yActual);
$tipoReporte = null;


if (isset($_GET['cicloId']) && !isset($_GET['planEstudioId']) && !isset($_GET['grupoId'])) {
    // Carreras
    $tipoReporte = 1;
    reportePorCiclo($_GET['cicloId']);
} else if (isset($_GET['planEstudioId']) && isset($_GET['cicloId'])) {
    // plan
    $tipoReporte = 2;
    reportePorPlan($_GET['planEstudioId'],$_GET['cicloId']);
} else if (isset($_GET['grupoId'])) {
    // grupo
    $tipoReporte = 3;
    reportePorGrupo($_GET['grupoId'],$_GET['cicloId']);
}

pintarTotales($sumaTotalHombres, $sumaTotalMujeres, true);

$pdf->Output();
