<?php
include "../../vendor/phpoffice/phpexcel/Classes/PHPExcel/IOFactory.php";
include '../../jwt.php';
include '../../headers.php';
include '../../general/class/usuario.class.php';


try {

    db('datosGenerales');
  
    $usuario = new Usuario();
    $contraseña_tmp = "";
  
    if($_SERVER['REQUEST_METHOD'] == "POST"){
      foreach($_POST as $clave => $valor){
        ${$clave} = escape_cara($valor);
      }
      
        /*$usuario = Auth::GetData(
              $jwt  
          );*/
            
        $elimina_docentes = query('delete from cat_asignaturas');
        $elimina_docentes = query('alter table cat_asignaturas AUTO_INCREMENT = 0');
        $elimina_docentes = query('delete from tr_plan_estudios');
        $elimina_docentes = query('alter table tr_plan_estudios AUTO_INCREMENT = 0');
        $elimina_docentes = query('delete from inter_carrera_campus');
        $elimina_docentes = query('alter table inter_carrera_campus AUTO_INCREMENT = 0');
        $elimina_docentes = query('delete from '.$GLOBALS['db_pagos'].'.cat_pago_inscripcion');
        $elimina_docentes = query('alter table '.$GLOBALS['db_pagos'].'.cat_pago_inscripcion AUTO_INCREMENT = 0');
        $elimina_docentes = query('delete from cat_campus');
        $elimina_docentes = query('alter table cat_campus AUTO_INCREMENT = 0');
        $elimina_docentes = query('delete from tr_carrera');
        $elimina_docentes = query('alter table tr_carrera AUTO_INCREMENT = 0');
        // $elimina_docentes = query('delete from cat_instituciones');
        // $elimina_docentes = query('alter table cat_instituciones AUTO_INCREMENT = 0');
        $elimina_docentes = query('delete from '.$GLOBALS['db_controlEscolar'].'.tr_alumno where alumno_id != 1');
        $elimina_docentes = query('alter table '.$GLOBALS['db_controlEscolar'].'.tr_alumno AUTO_INCREMENT = 0');
        $elimina_docentes = query('delete from '.$GLOBALS['db_controlEscolar'].'.tr_docente');
        $elimina_docentes = query('alter table '.$GLOBALS['db_controlEscolar'].'.tr_docente AUTO_INCREMENT = 0');
        $elimina_docentes = query('delete from inter_persona_usuario_rol where persona_id not in (0,1,2)');
        // $elimina_docentes = query('alter table inter_persona_usuario_rol AUTO_INCREMENT = 0');
        $elimina_docentes = query('delete from usuarios where usuario_id not in (0,1,2)');
        // $elimina_docentes = query('alter table usuarios AUTO_INCREMENT = 0');
        $elimina_docentes = query('delete from personas where not in (0,1,2)');
        // $elimina_docentes = query('alter table personas AUTO_INCREMENT = 0');

        
        


        

    $nombre_archivo = $_FILES['documento']['tmp_name'];
    $extension = strtoupper(explode(".", $_FILES['documento']['name'])[1]);

    if($extension == 'XLSX' || $extension == 'ODS'){

        $objPHPExcel = PHPExcel_IOFactory::load($nombre_archivo);



        $objPHPExcel->setActiveSheetIndex(0);

        $numRows=$objPHPExcel ->setActiveSheetIndex(0)->getHighestRow();

        $numColumnas=$objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();


        $sheetData = $objPHPExcel->getActiveSheet(); 
        $highestRow = $sheetData->getHighestRow(); 
        $highestColumn = $sheetData->getHighestColumn(); 
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); 
        
        if($numColumnas != null){
            for ($i=3; $i <= $numRows ; $i++) { 


                $nombre = $objPHPExcel->getActiveSheet()->getCell('A'.$i)->getCalculatedValue();
                $primer_apellido = $objPHPExcel->getActiveSheet()->getCell('B'.$i)->getCalculatedValue();
                $segundo_apellido = $objPHPExcel->getActiveSheet()->getCell('C'.$i)->getCalculatedValue();
                $curp= $objPHPExcel->getActiveSheet()->getCell('D'.$i)->getCalculatedValue();
                $rfc = $objPHPExcel->getActiveSheet()->getCell('E'.$i)->getCalculatedValue();
                
                $fecha_nacimiento = $objPHPExcel->getActiveSheet()->getCell('F'.$i)->getCalculatedValue();
                $timestamp = PHPExcel_Shared_Date::ExcelToPHP($fecha_nacimiento);
                $fecha_php = date("Y-m-d",$timestamp);
        
                $email = $objPHPExcel->getActiveSheet()->getCell('G'.$i)->getCalculatedValue();
                $celular = $objPHPExcel->getActiveSheet()->getCell('H'.$i)->getCalculatedValue();
                $clave_alumno = $objPHPExcel->getActiveSheet()->getCell('I'.$i)->getCalculatedValue();
                $carrera= $objPHPExcel->getActiveSheet()->getCell('J'.$i)->getCalculatedValue();
                $grado = $objPHPExcel->getActiveSheet()->getCell('K'.$i)->getCalculatedValue();
                $id_plan_estudios = $objPHPExcel->getActiveSheet()->getCell('L'.$i)->getCalculatedValue();

                // if($nombre == null || $primer_apellido == null || $segundo_apellido == null || $curp == null ||  $clave_alumno == null){
                //     $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$i.", Nombre no puede estar vacio");
                //     break;
                // }

                // $options = array(
                //     'options' => array(
                //                     'min_range' => 1,
                //                     'max_range' => 10,
                //                     )
                // );
                
                // if($grado == null){
                //     $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$i.",Grado no puede estar vacio");
                //     break;
                // }elseif (filter_var($grado, FILTER_VALIDATE_INT, $options) === FALSE) {
                //     $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$i.", Grado maximo a cursar 10");
                //     break;   
                // }

                $inserta_usuario_A =$usuario::crea_usuario($nombre, $primer_apellido, $segundo_apellido, $curp, $email, $celular, $rfc, $fecha_php,$clave_alumno, $clave_alumno,"",2);
                $inserta_alumno =inserta($GLOBALS['db_controlEscolar'].'.tr_alumno','alumno_id, situacion_alumno_id, clave_alumno, estatus',
                ''.$inserta_usuario_A['id_persona'].',1,"'.$clave_alumno.'",1');

                
            }
            

        }

    // Pagina 1 del Excel (Maestros).-----------------------------------------------------------------------

        $objPHPExcel->setActiveSheetIndex(1);

        $numRowsD=$objPHPExcel ->setActiveSheetIndex(1)->getHighestRow();

        $numColumnasD=$objPHPExcel->setActiveSheetIndex(1)->getHighestColumn();

        $sheetData = $objPHPExcel->getActiveSheet(); 
        $highestRow = $sheetData->getHighestRow(); 
        $highestColumn = $sheetData->getHighestColumn(); 
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); 
        

    if($numColumnasD != null){
        for ($j=3; $j <= $numRowsD ; $j++) { 

            
            $nombreD = $objPHPExcel->getActiveSheet()->getCell('A'.$j)->getCalculatedValue();
            $primer_apellidoD = $objPHPExcel->getActiveSheet()->getCell('B'.$j)->getCalculatedValue();
            $segundo_apellidoD = $objPHPExcel->getActiveSheet()->getCell('C'.$j)->getCalculatedValue();
            $curpD= $objPHPExcel->getActiveSheet()->getCell('D'.$j)->getCalculatedValue();
            $rfcD = $objPHPExcel->getActiveSheet()->getCell('E'.$j)->getCalculatedValue();
            
            $fecha_nacimientoD = $objPHPExcel->getActiveSheet()->getCell('F'.$j)->getCalculatedValue();
            $timestampD = PHPExcel_Shared_Date::ExcelToPHP($fecha_nacimientoD);
            $fecha_phpD = date("Y-m-d",$timestampD);

            $emailD = $objPHPExcel->getActiveSheet()->getCell('G'.$j)->getCalculatedValue();
            $celularD = $objPHPExcel->getActiveSheet()->getCell('H'.$j)->getCalculatedValue();
            $clave_docente = $objPHPExcel->getActiveSheet()->getCell('I'.$j)->getCalculatedValue();
            
            // if($nombreD == null || $primer_apellidoD == null || $segundo_apellidoD == null || $curpD == null || $clave_docente == null){
            //     $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$j." Pagina 2, Celda no puede estar vacia");
            //     break;
            // }


            
            $inserta_usuario_D = $usuario::crea_usuario($nombreD, $primer_apellidoD, $segundo_apellidoD,$curpD, $emailD, $celularD, $rfcD, $fecha_phpD,$clave_docente, $clave_docente, "",3);
            $inserta_docente =inserta($GLOBALS['db_controlEscolar'].'.tr_docente','docente_id, situacion_docente_id, clave_profesor, estatus',
            ''.$inserta_usuario_D['id_persona'].',1,"'.$clave_docente.'",1');

        }
        

    }




    // Pagina 2 del Excel (Institutos).-----------------------------------------------------------------------

            
    // $objPHPExcel->setActiveSheetIndex(2);
    // $numRowsD=$objPHPExcel ->setActiveSheetIndex(2)->getHighestRow();
    // $numColumnasD=$objPHPExcel->setActiveSheetIndex(2)->getHighestColumn();

    // if($numColumnasD != null){
    // for ($k=3; $k <= $numRowsD ; $k++) { 

    //     $instituto_id = $objPHPExcel->getActiveSheet()->getCell('A'.$k)->getCalculatedValue();
    //     $institucion_clave = $objPHPExcel->getActiveSheet()->getCell('B'.$k)->getCalculatedValue();
    //     $institucion = $objPHPExcel->getActiveSheet()->getCell('C'.$k)->getCalculatedValue();
    
    
    //     if($instituto_id == null || $institucion_clave == null || $institucion == null ){
    //         $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$k." Pagina 3, Celda no puede estar vacia");
    //         break;
    //     }

    //         $inserta_institutos = inserta('cat_instituciones','institucion_clave, institucion, tipo_institucion_id,estatus',
    //         '"'.$institucion_clave.'", "'.$institucion.'", 1,1');     
        

    // }


    // }


        // Pagina 3 del Excel (Campus).-----------------------------------------------------------------------

        $numRowsC=$objPHPExcel ->setActiveSheetIndex(2)->getHighestRow();
        $numColumnasC=$objPHPExcel->setActiveSheetIndex(2)->getHighestColumn();
    
        $docente=0;
        if($numColumnasC!= null){
        for ($l=3; $l <= $numRowsC ; $l++) { 
    
            
            // $campus_id= $objPHPExcel->getActiveSheet()->getCell('A'.$l)->getCalculatedValue();
            $campus_clave = $objPHPExcel->getActiveSheet()->getCell('B'.$l)->getCalculatedValue();
            $campus = $objPHPExcel->getActiveSheet()->getCell('C'.$l)->getCalculatedValue();
    
            // if($campus_clave == null || $campus == null){
            //     $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$l." Pagina 4, Celda no puede estar vacia");
            //     break;
            // }
    
            $inserta_institutos = inserta('cat_campus','institucion_id, campus_clave,online, tipo_campus_id, campus, estatus',
            '1,"'.$campus_clave.'","'.$campus.'",'.$online.','.$id_tipo_campus.',1');     
    
        }
    
    
        }
    

    // Pagina 4 del Excel (Carreras).-----------------------------------------------------------------------

        
    $numRowsC=$objPHPExcel ->setActiveSheetIndex(3)->getHighestRow();
    $numColumnasC=$objPHPExcel->setActiveSheetIndex(3)->getHighestColumn();

    $docente=0;
    if($numColumnasC!= null){
    for ($m=3; $m <= $numRowsC ; $m++) { 

        
        $carrera_id = $objPHPExcel->getActiveSheet()->getCell('A'.$m)->getCalculatedValue();
        $nivel_estudios = $objPHPExcel->getActiveSheet()->getCell('B'.$m)->getCalculatedValue();
        $clave_carrea = $objPHPExcel->getActiveSheet()->getCell('C'.$m)->getCalculatedValue();
        $carrera = $objPHPExcel->getActiveSheet()->getCell('D'.$m)->getCalculatedValue();

        // if($nivel_estudios == null || $clave_carrea == null || $carrera == null  ){
        //     $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$m." Pagina 5, Celda no puede estar vacia");
        //     break;
        // }

        $inserta_institutos = inserta('tr_carrera','institucion_id, nivel_estudios_id, carrera_clave, carrera, estatus',
        '1,'.$nivel_estudios.',"'.$clave_carrea.'","'.$carrera.'",1');     

    }


    }
    
    // Pagina 5 del Excel (inter_carrera_campus).-----------------------------------------------------------------------

    $numRowsC=$objPHPExcel ->setActiveSheetIndex(4)->getHighestRow();
    $numColumnasC=$objPHPExcel->setActiveSheetIndex(4)->getHighestColumn();

    $docente=0;
    if($numColumnasC!= null){
    for ($m=3; $m <= $numRowsC ; $m++) { 

        
        $carrera_id = $objPHPExcel->getActiveSheet()->getCell('A'.$m)->getCalculatedValue();
        $campus_id = $objPHPExcel->getActiveSheet()->getCell('B'.$m)->getCalculatedValue();

        // if($carrera_id == null || $campus_id == null){
        //     $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$m." Pagina 6, Celda no puede estar vacia");
        //     break;
        // }

        $inserta_institutos = inserta('inter_carrera_campus','carrera_id, campus_id, estatus',
        ''.$carrera_id.','.$campus_id.',1');     

    }


    }


    // Pagina 6 del Excel (Plan).-----------------------------------------------------------------------

            

    $numRowsP=$objPHPExcel ->setActiveSheetIndex(5)->getHighestRow();
    $numColumnasP=$objPHPExcel->setActiveSheetIndex(5)->getHighestColumn();

    $docente=0;
    if($numColumnasP != null){
    for ($n=3; $n <= $numRowsP ; $n++) { 

        
        $plan_id = $objPHPExcel->getActiveSheet()->getCell('A'.$n)->getCalculatedValue();
        $carrera_plan_id = $objPHPExcel->getActiveSheet()->getCell('B'.$n)->getCalculatedValue();
        $plan_clave= $objPHPExcel->getActiveSheet()->getCell('C'.$n)->getCalculatedValue();
        $plan_estudios = $objPHPExcel->getActiveSheet()->getCell('D'.$n)->getCalculatedValue();
        $n_revoe = $objPHPExcel->getActiveSheet()->getCell('E'.$n)->getCalculatedValue();
        $cal_min = $objPHPExcel->getActiveSheet()->getCell('F'.$n)->getCalculatedValue();
        $total_creditos = $objPHPExcel->getActiveSheet()->getCell('G'.$n)->getCalculatedValue();
        $tipo_periodo = $objPHPExcel->getActiveSheet()->getCell('H'.$n)->getCalculatedValue();
        $tipo_plan_estudio = $objPHPExcel->getActiveSheet()->getCell('I'.$n)->getCalculatedValue();
        $total_periodos = $objPHPExcel->getActiveSheet()->getCell('J'.$n)->getCalculatedValue();


        // if($plan_id == null || $plan_clave == null || $plan_estudios == null || $n_revoe == null || $cal_min == null || $total_creditos == null || $tipo_periodo == null || $tipo_plan_estudio == null || $total_periodos == null){
        //     $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$n." Pagina 7, Celda no puede estar vacia");
        //     break;
        // }

        $inserta_institutos = inserta('tr_plan_estudios','carrera_id, plan_estudio_clave, plan_estudio, rvoe, minima_aprobatoria, total_creditos, tipo_periodo_id, tipo_plan_estudio_id, total_periodos, estatus',
        ''.$carrera_plan_id.',"'.$plan_clave.'","'.$plan_estudios.'","'.$n_revoe.'", '.$cal_min.', '.$total_creditos.','.$tipo_periodo.', '.$tipo_plan_estudio.', '.$total_periodos.',1');     

    }


    }


    // Pagina 7 del Excel (Asignaturas).-----------------------------------------------------------------------


    $numRowsA=$objPHPExcel ->setActiveSheetIndex(6)->getHighestRow();
    $numColumnasA=$objPHPExcel->setActiveSheetIndex(6)->getHighestColumn();

    $docente=0;
    if($numColumnasA != null){
    for ($o=3; $o <= $numRowsA ; $o++) { 

        
    
        $asignatura_id= $objPHPExcel->getActiveSheet()->getCell('A'.$o)->getCalculatedValue();
        $asignatura_clave = $objPHPExcel->getActiveSheet()->getCell('B'.$o)->getCalculatedValue();
        $asignatura = $objPHPExcel->getActiveSheet()->getCell('C'.$o)->getCalculatedValue();
        $tipo_asignatura = $objPHPExcel->getActiveSheet()->getCell('D'.$o)->getCalculatedValue();
        $cal_min = $objPHPExcel->getActiveSheet()->getCell('F'.$o)->getCalculatedValue();
        $cal_max = $objPHPExcel->getActiveSheet()->getCell('G'.$o)->getCalculatedValue();
        $creditos = $objPHPExcel->getActiveSheet()->getCell('H'.$o)->getCalculatedValue();

        // if($asignatura_id == null || $asignatura_clave == null || $asignatura == null || $tipo_asignatura == null || $cal_min == null || $cal_max == null || $creditos == null){
        //     $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$o." Pagina 8, Celda no puede estar vacia");
        //     break;
        // }

        $inserta_institutos = inserta('cat_asignaturas','asignatura_clave, asignatura, tipo_asignatura_id, calif_min,calif_max , creditos, estatus',
        '"'.$asignatura_clave.'","'.$asignatura.'",'.$tipo_asignatura.','.$cal_min.', '.$cal_max.', '.$creditos.',1');     


        $json = array("msg" => "Se insertaron Alumnos, Docentes, Instituciones, Carreras, Planes de Estudios y Asignaturas Correctamente.");

    }


    }

    }else{
        $json = "Solo acepta documentos Excel";
    }



}else{
    $json = array("status" => 0, "msg" => "Método no aceptado");
}

/* Output header */

echo json_encode($json);

} catch (Exception $e) {
  $json = array("status" => 0, "msg" =>  $e->getMessage());

  echo json_encode($json);
}

