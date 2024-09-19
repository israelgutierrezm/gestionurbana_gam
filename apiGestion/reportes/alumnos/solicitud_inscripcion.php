<?php

include '../../vendor/FPDF/fpdf.php';
include '../../jwt.php';
include '../../headers.php';

db('seguimiento');


$id_alumno = $_GET['id'];

$query_curso = query('SELECT tc.carrera, tc.carrera_id from '.$GLOBALS['db_controlEscolar'].'.inter_alumno_plan iap
join '.$GLOBALS['db_datosGenerales'].'.inter_plan_orden ipo on ipo.plan_orden_id = iap.plan_orden_id
join '.$GLOBALS['db_datosGenerales'].'.tr_plan_estudios tpe on tpe.plan_estudio_id = ipo.plan_estudio_id
join '.$GLOBALS['db_datosGenerales'].'.tr_carrera tc on tpe.carrera_id = tc.carrera_id
where alumno_id ='.$id_alumno.' and iap.estatus = 1');

$arreglo_curso = arreglo($query_curso);




$query = query('SELECT a.alumno_id as clave_alumno, clave_alumno,nombre, primer_apellido, segundo_apellido, 
curp, rfc, fecha_nacimiento, p.email, sexo_id, otros_datos_id,color, p.fecha_nacimiento, p.celular
FROM '.$GLOBALS['db_controlEscolar'].'.tr_alumno a
join '.$GLOBALS['db_datosGenerales'].'.personas p on a.alumno_id = p.persona_id 
join '.$GLOBALS['db_datosGenerales'].'.inter_persona_usuario_rol ipur on ipur.persona_id = p.persona_id
join '.$GLOBALS['db_datosGenerales'].'.usuarios u on ipur.usuario_id = u.usuario_id
WHERE a.alumno_id = '.$id_alumno.' and ipur.rol_id = 2');
$formulario=array();
$arreglo_alumno = arreglo($query);
$calle='';
$num_ext='';
$num_int='';
$colonia='';
$codigo_postal='';
$Municipio='';
$estado='';
$telefono='';
$ultimo_nivel_estudios='';
$institucion_educativa='';
$donde_trabajas='';
$puesto='';
$distrito_local='';
$estadoa='';
$candidatoa='';
$distrito_federal='';
$municipioAlu='';
$cargo='';
$campo_campaña='';
$tipo_campaña='';
$delegacion='';

$query_formulario=query('SELECT tcf.campo_formulario_id,tcf.pregunta, respuesta 
from tr_campo_formulario tcf
right join inter_campo_aspirante ica on ica.campo_formulario_id = tcf.campo_formulario_id
    where ica.aspirante_id ='.$id_alumno);

while($arreglo=arreglo($query_formulario)){
    
    if($arreglo['campo_formulario_id']==7){
        $telefono=$arreglo['respuesta'];
    }
    
    if($arreglo['campo_formulario_id']==12){
        $calle=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==13){
        $num_ext=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==14){
        $num_int=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==15){
        $codigo_postal=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==16){
       $colonia=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==17){
        $delegacion=$arreglo['respuesta'];
    }
    
    if($arreglo['campo_formulario_id']==18){
        $estado=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==29){
        $ultimo_nivel_estudios=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==31){
        $institucion_educativa=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==36){
        $donde_trabajas=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==37){
        $puesto=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==44){
        $candidatoa=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==45){
        $distrito_federal=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==46){
        $distrito_local=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==47){
        $Municipio=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==48){
        $estadoa=$arreglo['respuesta'];
    }

    if($arreglo['campo_formulario_id']==49){
        $cargo=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==50){
        $campo_campaña=$arreglo['respuesta'];
    }
    if($arreglo['campo_formulario_id']==51){
        $tipo_campaña=$arreglo['respuesta'];
    }





    array_push($formulario, $arreglo);
}



$marco = 0;
$ancho = 6.5;


$pdf=new FPDF();
$pdf->AliasNbPages();
$pdf->AddPage();

if(!num($query)){
        $pdf->SetFont('arial','',18);
        $pdf->Ln(5);
        $pdf->Cell(100,5,utf8_decode('NO TENEMOS UN REGISTRO DE ESTE ALUMNO'));
}
else{
        //modificar fecha
        $pdf->SetFont('arial','B',12);
        $pdf->Ln(2.5);
        $pdf->Cell(20,5,'',$marco);
        $pdf->Cell(150,5,utf8_decode($arreglo_curso['carrera']),$marco,1,'C'); //texto centrado y salto de línea.
        $pdf->Ln(6);



        $pdf->Ln(2);
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(190,$ancho,utf8_decode('SOLICITUD PARA VALIDACIÓN DE INSCRIPCIÓN'),$marco,1,'C'); //texto centrado y salto de línea.
        $pdf->Ln(3);
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(15,$ancho,utf8_decode('Fecha:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->cell(65,$ancho,(date('d')-1)."/".date('m')."/".date('Y'),$marco) ;

        if($arreglo_curso['carrera_id'] == 3){
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(29,$ancho,utf8_decode('Candidato a:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(53,$ancho,utf8_decode(mb_strtoupper($candidatoa,'UTF-8')),$marco,1);
        
        $pdf->SetFont('arial','B',10);      
        $pdf->Cell(30,$ancho,utf8_decode('Distrito Federal:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(50,$ancho,utf8_decode($distrito_federal),$marco);   
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(30,$ancho,utf8_decode('Distrito Local:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(36,$ancho,utf8_decode($distrito_local),$marco,1);     
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(37,$ancho,utf8_decode('Municipio/Alcaldia:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(43,$ancho,utf8_decode(mb_strtoupper($Municipio,'UTF-8')),$marco);
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(16,$ancho,utf8_decode('Estado:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(30,$ancho,utf8_decode(mb_strtoupper($estadoa,'UTF-8')),$marco,1);
    }else{
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(35,$ancho,utf8_decode('Cargo:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(55,$ancho,utf8_decode(mb_strtoupper($cargo,'UTF-8')),$marco,1);
        
        $pdf->SetFont('arial','B',10);      
        $pdf->Cell(35,$ancho,utf8_decode('Campo de Campaña:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(55,$ancho,utf8_decode($campo_campaña),$marco,1);   
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(30,$ancho,utf8_decode('Tipo de campaña:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(36,$ancho,utf8_decode($tipo_campaña),$marco,1);     
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(37,$ancho,utf8_decode(''),$marco);
    }


       
        $pdf->Ln(2);
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(190,$ancho,utf8_decode('DATOS PERSONALES'),1,1); //texto centrado y salto de línea.
        $pdf->Ln(2.5);
        $pdf->Cell(40,$ancho,utf8_decode('Nombre completo:'),$marco);
        $pdf->SetFont('arial','',11);
        $pdf->Cell(150,$ancho,strtoupper(utf8_decode($arreglo_alumno['nombre'].' '.$arreglo_alumno['primer_apellido'].' '.$arreglo_alumno['segundo_apellido'])),$marco,1);


        $pdf->SetFont('arial','B',11);
        $pdf->Cell(42,$ancho,utf8_decode('Fecha de nacimiento:'),$marco);
        $pdf->SetFont('arial','',11);
        $pdf->Cell(50,$ancho,$arreglo_alumno['fecha_nacimiento'],$marco);
        $pdf->SetFont('arial','B',11);
        $pdf->Cell(15,$ancho,utf8_decode('CURP:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(85,$ancho,utf8_decode($arreglo_alumno['curp']),$marco,1);


        $pdf->SetFont('arial','B',11);
        $pdf->Cell(15,$ancho,utf8_decode('Calle:'),$marco);
        $pdf->SetFont('arial','',11);
        $pdf->Cell(150,$ancho,utf8_decode(mb_strtoupper($calle,'UTF-8')),$marco,1);

        $pdf->SetFont('arial','B',11);
        $pdf->Cell(25,$ancho,utf8_decode('Núm. int.:'),$marco);
        $pdf->SetFont('arial','',11);
        $pdf->Cell(65,$ancho,utf8_decode(mb_strtoupper($num_int,'UTF-8')),$marco);
        $pdf->SetFont('arial','B',11);
        $pdf->Cell(25,$ancho,utf8_decode('Núm. ext.:'),$marco);
        $pdf->SetFont('arial','',11);
        $pdf->Cell(70,$ancho,utf8_decode(mb_strtoupper($num_ext,'UTF-8')),$marco,1);

        $pdf->SetFont('arial','B',11);
        $pdf->Cell(18,$ancho,utf8_decode('Colonia:'),$marco);
        $pdf->SetFont('arial','',11);
        $pdf->Cell(72,$ancho,utf8_decode(mb_strtoupper($colonia,'UTF-8')),$marco,1);
       

        $pdf->SetFont('arial','B',11);
        $pdf->Cell(22,$ancho,utf8_decode('Municipio:'),$marco);
        $pdf->SetFont('arial','',11);
        $pdf->Cell(68,$ancho,utf8_decode(mb_strtoupper($delegacion,'UTF-8')),$marco,1);
        $pdf->SetFont('arial','B',11);
        $pdf->Cell(30,$ancho,utf8_decode('Codigo Postal:'),$marco);
        $pdf->SetFont('arial','',11);
        $pdf->Cell(60,$ancho,$codigo_postal,$marco);
        $pdf->SetFont('arial','B',11);
        $pdf->Cell(15,$ancho,utf8_decode('Estado:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(85,$ancho,utf8_decode(mb_strtoupper($estado,'UTF-8')),$marco,1);

        $pdf->SetFont('arial','B',11);
        $pdf->Cell(20,$ancho,utf8_decode('Teléfono:'),$marco);
        $pdf->SetFont('arial','',11);
        $pdf->Cell(70,$ancho,utf8_decode($telefono),$marco);
        $pdf->SetFont('arial','B',11);
        $pdf->Cell(15,$ancho,utf8_decode('Celular:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(85,$ancho,utf8_decode($arreglo_alumno['celular']),$marco,1);

        $pdf->SetFont('arial','B',11);
        $pdf->Cell(37.5,$ancho,utf8_decode('Correo electrónico:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(85,$ancho,utf8_decode($arreglo_alumno['email']),$marco,1);

      

        //lineas de division
        $pdf->Ln(2);
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(190,$ancho,utf8_decode('PREPARACIÓN PROFESIONAL'),1,1); //texto centrado y salto de línea.
        $pdf->Ln(1.5);
        $pdf->SetFont('arial','B',11);
        $pdf->Cell(50,$ancho,utf8_decode('último nivel de estudios:'),$marco);
        $pdf->SetFont('arial','',11);
        $pdf->Cell(100,$ancho,utf8_decode(mb_strtoupper($ultimo_nivel_estudios,'UTF-8')),$marco,1);

        $pdf->SetFont('arial','B',11);
        $pdf->Cell(50,$ancho,utf8_decode('Institución educativa:'),$marco);
        $pdf->SetFont('arial','',10);
        $pdf->Cell(100,$ancho,utf8_decode(mb_strtoupper($institucion_educativa,'UTF-8')),$marco,1);

        $pdf->Ln(2);
        $pdf->SetFont('arial','B',10);
        $pdf->Cell(190,$ancho,utf8_decode('INFORMACIÓN LABORAL'),1,1); //texto centrado y salto de línea.
        $pdf->Ln(2.5);
        $pdf->SetFont('arial','B',11);
        $pdf->Cell(50,$ancho,utf8_decode('¿Donde Trabajas?'),$marco,1);
        $pdf->SetFont('arial','',11);
        $pdf->MultiCell(190,6,utf8_decode(mb_strtoupper($donde_trabajas,'UTF-8')),$marco,1);

        $pdf->SetFont('arial','B',11);
        $pdf->Cell(50,$ancho,utf8_decode('¿Cuál es tu puesto?'),$marco,1);
        $pdf->SetFont('arial','',11);
        $pdf->MultiCell(190,10,utf8_decode(mb_strtoupper($puesto,'UTF-8')),$marco,1);

        $pdf->Ln(15);
        $pdf->SetFont('arial','B',10);
        $pdf->MultiCell(195,4,utf8_decode('AVISO'),1,'C');
        $pdf->MultiCell(195,4,utf8_decode('

Declaro que la información proporcionada es veridica y que será corroborada para asegurar la calidad del curso.
Así mismo, la aceptación al curso solicitado queda pendiente de validación de acuerdo a los requisitos de la Coordinación del Diplomado. En caso de no ser aceptado en el programa, se tomarán los términos y condiciones de Universidad .


'),1,1);
        $pdf->SetXY(10,270);
        $pdf->Cell(195,$ancho,utf8_decode('Nombre y firma del aspirante'),$marco,1,'C');
        $pdf->Ln(8);
        $pdf->line(82.5,269,132.5,269);


   }
        //$pdf->Cell(700,85,$pdf->Image('../'.$query_doc['ad2'].'',120,12,80),0,0,'C');
        //$pdf->Cell(700,85,$pdf->Image('../'.$query_doc['ad3'].'',120,12,80),0,0,'C');
        $modo="I";
        $nombre_archivo = 'Solcitud_inscripcion_'.$arreglo_alumno['nombre'].$arreglo_alumno['primer_apellido'].$arreglo_alumno['segundo_apellido'].'.pdf';
$pdf->Output($nombre_archivo,$modo);

?>
