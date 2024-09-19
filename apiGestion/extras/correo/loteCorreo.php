<?php
 
 include '../../jwt.php';
 include '../../headers.php';
 
  include 'class/lotescorreo.class.php';
 
 
  $loteCorreo = new LotesCorreo();

  $loteCorreo::inicializa_mail();

  $query = query('select * from personas where email in ("gral.vacio@gmail.com","ricardo.ruiz@estudy.com.mx")');


  while($arreglo = arreglo($query)){
      
      $loteCorreo::matriculacion_exitosa($arreglo['nombre'],$arreglo['email'],$arreglo['curp']);
  }
  
 
  