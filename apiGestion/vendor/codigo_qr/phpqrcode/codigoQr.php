<?php
require_once '../../../config/db.php';

    $dir = '../../../../assets/QR/';

    include "qrlib.php";    
    
    if (!file_exists($dir))
        mkdir($dir);

    $filename = $dir.$_GET['id'].'.png';
    $tamanio = 10;
    $level = 'M';
    $frameSize = 3;
    $contenido = 'Prueba de Estudy';

    QRcode::png($_GET['id'], $filename);

    echo '<img src="'.$filename.'">'


    ?>
    