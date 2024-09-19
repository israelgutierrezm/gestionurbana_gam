<?php
include '../../jwt.php';
include "../../vendor/codigo_qr/phpqrcode/qrlib.php";
require('../../vendor/FPDF/fpdf.php');
include '../../controlEscolar/class/asignaturagrupo.class.php';


// $asignatura_grupo_id = $_GET['id_asignatura_grupo'];
// $usuario = Auth::GetData(
//     $jwt  
// );

// if ($_SERVER['REQUEST_METHOD'] == "GET") {
//     foreach ($_GET as $clave => $valor) {
//         ${$clave} = escape_cara($valor);
//     }
// }
$estatus_materias = $_GET['estatus_materias'];
$alumno_id = $_GET['alumno_id'];
$carrera_id = $_GET['carrera_id'];
$orden_jerarquico_id = $_GET['orden_jerarquico_id'];
$estatus_materias = $_GET['estatus_materias'];
if ($estatus_materias == 1) { // materias activas
    $script_estatus_asignatura = ' AND cem.estatus_materia_id = 1 AND validacion = 1';
} else if ($estatus_materias == 0) { // materias finalizadas
    $script_estatus_asignatura = '  AND cem.estatus_materia_id != 1 AND validacion = 0';
}
if($carrera_id == -1){
    $planes_estudio_inscritos_id = $_GET['planes_estudio_inscritos_id'];
    $arreglo_materias = consultaMateriasExtra($alumno_id, $script_estatus_asignatura, $planes_estudio_inscritos_id, $orden_jerarquico_id);
}else{
    $arreglo_materias = consultaMaterias($alumno_id, $script_estatus_asignatura, $carrera_id, $orden_jerarquico_id);
}

class PDF extends FPDF
{
    
    function Header()
    {
        $estatus_materias = $_GET['estatus_materias'];
        $alumno_id = $_GET['alumno_id'];
        $carrera_id = $_GET['carrera_id'];
        $orden_jerarquico_id = $_GET['orden_jerarquico_id'];
        $estatus_materias = $_GET['estatus_materias'];
        if ($estatus_materias == 1) { // materias activas
            $script_estatus_asignatura = ' and cem.estatus = 1';
        } else if ($estatus_materias == 0) { // materias finalizadas
            $script_estatus_asignatura = ' and cem.estatus = 1 and validacion = 0';
        }
        if($carrera_id == -1){
            $planes_estudio_inscritos_id = $_GET['planes_estudio_inscritos_id'];
            $arreglo_materias = consultaMateriasExtra($alumno_id, $script_estatus_asignatura, $planes_estudio_inscritos_id, $orden_jerarquico_id);
        }else{
            $arreglo_materias = consultaMaterias($alumno_id, $script_estatus_asignatura, $carrera_id, $orden_jerarquico_id);
        }
        $arreglo_alumno = consultaAlumno($alumno_id);
        $nombre_ciclo = consultaCiclo($carrera_id);
        $this->SetFont('Arial', 'B', 30);
        $this->Ln(20);
        $logo_nivel = arreglo(query('SELECT cne.nivel_estudios, cne.url_imagen FROM tr_carrera tc 
        JOIN cat_nivel_estudios cne on cne.nivel_estudios_id = tc.nivel_estudios_id
        WHERE tc.estatus = 1 AND tc.carrera_id ='.$carrera_id));
        if(isset($logo_nivel['url_imagen']) || $logo_nivel['url_imagen']!=null || $logo_nivel['url_imagen']!=''){
            $this->Image($GLOBALS['url_front_assets'].$logo_nivel['url_imagen'], 15, 5, 45, 35);
        }else{
            $this->Image($GLOBALS['url_front'].'assets/images/logo.png', 15, 5, 45, 35, 'png');} 
        $this->SetFont('Arial', '', 10);
        $this->SetXY(90, 13);
        $this->SetXY(70, 18);
        $this->Cell(70, 10, 'Boleta Parcial', 0, 1, 'C', 0);
        $this->SetFont('Arial', 'B', 10);
        $this->SetXY(91, 26);
        $query_tipo_plan = query('SELECT cne.nivel_estudios, cne.url_imagen FROM tr_carrera tc 
        JOIN cat_nivel_estudios cne on cne.nivel_estudios_id = tc.nivel_estudios_id
        WHERE tc.estatus = 1 AND tc.carrera_id ='.$carrera_id);
        $nivel = arreglo($query_tipo_plan)['nivel_estudios'];
        $this->Cell(30, 6, $nivel, 0, 0, 'L', 0);

        $posicion_x = 15;
        $this->Ln(11);
        $this->SetFont('Arial', 'B', 8);
        $this->SetXY(75, 38);
        $this->Cell(30, 6, 'INCORPORADA A LA DGB CON RVOE', 0, 0, 'C', 0);
        $this->SetXY(125, 38);
        $this->Cell(30, 6, '-  SEP', 0, 0, 'L', 0);

        $this->Ln(6);
        $this->SetFont('Arial', 'B', 8);
        $this->SetXY(15, 45);
        $this->Cell(45, 6, utf8_decode('Nombre: ' . $arreglo_alumno['nombre_alumno'] . ''), 0, 0, 'L', 0);
        $this->SetXY(15, 50);
        $this->SetX($posicion_x);
        $this->Cell(25, 6, 'Clave del alumno: ' . $arreglo_alumno['clave_alumno'] . '', 0, 0, 'L', 0);
        // $this->Cell(10, 6, 'Semestre', 0, 1, 'C', 0);
        // $this->SetXY(97, 44);
        // $this->Cell(5, 6, '-a-', 0, 1, 'C', 0);
        $this->SetXY(120, 44);
        if(!empty($arreglo_materias)){
            $this->Cell(10, 6, 'Grupo: '.$arreglo_materias[0]['grupo'].'', 0, 1, 'C', 0);
        }else{
            $this->Cell(10, 6, 'Grupo:', 0, 1, 'C', 0);
        }
        // $this->SetXY(130, 44);
        // $this->Cell(10, 6, 'a', 0, 1, 'C', 0);


        // $this->SetX($posicion_x);
        // $this->Cell(11, 6, 'MATERIA', 0, 0, 'L', 0);
        $this->Cell(79, 6, '', 0, 0, 'L', 0);
        $this->Cell(18, 6, 'Periodo Escolar: ' . $nombre_ciclo . '', 0, 0, 'L', 0);
        $this->Ln(3);
    }

    function Footer()
    {

        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
    }
}


$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$posicion_x = 15;
$pdf->Ln(5);
$pdf->SetX($posicion_x-2);
$pdf->SetFont('Arial', '', 7);
$pdf->Cell(52, 14, 'Asignaturas', 1, 0, 'C', 0);
$pdf->Cell(23, 7, '1er parcial', 1, 0, 'C', 0);
$pdf->Cell(23, 7, '2do parcial', 1, 0, 'C', 0);
$pdf->Cell(23, 7, '3er parcial', 1, 0, 'C', 0);
$pdf->Cell(23, 7, '4to parcial', 1, 0, 'C', 0);
$pdf->Cell(23, 7, '5er parcial', 1, 0, 'C', 0);
$pdf->Cell(23, 7, '6to parcial', 1, 0, 'C', 0);
$pdf->Ln(7);
$pdf->SetX(65);
$pdf->Cell(23, 7, 'cal', 1, 0, 'C', 0);
$pdf->Cell(23, 7, 'cal', 1, 0, 'C', 0);
$pdf->Cell(23, 7, 'cal', 1, 0, 'C', 0);
$pdf->Cell(23, 7, 'cal', 1, 0, 'C', 0);
$pdf->Cell(23, 7, 'cal', 1, 0, 'C', 0);
$pdf->Cell(23, 7, 'cal', 1, 0, 'C', 0);
$pdf->Ln(7);
$pdf->SetX($posicion_x);
$posicion_y = 72;


$pdf->SetFont('Arial', '', 6);
foreach ($arreglo_materias as $i => $materia) {
    $pdf->SetXY(13,$posicion_y);
    $pdf->Cell(52, 7, utf8_decode($materia['asignatura']), 1, 0, 'L', 0);
    foreach ($materia['modulos'] as $key => $modulo) {
        if(isset($modulo['calificacion'])){
            $pdf->Cell(23, 7, $modulo['calificacion'], 1, 0, 'C', 0);
        }
    }
    $posicion_y += 7;
    $recorre_x= 0;
    $i= 6;    
    for ($i=0; $i < $f ; $i++) { 
        $pdf->SetXY(105 + $recorre_x,72 + $recorre_y);
        $pdf->Cell(15, 7, '', 1, 0, 'C', 0);
        $recorre_x += 15;
    }

    if($nivel_estudios_id['nivel_estudios_id'] == 9 || $nivel_estudios_id['nivel_estudios_id'] == 10){
    }else{
        $pdf->SetXY($x_calfin,$posicion_y-7);
        $pdf->Cell($x_celda, 7, utf8_decode($materia['calificacion']), 1, 0, 'C', 0);
    }
    

    $recorre_y += 7;
}

$pdf->SetXY(80, 260);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(52, 6, utf8_decode('Sello de la institución'), 0, 0, 'C', 0);
$pdf->Output();


function consultaAlumno($alumno_id)
{
    $arreglo_alumno = arreglo(query('SELECT p.persona_id, CONCAT(p.nombre," ",p.primer_apellido," ",p.segundo_apellido) AS nombre_alumno, ta.clave_alumno  
    FROM ' . $GLOBALS["db_datosGenerales"] . '.personas p 
    JOIN ' . $GLOBALS["db_controlEscolar"] . '.tr_alumno ta on ta.alumno_id = p.persona_id
    WHERE ta.alumno_id=' . $alumno_id . ''));
    return $arreglo_alumno;
}

function consultaCiclo($carrera_id)
{
    $arreglo_ciclo = arreglo(query('SELECT cc.ciclo_desc FROM ' . $GLOBALS["db_controlEscolar"] . '.inter_carrera_ciclo icc 
        Join ' . $GLOBALS["db_controlEscolar"] . '.cat_ciclo cc on cc.ciclo_id = icc.ciclo_id
        WHERE icc.carrera_id=' . $carrera_id . ''));
    if (!empty($arreglo_ciclo)) {
        return $arreglo_ciclo['ciclo_desc'];
    } else {
        return $arreglo_ciclo['ciclo_desc'] = '';
    }
}

function consultaMaterias($id_alumno, $script_estatus_asignatura, $carrera_id, $orden_jerarquico_id)
{
    $query_materias = query('SELECT tm.materia_id,tg.grupo,tg.nombre_grupo, tm.calificacion, ca.asignatura_id, ca.asignatura, tm.estatus_materia_id, 
        UNIX_TIMESTAMP(iag.fecha_inicio) as fecha_inicio, UNIX_TIMESTAMP(iag.fecha_fin) as fecha_fin, ca.url_imagen_previa,tm.asignatura_grupo_id,
         tm.tipo_materia_id,validacion
        FROM ' . $GLOBALS["db_learning"] . '.tr_materia tm
        join ' . $GLOBALS["db_controlEscolar"] . '.inter_asignatura_grupo iag ON tm.asignatura_grupo_id = iag.asignatura_grupo_id
        join ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa ON ioa.orden_asignatura_id = iag.orden_asignatura_id
        join ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
        join ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg on tg.grupo_id = iag.grupo_id
        join ' . $GLOBALS["db_learning"] . '.cat_estatus_materia cem on cem.estatus_materia_id = tm.estatus_materia_id
        join ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo on ipo.plan_orden_id = ioa.plan_orden_id
        join ' . $GLOBALS["db_datosGenerales"] . '.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
        JOIN ' . $GLOBALS["db_datosGenerales"] . '.cat_orden_jerarquico coj on coj.orden_jerarquico_id = ipo.orden_jerarquico_id
        join ' . $GLOBALS["db_datosGenerales"] . '.tr_carrera tc on tpe.carrera_id = tc.carrera_id
        WHERE tm.estatus = 1 and tm.estatus = 1 and iag.estatus=1 and alumno_id = ' . $id_alumno . $script_estatus_asignatura . ' 
        AND tc.carrera_id =' . $carrera_id.' AND coj.orden_jerarquico_id ='.$orden_jerarquico_id);
    if (num($query_materias)) {
        while ($arreglo_materia = arreglo($query_materias)) {
            $arreglo_materia['modulos'] = consultaModulos($arreglo_materia['asignatura_grupo_id'], $arreglo_materia['materia_id']);
            $arreglo_materias[] = $arreglo_materia;
        }
    } else {
        $arreglo_materias = [];
    }
    // echo '<pre>';
    // print_r($arreglo_materias);
    // echo '</pre>';
    return $arreglo_materias;
}

function consultaMateriasExtra($alumno_id,$script_estatus_asignatura, $planes_estudio_inscritos_id, $orden_jerarquico_id)
{
  $planes_estudio_inscritos_id = ('0' . $planes_estudio_inscritos_id);
  $query_asignaturas = query('SELECT  tm.materia_id, tg.grupo, tg.nombre_grupo,tm.calificacion, ca.asignatura_id, ca.asignatura,tm.estatus_materia_id, UNIX_TIMESTAMP(iag2.fecha_inicio) AS fecha_inicio,
   UNIX_TIMESTAMP(iag2.fecha_fin) AS fecha_fin,ca.url_imagen_previa, tm.asignatura_grupo_id, tm.tipo_materia_id
  FROM ' . $GLOBALS["db_controlEscolar"] . '.inter_alumno_asignatura_grupo iaag
  JOIN ' . $GLOBALS["db_controlEscolar"] . '.inter_asignatura_grupo iag on iaag.asignatura_grupo_id = iag.asignatura_grupo_id
  JOIN ' . $GLOBALS["db_controlEscolar"] . '.inter_asignatura_grupo iag2 on iag2.asignatura_grupo_id = iag.asignatura_grupo_id
  JOIN ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg on tg.grupo_id = iag2.grupo_id
  JOIN  ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa on ioa.orden_asignatura_id = iag2.orden_asignatura_id
  JOIN  ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo on ipo.plan_orden_id = ioa.plan_orden_id
  JOIN  ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca on ca.asignatura_id = ioa.asignatura_id
  JOIN ' . $GLOBALS["db_learning"] . '.tr_materia tm on tm.asignatura_grupo_id = iag.asignatura_grupo_id
  JOIN  ' . $GLOBALS["db_learning"] . '.cat_estatus_materia cem on cem.estatus_materia_id = tm.estatus_materia_id
  WHERE tm.alumno_id = ' . $alumno_id . ' AND iag.estatus = 1 AND ipo.orden_jerarquico_id = '.$orden_jerarquico_id.' AND iag2.estatus = 1  ' . $script_estatus_asignatura . ' 
  AND ipo.plan_estudio_id NOT IN (' . $planes_estudio_inscritos_id . ')
  UNION  
  SELECT tm.materia_id, tg.grupo, tg.nombre_grupo,tm.calificacion, ca.asignatura_id, ca.asignatura,tm.estatus_materia_id, UNIX_TIMESTAMP(iag2.fecha_inicio) AS fecha_inicio,
  UNIX_TIMESTAMP(iag2.fecha_fin) AS fecha_fin,ca.url_imagen_previa, tm.asignatura_grupo_id, tm.tipo_materia_id
  FROM ' . $GLOBALS["db_controlEscolar"] . '.inter_alumno_grupo iag
  JOIN ' . $GLOBALS["db_controlEscolar"] . '.inter_asignatura_grupo iag2 ON iag2.grupo_id = iag.grupo_id
  JOIN ' . $GLOBALS["db_controlEscolar"] . '.tr_grupo tg ON tg.grupo_id = iag2.grupo_id
  JOIN  ' . $GLOBALS["db_datosGenerales"] . '.inter_orden_asignatura ioa ON ioa.orden_asignatura_id = iag2.orden_asignatura_id
  JOIN  ' . $GLOBALS["db_datosGenerales"] . '.inter_plan_orden ipo ON ipo.plan_orden_id = ioa.plan_orden_id
  JOIN  ' . $GLOBALS["db_datosGenerales"] . '.cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
  JOIN ' . $GLOBALS["db_learning"] . '.tr_materia tm ON tm.asignatura_grupo_id = iag2.asignatura_grupo_id AND tm.alumno_id = ' . $alumno_id . '
  JOIN  ' . $GLOBALS["db_learning"] . '.cat_estatus_materia cem on cem.estatus_materia_id = tm.estatus_materia_id
  WHERE iag.alumno_id = ' . $alumno_id . ' AND iag.estatus = 1  AND ipo.orden_jerarquico_id = '.$orden_jerarquico_id.' AND iag2.estatus = 1 ' . $script_estatus_asignatura . ' 
  AND ipo.plan_estudio_id NOT IN (' . $planes_estudio_inscritos_id . ');');
  $arreglo_asignaturas_extra = [];
  while ($arreglo_asignatura = arreglo($query_asignaturas)) {
    $arreglo_asignatura['modulos'] = consultaModulos($arreglo_asignatura['asignatura_grupo_id'], $arreglo_asignatura['materia_id']);
    $arreglo_asignaturas_extra[] = $arreglo_asignatura;
  }
  return $arreglo_asignaturas_extra;
}

function consultaModulos($asignatura_grupo_id, $materia_id){
    $asignaturaGrupo = new AsignaturaGrupo();
    $query_modulos = $asignaturaGrupo::consultaTodosModulos($asignatura_grupo_id);
    $arreglo_modulos = [];
    if(num($query_modulos)){
        while ($arreglo_modulo = arreglo($query_modulos)){
            $arreglo_modulo['calificacion'] = consultaCalificacionModulo($arreglo_modulo['modulo_id'],$materia_id);
            $arreglo_modulos[] = $arreglo_modulo;
        }
    }
    // $arreglo_modulos['num_modulos'] = intval(num($query_modulos));
    return $arreglo_modulos;
}

function consultaCalificacionModulo($modulo_id, $materia_id){
    $query_calificacion = query('SELECT tmfa.calificacion FROM ' . $GLOBALS["db_learning"] . '.tr_actividad ta 
    JOIN ' . $GLOBALS["db_learning"] . '.tr_materia_fecha_actividad tmfa ON tmfa.actividad_id = ta.actividad_id AND tmfa.materia_id = '.$materia_id.'
    WHERE ta.modulo_id = '.$modulo_id.' AND ta.tipo_actividad_id = 5 AND ta.actividad_nombre NOT LIKE "%VERIFIN%"');
    if(num($query_calificacion) > 0){
        while ($arreglo_calificacion = arreglo($query_calificacion)) {
            return $arreglo_calificacion['calificacion'];
        }
    }else{
        return null;
    }
}