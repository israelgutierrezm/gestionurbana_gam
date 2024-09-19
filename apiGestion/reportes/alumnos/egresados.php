<?php

include '../../jwt.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';


try {

    db('datosGenerales');

    if ($_SERVER['REQUEST_METHOD'] == "GET") {

        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        if (isset($id_alumnos)) {
            $condicion_query = ' AND ta.alumno_id in (' . $id_alumnos . ')';
        } else {
            $condicion_query = '';
        }

        $condicion_query .= ' AND tc.carrera_id = ' . $id_carrera;

        $consulta_campus_administrador = arreglo(query('SELECT GROUP_CONCAT( ica.campus_id ) as campuses
            FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_administrador ta
            JOIN ' . $GLOBALS['db_controlEscolar'] . '.inter_campus_administrador ica ON ta.administrador_id = ica.administrador_id
            WHERE ta.estatus = 1 AND ica.estatus = 1 AND ta.administrador_id = ' . $usuario_id));
            if ($consulta_campus_administrador['campuses'] == '') {
                $script =' ';
            }else{
              $campus_administrador = $consulta_campus_administrador['campuses'];
              $script = 'and iap.campus_id IN ('.$campus_administrador.')';
            }

        $query = query('SELECT t1.alumno_id,
        20034 as institucion_id,
        090491 as clave_campus,
        09 as entidad_federativa_id,
        "GODC481211MDFMSL00" as curp_responsable,
        "nombre" as nombre_responsable_emision,
        "primer_apellido" as primer_apellido_emision,
        "segundo_apellido" as segundo_apellido_emision,
        1 as cargo_responsable_id,
        t1.clave_alumno,
        t1.curp,
        t1.nombre,
        t1.primer_apellido,
        t1.segundo_apellido,
        t1.genero_id,
        t1.fecha_nacimiento,
        "" as foto,
        "" as firma_autografa,
        "" as tipo_certificacion_id,
        "" as tipo_certificacion,
        "" as fecha_expedicion,
        09 as id_lugar_expedicion,
        "CIUDAD DE MÉXICO" as lugar_expedicion,
        t1.tipo_periodo_id,
        t2.numero_asignaturas,
        t1.plan_estudio_clave,
        t1.plan_estudio,
        t1.rvoe,
        "" as fecha_rvoe,
        t1.carrera_id,
        t3.numero_asignaturas_cursadas,
        cast(t4.promedio_general as decimal(10,2)) as promedio_general,
        t3.asignatura_id,
        t3.asignatura_clave,
        t3.asignatura,
        t3.ciclo_desc as ciclo,
        t3.calificacion,
        "" as observaciones_id,
        "" as observaciones
        FROM (SELECT 
        ta.clave_alumno,
        ta.alumno_id, 
        ta.situacion_alumno_id,
        p.curp, 
        p.nombre, 
        p.primer_apellido, 
        p.segundo_apellido,
        IF(SUBSTRING(p.curp, 11,1) = "M", "250", "251") AS genero_id,
        DATE_FORMAT(p.fecha_nacimiento , "%d/%m/%Y") as fecha_nacimiento,
        ctp.periodo_clave as tipo_periodo_id,
        ipo.plan_estudio_id,
        tpe.plan_estudio_clave,
        tpe.plan_estudio,
        tc.carrera_id, tpe.rvoe
        FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_alumno ta 
        JOIN personas p ON p.persona_id = ta.alumno_id
        JOIN ' . $GLOBALS['db_controlEscolar'] . '.inter_alumno_plan iap ON iap.alumno_id = ta.alumno_id 
        JOIN inter_plan_orden ipo ON ipo.plan_orden_id = iap.plan_orden_id
        JOIN tr_plan_estudios tpe ON tpe.plan_estudio_id = ipo.plan_estudio_id 
        JOIN cat_tipo_periodo ctp ON ctp.tipo_periodo_id = tpe.tipo_periodo_id
        JOIN tr_carrera tc ON tc.carrera_id = tpe.carrera_id
        WHERE ta.estatus = 1 AND p.estatus = 1 AND iap.estatus = 1 AND ipo.estatus = 1 AND tpe.estatus = 1 AND 
        ctp.estatus = 1 AND tc.estatus = 1 AND p.estatus = 1 AND iap.situacion_alumno_id = 4 ' . $condicion_query . ' '.$script.') AS t1
        LEFT JOIN (SELECT count(*) as numero_asignaturas, ipo.plan_estudio_id, ca.asignatura_id
                    FROM cat_asignaturas ca
                    JOIN cat_tipo_asignaturas cta ON cta.tipo_asignatura_id = ca.tipo_asignatura_id
                    JOIN inter_orden_asignatura ioa ON ioa.asignatura_id = ca.asignatura_id
                    JOIN inter_plan_orden ipo ON ipo.plan_orden_id = ioa.plan_orden_id 
                    WHERE ca.estatus = 1 GROUP by ipo.plan_estudio_id) t2 ON t2.plan_estudio_id = t1.plan_estudio_id
        LEFT JOIN (SELECT st2.numero_asignaturas_cursadas, th.alumno_id,ca.asignatura_id, ca.asignatura,ca.asignatura_clave, cc.ciclo_desc, th.calificacion 
                    FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_historial th 
                    JOIN inter_orden_asignatura ioa ON ioa.orden_asignatura_id = th.orden_asignatura_id
                    JOIN cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
                    JOIN ' . $GLOBALS['db_controlEscolar'] . '.cat_ciclo cc ON cc.ciclo_id = th.ciclo_id 
                    JOIN (SELECT COUNT(DISTINCT ca.asignatura_id) AS numero_asignaturas_cursadas,th.alumno_id 
                    FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_historial th
                    JOIN inter_orden_asignatura ioa ON ioa.orden_asignatura_id = th.orden_asignatura_id
                    JOIN cat_asignaturas ca ON ca.asignatura_id = ioa.asignatura_id
                    WHERE ca.estatus = 1 AND th.estatus = 1 GROUP BY th.alumno_id) st2 ON st2.alumno_id = th.alumno_id 
                    WHERE th.estatus = 1 AND ioa.estatus = 1 AND ca.estatus = 1 AND cc.estatus = 1) t3 ON t3.alumno_id = t1.alumno_id
        JOIN (SELECT ROUND(SUM(th.calificacion)/COUNT(*),2)  AS promedio_general, th.alumno_id 
        FROM ' . $GLOBALS['db_controlEscolar'] . '.tr_historial th 
        WHERE th.estatus = 1 
        GROUP BY th.alumno_id) t4 ON t4.alumno_id = t1.alumno_id order by alumno_id');


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getProperties()

            ->setCreator("Temporaris")
            ->setLastModifiedBy("Temporaris")
            ->setTitle("Template Relevé des heures intérimaires")
            ->setSubject("Template excel")
            ->setDescription("Template excel permettant la création d'un ou plusieurs relevés d'heures")
            ->setKeywords("Template excel");


        $estiloTituloReporte = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,
                'size' => 13
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'F9FD00')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $estiloTituloColumnas = array(
            'font' => array(
                'name'  => 'Arial',
                'bold'  => true,
                'size' => 11,
                'color' => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '538DD5')
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        );

        $estiloInformacion = new PHPExcel_Style();
        $estiloInformacion->applyFromArray(array(
            'font' => array(
                'name'  => 'Arial',
                'size' => 10,
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'fill' => array(
                'type'  => PHPExcel_Style_Fill::FILL_SOLID
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' =>  array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'  => PHPExcel_Style_Alignment::VERTICAL_CENTER
            )
        ));


        //     // $objPHPExcel->createSheet(1);//creamos la pestaña
        //     $objPHPExcel->createSheet(1);
        //     $objPHPExcel->getActiveSheet()->setCellValue('A1','idTipoAsignatura');
        //     $objPHPExcel->getActiveSheet()->setCellValue('B1','Descripcion');


        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("creater");
        $objPHPExcel->getProperties()->setLastModifiedBy("Middle field");
        $objPHPExcel->getProperties()->setSubject("Subject");
        $objWorkSheet = $objPHPExcel->createSheet();
        $work_sheet_count = 9; //number of sheets you want to create
        $work_sheet = 0;
        while ($work_sheet <= $work_sheet_count) {
            if ($work_sheet == 0) {
                $objWorkSheet->setTitle("MIS CERTIFICADOS");

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'Reporte de alumnos egresados');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A1')->applyFromArray($estiloTituloReporte);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A3:AL3')->applyFromArray($estiloTituloColumnas);
                $objPHPExcel->setActiveSheetIndex(0);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setTitle('ReporteEgresados');

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A3', 'Institucion_id');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B3', 'Clave_campus');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C3', 'Entidad_federativa_id');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('D3', 'Curp_responsable');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('E3', 'Nombre_responsable_emision');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('F3', 'Primer_apellido');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('G3', 'Segundo_apellido');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('H3', 'Cargo_responsable_id');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('I3', 'Clave_alumno');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('J3', 'Curp');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('K3', 'Nombre_alumno');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('L3', 'Primer_apellido');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('M3', 'Segundo_apellido');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('N3', 'genero_id');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('O3', 'fecha_nacimiento');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('P3', 'Foto');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('Q3', 'Firma_autografica');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('R3', 'Tipo_certificacion_id');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('S3', 'Tipo_certificacion');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('T3', 'Fecha_expedicion');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('U3', 'Id_lugar_expedicion');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('V3', 'Lugar_expedicion');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('W3', 'Tipo_periodo_id');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('X3', 'Numero_asignaturas');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('Y3', 'Plan_estudios_clave');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('Z3', 'plan_estudio');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AA3', 'Rvoe');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AB3', 'Fecha_rvoe');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AC3', 'Carrera_id');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AD3', 'Numero_asignaturas_cursadas');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AE3', 'Promedio_general');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AF3', 'Asignatura_id');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AG3', 'Asignatura_clave');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AH3', 'Asignatura');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AI3', 'Ciclo');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AJ3', 'Calificacion');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AK3', 'Observaciones_id');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AL3', 'Observaciones');





                $fila = 4;
                while ($arreglo =  arreglo($query)) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A' . $fila, $arreglo['institucion_id']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B' . $fila, $arreglo['clave_campus']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C' . $fila, $arreglo['entidad_federativa_id']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('D' . $fila, $arreglo['curp_responsable']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('E' . $fila, $arreglo['nombre_responsable_emision']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('F' . $fila, $arreglo['primer_apellido_emision']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('G' . $fila, $arreglo['segundo_apellido_emision']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('H' . $fila, $arreglo['cargo_responsable_id']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('I' . $fila, $arreglo['clave_alumno']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('J' . $fila, $arreglo['curp']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('K' . $fila, $arreglo['nombre']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('L' . $fila, $arreglo['primer_apellido']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('M' . $fila, $arreglo['segundo_apellido']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('N' . $fila, $arreglo['genero_id']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('O' . $fila, $arreglo['fecha_nacimiento']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('P' . $fila, $arreglo['foto']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('Q' . $fila, $arreglo['firma_autografa']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('R' . $fila, $arreglo['tipo_certificacion_id']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('S' . $fila, $arreglo['tipo_certificacion']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('T' . $fila, $arreglo['fecha_expedicion']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('U' . $fila, $arreglo['id_lugar_expedicion']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('V' . $fila, $arreglo['lugar_expedicion']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('W' . $fila, $arreglo['tipo_periodo_id']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('X' . $fila, $arreglo['numero_asignaturas']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('Y' . $fila, $arreglo['plan_estudio_clave']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('Z' . $fila, $arreglo['plan_estudio']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AA' . $fila, $arreglo['rvoe']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AB' . $fila, $arreglo['fecha_rvoe']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AC' . $fila, $arreglo['carrera_id']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AD' . $fila, $arreglo['numero_asignaturas_cursadas']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AE' . $fila, $arreglo['promedio_general']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AF' . $fila, $arreglo['asignatura_id']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AG' . $fila, $arreglo['asignatura_clave']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AH' . $fila, $arreglo['asignatura']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AI' . $fila, $arreglo['ciclo']);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AJ' . $fila, $arreglo['calificacion']);
                    if (empty($arreglo['observacion_id'])) {
                        $arreglo['observacion_id'] = '';
                    }
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AK' . $fila, $arreglo['observacion_id']);
                    if (empty($arreglo['observacion'])) {
                        $arreglo['observacion'] = '';
                    }
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('AL' . $fila, $arreglo['observacion']);

                    $fila++;
                }
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setSharedStyle($estiloInformacion, "A4:AL" . $fila);

                foreach (range('A', 'AJ') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
            if ($work_sheet == 1) {
                $objWorkSheet->setTitle("CREDITOS Y TIPOS ASIGNATURA");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'CLAVE_CAMPUS');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B1', 'ID_ CARRERA (Generado por la Institución para el Layout de Programas)');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C1', 'ID_ ASIGNATURA (Generado por la Institución para el Layout de Programas)');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('D1', 'RVOE');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('E1', 'Fecha_RVOE');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('F1', 'CLAVE PLAN ESTUDIOS');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('G1', 'ID_TIPO_ASIGNATURA');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('H1', 'NÚMERO DE CRÉDITOS EN EL PLAN DEL ESTUDIOS');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('I1', 'DESCRIPCIÓN');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('J1', 'CALIF_MÍNIMA (A dos decimales)');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('K1', 'CALIF_MÁXIMA');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('L1', 'MÍNIMA_APROBATORIA (A dos decimales)');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A1:L1')->applyFromArray($estiloTituloColumnas);
                foreach (range('A', 'L') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
            if ($work_sheet == 2) {
                $objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
                $objWorkSheet->setTitle("INSTRUCIONES");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'Instituciones Particulares de Educación Superior')->getStyle('A1')->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A3', 'Instrucciones')->getStyle('A1')->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A1:A1')->applyFromArray($estiloTituloColumnas);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A3:A3')->applyFromArray($estiloTituloColumnas);
                foreach (range('A', 'A') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
            if ($work_sheet == 3) {
                $objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
                $objWorkSheet->setTitle("TIPO DE ASIGNATURA");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'Id tipo Asignatura');
                // $objPHPExcel->getActiveSheet(1)->getStyle('A1')->applyFromArray($estiloTituloReporte);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setSharedStyle($estiloInformacion, "A1");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B1', 'DESCRIPCIÓN');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setSharedStyle($estiloInformacion, "B1");

                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A1:B1')->applyFromArray($estiloTituloColumnas);
                $objPHPExcel->setActiveSheetIndex(1);

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A2', '263');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B2', 'OBLIGATORIA');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A3', '264');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B3', 'OPTATIVA');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A4', '265');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B4', 'ADICIONAL');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A5', '266');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B5', 'COMPLEMENTARIA');

                foreach (range('A', 'B') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
            if ($work_sheet == 4) {
                $objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
                $objWorkSheet->setTitle("ENTIDAD FEDERATIVA");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'ID');
                // $objPHPExcel->getActiveSheet(1)->getStyle('A1')->applyFromArray($estiloTituloReporte);
                // $objPHPExcel->setActiveSheetIndex($work_sheet)->setSharedStyle($estiloInformacion, "A1");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B1', 'ENTIDAD');
                // $objPHPExcel->setActiveSheetIndex($work_sheet)->setSharedStyle($estiloInformacion, "B1");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A1:B1')->applyFromArray($estiloTituloColumnas);

                $estados = [
                    '', 'Aguascalientes', 'Baja California', 'Baja California Sur', 'Campeche', 'Chiapas',
                    'Chihuahua', 'Coahuila de Zaragoza', 'Colima', 'Ciudad de México', 'Durango', 'Guanajuato', 'Guerrero', 'Hidalgo', 'Jalisco',
                    'Estado de Mexico', 'Michoacan de Ocampo', 'Morelos', 'Nayarit', 'Nuevo Leon', 'Oaxaca', 'Puebla', 'Queretaro de Arteaga', 'Quintana Roo',
                    'San Luis Potosi', 'Sinaloa', 'Sonora', 'Tabasco', 'Tamaulipas', 'Tlaxcala', 'Veracruz de Ignacio de la Llave', 'Yucatan', 'Zacatecas'
                ];
                $num_estados = count($estados);
                $num = $num_estados - 1;
                for ($i = 1; $i <= $num; $i++) {
                    $e = $i + 1;
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A' . $e, $i);
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B' . $e, $estados[$i]);
                }
                foreach (range('A', 'B') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
            if ($work_sheet == 5) {
                $objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
                $objWorkSheet->setTitle("OBSERVACIONES");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'ID OBSERVACIONES');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B1', 'DESCRIPCIÓN');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C1', 'DESCRIPCION_CORTA');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A1:C1')->applyFromArray($estiloTituloColumnas);

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A2', '70');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A3', '71');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A4', '72');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A5', '73');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A6', '74');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A7', '75');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A8', '76');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A9', '77');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A10', '78');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A11', '100');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A12', '101');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A13', '102');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A14', '103');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A15', '104');

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B2', 'EQUIVALENCIA DE ESTUDIOS');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B3', 'EXAMEN EXTRAORDINARIO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B4', 'EXAMEN A TITULO DE SUFICIENCIA');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B5', 'CURSO DE VERANO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B6', 'RECURSAMIENTO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B7', 'REINGRESO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B8', 'ACUERDO REGULARIZACIÓN');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B9', 'CON CAMBIO EN EL ACUERDO DE RVOE');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B10', 'REVALIDACIÓN DE ESTUDIOS');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B11', 'NORMAL / ORDINARIO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B12', 'CORRESPONDENCIA DE ASIGNATURA POR PLAN');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B13', 'EXENTO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B14', 'ACREDITADO O APROBADO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B15', 'CURSO DE REGULARIZACIÓN');

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C2', 'E.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C3', 'E.E.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C4', 'E.T.S.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C5', 'C.V.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C6', 'Rec.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C7', 'Rein.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C8', 'A.C.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C9', 'C.A.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C10', 'R.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C11', '');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C12', 'C.A.P.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C13', 'E.X.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C14', 'A.');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('C15', 'C.R.');

                foreach (range('A', 'C') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }
            }

            if ($work_sheet == 6) {
                $objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
                $objWorkSheet->setTitle("INSTITUCIONES");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'ID_INSTITUCION');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B1', 'DESCRIPCION');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A1:B1')->applyFromArray($estiloTituloColumnas);

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A2', '20034');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B2', 'CENTRO DE CULTURA CASA LAMM');

                foreach (range('A', 'B') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }
            }

            if ($work_sheet == 7) {
                $objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
                $objWorkSheet->setTitle("NIVEL DE ESTUDIOS");

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'ID NIVEL');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B1', 'DESCRIPCION');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A1:B1')->applyFromArray($estiloTituloColumnas);

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A2', '95');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A3', '85');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A4', '84');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A5', '83');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A6', '82');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A7', '81');

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B2', 'DOCTORADO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B3', 'ESPECIALIDAD');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B4', 'TÉCNICO SUPERIOR UNIVERSITARIO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B5', 'PROFESIONAL ASOCIADO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B6', 'MAESTRÍA');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B7', 'LICENCIATURA');

                foreach (range('A', 'B') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }
            }

            if ($work_sheet == 8) {
                $objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
                $objWorkSheet->setTitle("CARGOS");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'ID CARGO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B1', 'DESCRIPCION');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A1:B1')->applyFromArray($estiloTituloColumnas);

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A2', '1');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A3', '2');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A4', '3');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A5', '4');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A6', '5');

                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B2', 'DIRECTOR');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B3', 'SUBDIRECTOR');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B4', 'RECTOR');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B5', 'VICERRECTOR');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B6', 'RESPONSABLE DE EXPEDICIÓN');

                foreach (range('A', 'B') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
            if ($work_sheet == 9) {
                $objWorkSheet = $objPHPExcel->createSheet($work_sheet_count);
                $objWorkSheet->setTitle("CARGOS");
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A1', 'CATALOGO TIPO PERIODO');
                $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($estiloTituloReporte);


                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A2', 'ID_TIPOPERIODO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B2', 'TIPO_PERIODO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('A2:B2')->applyFromArray($estiloTituloColumnas);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A3', '91');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A4', '92');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A5', '93');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A6', '94');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A7', '260');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A8', '261');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('A9', '262');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B3', 'SEMESTRE');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B4', 'BIMESTRE');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B5', 'CUATRIMESTRE');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B6', 'TETRAMESTRE');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B7', 'TRIMESTRE');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B8', 'MODULAR');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('B9', 'ANUAL');
                foreach (range('A', 'B') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }


                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('E1', 'CATALOGO TIPO PERIODO');
                $objPHPExcel->getActiveSheet()->getStyle('E1')->applyFromArray($estiloTituloReporte);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('E2', 'ID_GENERO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('F2', 'GENERO');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('E2:F2')->applyFromArray($estiloTituloColumnas);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('E3', '250');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('E4', '251');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('F3', 'MUJER');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('F4', 'HOMBRE');
                foreach (range('E', 'F') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }



                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('H1', 'OTROS CATOLOGOS');
                $objPHPExcel->getActiveSheet()->getStyle('H1')->applyFromArray($estiloTituloReporte);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('H2', 'ID_TIPOCERTIFICACION');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('I2', 'TIPO_CERTIFICACION');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->getStyle('H2:I2')->applyFromArray($estiloTituloColumnas);
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('H3', '79');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('H4', '80');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('I3', 'TOTAL');
                $objPHPExcel->setActiveSheetIndex($work_sheet)->setCellValue('I4', 'PARCIAL');
                foreach (range('H', 'I') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }


                foreach (range('A', 'B') as $columnID) {
                    $objPHPExcel->setActiveSheetIndex($work_sheet)->getColumnDimension($columnID)->setAutoSize(true);
                }
            }
            $work_sheet++;
        }




        if ($GLOBALS['version'] == 5) {
            // include '../../extras/excel/crear5.php';  
            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        } else {
            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel7');
            // include '../../extras/excel/crear7.php';  

        }



        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="reporteEgresados.xls"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    } else {
        $json = array("status" => 0, "msg" => "Método no aceptado");
    }

    /* Output header */

    // echo json_encode($json);

} catch (Exception $e) {
    echo  $e->getMessage();
}
