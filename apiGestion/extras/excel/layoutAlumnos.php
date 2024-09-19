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

                if($nombre == null || $primer_apellido == null || $segundo_apellido == null || $email == null ||  $clave_alumno == null){
                    $json = array("estatus "=>0, "msg"=> "Error en la Fila ".$i.", Nombre no puede estar vacio");
                    break;
                }

                $inserta_usuario_A =$usuario::crea_usuario($nombre, $primer_apellido, $segundo_apellido, $curp, $email, $celular, $rfc, $fecha_php,$clave_alumno, $clave_alumno,"",2);
                $inserta_alumno =inserta($GLOBALS['db_controlEscolar'].'.tr_alumno','alumno_id, situacion_alumno_id, clave_alumno, estatus',
                ''.$inserta_usuario_A['id_persona'].',1,"'.$clave_alumno.'",1');

                
            }
            
            $json = array("msg" => "Se insertaron Alumnos Correctamente.");
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

