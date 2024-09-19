 <?php
 
include '../../jwt.php';
include '../../headers.php';

 include 'class/correo.class.php';


 $correo = new Correo();

 print_r($correo::test_body());