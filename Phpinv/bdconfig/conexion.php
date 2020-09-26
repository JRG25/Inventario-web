<?php
    $con=@mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if(!$con){
        die("Conexion Incorrecta: ".mysqli_error($con));
    }
    if (@mysqli_connect_errno()) {
        die("Fallo en Conexion: ".mysqli_connect_errno()." : ". mysqli_connect_error());
    }
?>
