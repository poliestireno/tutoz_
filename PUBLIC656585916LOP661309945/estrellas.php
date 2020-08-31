<?php
session_start();
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$db=conectarDB();

$_SESSION["idAsignatura"]=$_GET["asig"];
$idAsignatura = $_SESSION["idAsignatura"];

$curso = getCursoFromAsignaturaId($db,$idAsignatura);
$grado = $curso["GRADO"];
$nivel = $curso["NIVEL"];
$nSesionesTotalesEstrellas = getNumeroSesionesEstrellasFromAsignatura($db,$idAsignatura);

?>
<html lang="es">
<head>
  <title>Asis-tencia</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../UTILS/mi.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <script>
     
  </script>
</head>
<body>
  <br/>
<form id="form1" method="post" action="asis00.php">

  <span class="label label-danger">ESTRELLAS </span>
  <span class="label label-danger"><?php echo $grado." ".$nivel."º ".getAsignaturaFromAsignaturaId($db,$idAsignatura)["NOMBRE"]?></span>
  
  <br/><br/>
  <p><span class="label label-primary">SESIONES TOTALES ESTRELLAS</span>
  <span class="label label-warning"><?php echo $nSesionesTotalesEstrellas?></span></p>
  <p><span class="label label-primary">MÁXIMO ESTRELLAS POSIBLES</span>
  <span class="label label-warning"><?php echo $nSesionesTotalesEstrellas*5?></span></p>
 
  <?php 
  $arrayAlumnosFaltones = getAlumnosInformeEstrellas($db,$idAsignatura);
  $listaAlumnos = getAlumnosGradoNivel($db,$grado,$nivel);
  //var_dump($listaAlumnos);
  //echo "<br>";
  //var_dump($arrayAlumnosFaltones);
    $nPosicion=0;
    for($i = 0;$i< Count($arrayAlumnosFaltones);$i++){
     
      $fila = $arrayAlumnosFaltones[$i];
      if ($i == 0)
      {
        $nPosicion=1;
      }
      else
      {
        if ($fila["NFALTAS"]<$arrayAlumnosFaltones[$i-1]["NFALTAS"])
        {
          $nPosicion++;
        }
      }
      
      echo '<span class="label label-primary">'.$nPosicion.' '.$fila["Nombre"].' '.$fila["Apellido"].'</span>';
      echo '<span class="label label-success">'.$fila["NFALTAS"].'</span>';
       if ($nSesionesTotalesEstrellas>0)
      {
        echo '<span class="label label-success">'.number_format(($fila["NFALTAS"]*100)/($nSesionesTotalesEstrellas*5),2).'%</span>';
      }   
      echo '<br/><br/>';
      
    }
  $countAux=Count($arrayAlumnosFaltones)+1;
  for($i = 0; $i< Count($listaAlumnos);$i++){
    $esta=false;
    for($j=0;$j< Count($arrayAlumnosFaltones);$j++){
      if($listaAlumnos[$i]['ID']==$arrayAlumnosFaltones[$j]['ID_ALUMNO']){
        $esta=true;
        break;
      }
    }
    if(!$esta){
      echo '<span class="label  label-primary">'.($countAux++)." ".$listaAlumnos[$i]['NOMBRE']." ". $listaAlumnos[$i]['APELLIDO1'].'</span>';
       echo '<span class="label label-success">0</span>';
      echo "<br><br>";
    }
  }
  ?>
  </form>
</body>
</html>