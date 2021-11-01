<?php
session_start();
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$db=conectarDB();
//var_dump($_POST);

if ($_POST["nuevoCorreo"]=="1")
{
  modificarCorreoAlumno($db,$_POST["alumnoSel"],$_POST["correoTo"]);
}

$message = $_POST["alumnoSel"].",".$_POST["sel11"].",".$_POST["nSesiones"].",".$_POST["datepicker"];
$codigoEncriptado = openssl_encrypt ($message,"AES-128-ECB","kgYYBOihH8/(ggG/)gKGB8/biLJLDJOIUD/(%&/UG(DF(/F%&(IGDF%(F)HFG=FD:_V:F_VBLVP?F=F)FKIF)))");
$message = "Pulsa en el siguiente enlace para terminar el proceso de asistencia online\n
https://magicomagico.com/tutoz/PUBLIC656585916LOP661309945/gaok.php?cod=".urlencode($codigoEncriptado);
//echo $message;
$posGuion = strrpos($_POST["sel11"], "--");
$clase=substr($_POST["sel11"],0,$posGuion);
$subject="Asistencia online: ".$clase ;
$to=$_POST["correoTo"];
$okEnvio = enviarCorreo($to,$subject,$message);
$textoMostrar = "Error al mandar correo a ".$to." , pongase en contacto con el administrador, osea Gilbert";
if ($okEnvio)
{
  $textoMostrar = "Correo enviado correctamente a ".$to.", revisalo para pulsar el enlace sumistrado y terminar el proceso de asistencia.";
}


?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <title>Asis-tencia</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../UTILS/mi.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

  </head>

  <body>

      <span class="label label-primary"><?php echo $textoMostrar?></span>

  </body>

  </html>