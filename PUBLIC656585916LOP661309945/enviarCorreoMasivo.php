<?php
session_start();
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$db=conectarDB();
//var_dump($_POST);

$aAlumnosCurso = getAllAlumnos($db);
foreach ($aAlumnosCurso as $alumno) 
{
  $message = "Hola!, te deseo que este año que entra sea muy bonito bonito para tí ".$alumno['NOMBRE']." y que recibas muchos enhorabuenas aunque no sean de la buena.
Un abrazo.
Alberto";
  $subject="Muy feliz feliz Año" ;
  $to=$alumno['CORREO'];
  echo " Enviamos a ".$to;
  
  $okEnvio = enviarCorreo($to,$subject,$message);
  if (!$okEnvio)
  {
    echo 'Error al enviar a:'.$to;
  }
  
}

function enviarCorreo($in_to,$in_subject,$in_message)
{
// enviar correo
    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
    $from = "afsanchez@lasalleinstitucion.es";
    $to = $in_to;
    $subject = $in_subject;
    $message = $in_message;
    //echo "to:".$to;
    //echo "subject:".$subject;
    //echo "message:".$message;
    $headers = "From:" . $from;
    $success = mail($to,$subject,$message, $headers);
    if (!$success) {
      $errorMessage = error_get_last()['message'];
      echo 'e:'.$errorMessage;
    }
  return $success;
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


  </body>

  </html>