<?php
include '../../jwt.php';
include '../../headers.php';
include '../../controlEscolar/class/alumno.class.php';
include '../../controlEscolar/class/curp.class.php';
require_once '../../vendor/phpoffice/phpexcel/Classes/PHPExcel.php';

try {

    db('controlEscolar');
    $alumno = new Alumno();
    $curpInfo = new Curp();

    $nivel_estudios_id = null;
    $carrera_id = null;
    $tipo_plan_estudio_id = null;
    $plan_estudio_id = null;
    $orden_jerarquico_id = null;
    $grupo_id = null;
    $campus_id = null;
    $situacion_alumno_id = null;
    $situacion_pago_id = null;
    $consulta_personas = null;
    
    function getDomicilioCompleto($array)
    {
        $domicilio_completo =null;
        if (isset($array['Calle'])) {
            $domicilio_completo = 'Calle ' . $array['Calle'];
        }

        if (isset($array['Número exterior'])) {
            $domicilio_completo = $domicilio_completo . ' Núm. Ext. ' . $array['Número exterior'];
        }

        if (isset($array['Número interior'])) {
            $domicilio_completo = $domicilio_completo . ' Núm int. ' . $array['Número interior'];
        }

        if (isset($array['Código postal'])) {
            $domicilio_completo = $domicilio_completo . ' C.P.' . $array['Código postal'];
        }

        if (isset($array['Colonia'])) {
            $domicilio_completo = $domicilio_completo . ' Col.' . $array['Colonia'];
        }

        if (isset($array['Alcaldía / municipio'])) {
            $domicilio_completo = $domicilio_completo . ' ' . $array['Alcaldía / municipio'];
        }

        if (isset($array['Estado'])) {
            $domicilio_completo = $domicilio_completo . ' ' . $array['Estado'];
        }
        return $domicilio_completo;

    }
    function num2alpha($n)
    {
        for ($r = ""; $n >= 0; $n = intval($n / 26) - 1) {
            $r = chr($n % 26 + 0x41) . $r;
        }

        return $r;
    }

    function getInformacionFormularios($id)
    {
        $query_formulario = query('SELECT tcf.pregunta, ica.respuesta FROM ' . $GLOBALS["db_seguimiento"] . '.tr_campo_formulario tcf
            LEFT JOIN ' . $GLOBALS["db_seguimiento"] . '.inter_campo_aspirante ica ON ica.campo_formulario_id = tcf.campo_formulario_id
            WHERE ica.aspirante_id = ' . $id . ' AND tcf.estatus = 1;');

        $new_array = array();
        while ($arreglo = arreglo($query_formulario)) {
            $new_array[$arreglo['pregunta']] = $arreglo['respuesta'];
        }
        $new_array['domicilio_completo'] = getDomicilioCompleto($new_array);
        return $new_array;
    }

    function getInformacionTutor($alumno_id)
    {
        $query_tutor = query('SELECT p.nombre AS t_nombre, p.primer_apellido AS t_primer_apellido, p.segundo_apellido AS t_segundo_apellido, p.email AS t_email, p.celular AS t_celular, p.correo_institucional AS t_correo_institucional
        FROM ' . $GLOBALS["db_datosGenerales"] . '.personas p
                            JOIN ' . $GLOBALS["db_seguimiento"] . '.inter_tutor_persona itp ON itp.tutor_id = p.persona_id
                            WHERE itp.estatus = 1 AND p.estatus = 1 AND itp.persona_id = ' . $alumno_id . '');
        $arreglo = arreglo($query_tutor);
        $arreglo['nombre_tutor'] = $arreglo['t_nombre'] . ' ' . $arreglo['t_primer_apellido'] . ' ' . $arreglo['t_segundo_apellido'] . ' ';
        return $arreglo;
    }

    if ($_SERVER['REQUEST_METHOD'] == "GET") {
        foreach ($_GET as $clave => $valor) {
            ${$clave} = escape_cara($valor);
        }

        // $usuario = Auth::GetData(
        //     $jwt
        // );
        $alumnos = $alumno::busquedaAlumnosPorFiltro($nivel_estudios_id, $carrera_id, $tipo_plan_estudio_id, $plan_estudio_id, $orden_jerarquico_id, $grupo_id, $campus_id, $situacion_alumno_id, $situacion_pago_id, $consulta_personas);

        $json_alumnos = array();
        while ($arreglo = arreglo($alumnos)) {
            $arreglo['edad'] = $curpInfo::getEdadFromCurp($arreglo['curp']);
            $arreglo['sx'] = $curpInfo::getSexo($arreglo['curp']);
            $arreglo['fecha_de_nacimiento'] = $curpInfo::getFechaNacFromCurp($arreglo['curp']);
            $arreglo['nombre_completo'] = $arreglo['primer_apellido'].' '.$arreglo['segundo_apellido'].' '.$arreglo['nombre'];
            $arregloConFormularios = array_merge($arreglo, getInformacionFormularios($arreglo['alumno_id']));
            $arregloCompleto = array_merge($arregloConFormularios, getInformacionTutor($arreglo['alumno_id']));
            array_push($json_alumnos, $arregloCompleto);
        }

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getProperties()

            ->setCreator("Temporaris")
            ->setLastModifiedBy("Temporaris")
            ->setTitle("Template Relevé des heures intérimaires")
            ->setSubject("Template excel")
            ->setDescription("Template excel permettant la création d'un ou plusieurs relevés d'heures")
            ->setKeywords("Template excel");

        $estiloTituloColumnas = array(
            'font' => array(
                'name' => 'Arial',
                'bold' => true,
                'size' => 12,
                'color' => array(
                    'rgb' => 'FFFFFF',
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '548DD5'),
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );
        $alumnos = $alumno::busquedaAlumnosPorFiltro($nivel_estudios_id, $carrera_id, $tipo_plan_estudio_id, $plan_estudio_id, $orden_jerarquico_id, $grupo_id, $campus_id, $situacion_alumno_id, $situacion_pago_id, $consulta_personas);

        $estiloInformacion = array(
            'font' => array(
                'name' => 'Arial',
                'size' => 12,
                'color' => array(
                    'rgb' => '000000',
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
        );

        $objPHPExcel->getActiveSheet()->getStyle('A4:AJ4')->applyFromArray($estiloTituloColumnas);
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setTitle('ReporteFiltros');

        $objPHPExcel->getActiveSheet()
            ->setCellValue('A4', 'List')
            ->setCellValue('B4', 'Generación')
            ->setCellValue('C4', 'Ciclo')
            ->setCellValue('D4', 'Plan E.')
            ->setCellValue('E4', 'Matrícula')
            ->setCellValue('F4', 'Nombre')
            ->setCellValue('G4', 'Email')
            ->setCellValue('H4', 'Email 2')
            ->setCellValue('I4', 'Celular')
            ->setCellValue('J4', 'Edad')
            ->setCellValue('K4', 'Curp')
            ->setCellValue('L4', 'Sexo')
            ->setCellValue('M4', 'Edo. Nacimiento')
            ->setCellValue('N4', 'Mun. Nacimiento')
            ->setCellValue('O4', 'Fecha Nacimiento')
            ->setCellValue('P4', 'Domicilio')
            ->setCellValue('Q4', 'Escuela Procedencia')
            ->setCellValue('R4', 'Edo. Escuela')
            ->setCellValue('S4', 'Mun. Escuela')
            ->setCellValue('T4', 'Beca')
            ->setCellValue('U4', 'Tipo Beca')
            ->setCellValue('V4', 'Discapacidad')
            ->setCellValue('W4', 'Nació Extranjero')
            ->setCellValue('X4', 'Edo. Extranjero')
            ->setCellValue('Y4', 'Estudió Extranjero')
            ->setCellValue('Z4', 'Estatus')
            ->setCellValue('AA4', 'Tutor')
            ->setCellValue('AB4', 'Email tutor')
            ->setCellValue('AC4', 'Domicilio tutor')
            ->setCellValue('AD4', 'Celular tutor')
            ->setCellValue('AE4', 'Teléfono tutor')
            ->setCellValue('AF4', 'Parentesco tutor')
            ->setCellValue('AG4', 'Equivalencia')
            ->setCellValue('AH4', 'Reingreso')
            ->setCellValue('AI4', 'Fantasma')
            ->setCellValue('AJ4', 'Posterior');

        $fila = 5;
        $num_alumnos = sizeof($json_alumnos);

        for ($i = 0; $i < $num_alumnos; $i++) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $fila, $i);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $fila, $json_alumnos[$i]['plan_estudio']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $fila, $json_alumnos[$i]['clave_alumno']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $fila, $json_alumnos[$i]['nombre_completo']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $fila, $json_alumnos[$i]['email']);
            if (isset($fila, $json_alumnos[$i]['Email instituto'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('H' . $fila, $json_alumnos[$i]['Email instituto']);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $fila, $json_alumnos[$i]['celular']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $fila, $json_alumnos[$i]['edad']);
            if (isset($json_alumnos[$i]['curp']) && $json_alumnos[$i]['curp'] != '' && strlen($json_alumnos[$i]['curp']) === 18) {
                $objPHPExcel->getActiveSheet()->setCellValue('K' . $fila, $json_alumnos[$i]['curp']);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('L' . $fila, $json_alumnos[$i]['sx']);
            if (isset($fila, $json_alumnos[$i]['Estado de nacimiento'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('M' . $fila, $json_alumnos[$i]['Estado de nacimiento']);
            }
            if (isset($fila, $json_alumnos[$i]['Municipio de nacimiento'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('N' . $fila, $json_alumnos[$i]['Municipio de nacimiento']);
            }
            if (isset($fila, $json_alumnos[$i]['fecha_de_nacimiento'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('O' . $fila, $json_alumnos[$i]['fecha_de_nacimiento']);
            }
            if (isset($fila, $json_alumnos[$i]['domicilio_completo'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('P' . $fila, $json_alumnos[$i]['domicilio_completo']);
            }
            if (isset($fila, $json_alumnos[$i]['Nombre de la institución educativa de procedencia'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('Q' . $fila, $json_alumnos[$i]['Nombre de la institución educativa de procedencia']);
            }
            if (isset($fila, $json_alumnos[$i]['Estado de la institución educativa de procedencia'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('R' . $fila, $json_alumnos[$i]['Estado de la institución educativa de procedencia']);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('S' . $fila, '');
            if (isset($fila, $json_alumnos[$i]['Beca'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('T' . $fila, $json_alumnos[$i]['Beca']);
            }
            if (isset($fila, $json_alumnos[$i]['Tipo Beca'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('U' . $fila, $json_alumnos[$i]['Tipo Beca']);
            }
            if (isset($fila, $json_alumnos[$i]['Tipo de discapacidad'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('V' . $fila, $json_alumnos[$i]['Tipo de discapacidad']);
            }
            if (isset($fila, $json_alumnos[$i]['Nacio en el extranjero'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('W' . $fila, $json_alumnos[$i]['Nacio en el extranjero']);
            }
            if (isset($fila, $json_alumnos[$i]['Estado extranjero'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('X' . $fila, $json_alumnos[$i]['Estado extranjero']);
            }
            if (isset($fila, $json_alumnos[$i]['Estudio en el extranjero'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('Y' . $fila, $json_alumnos[$i]['Estudio en el extranjero']);
            }
            if (isset($fila, $json_alumnos[$i]['situacion_alumno_descripcion'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('Z' . $fila, $json_alumnos[$i]['situacion_alumno_descripcion']);
            }
            if (isset($fila, $json_alumnos[$i]['nombre_tutor'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('AA' . $fila, $json_alumnos[$i]['nombre_tutor']);
            }
            if (isset($fila, $json_alumnos[$i]['t_email'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('AB' . $fila, $json_alumnos[$i]['t_email']);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('AC' . $fila, '');
            if (isset($fila, $json_alumnos[$i]['t_celular'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('AD' . $fila, $json_alumnos[$i]['t_celular']);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('AE' . $fila, '');
            $objPHPExcel->getActiveSheet()->setCellValue('AF' . $fila, '');
            if (isset($fila, $json_alumnos[$i]['Equivalencia'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('AG' . $fila, $json_alumnos[$i]['Equivalencia']);
            }
            if (isset($fila, $json_alumnos[$i]['Reingreso'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('AH' . $fila, $json_alumnos[$i]['Reingreso']);
            }
            if (isset($fila, $json_alumnos[$i]['Validación fantasma'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('AI' . $fila, $json_alumnos[$i]['Validación fantasma']);
            }
            if (isset($fila, $json_alumnos[$i]['Validación posterior'])) {
                $objPHPExcel->getActiveSheet()->setCellValue('AJ' . $fila, $json_alumnos[$i]['Validación posterior']);
            }
            $objPHPExcel->getActiveSheet()->getStyle('A' . $fila . ':AJ' . $fila . '')->applyFromArray($estiloInformacion);
            $fila++;
        }

        foreach ($objPHPExcel->getActiveSheet()->getColumnIterator() as $column) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        if ($GLOBALS['version'] == 5) {
            // include '../../extras/excel/crear5.php';
            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        } else {
            $json = array("status" => 0, "msg" => "No se encontraron alumnos");
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Control_escolar.xls"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

    } else {
        $json = array("status" => 0, "msg" => "Método no aceptado");
    }

/* Output header */

    echo json_encode($json);

} catch (Exception $e) {
    $json = array("status" => 0, "msg" => $e->getMessage());

    echo json_encode($json);
}
