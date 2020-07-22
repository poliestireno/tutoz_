<?php
require_once("dbutils.php");

$db=conectarDB();

//$listaAlumnos = getAlumnosGradoNivel($db,"DAM",2);

//$listaAlumnos = getAlumnosGradoNivel($db,"ASIR",1);

//var_dump(getAsignaturasConCurso($db));
//var_dump(getCursosGradoNivel($db));
//echo $fecha->format('Y-m-d');
//var_dump(getDate()["wday"]);
//var_dump(getSesionesAsignaturas($db));
var_dump(getNumElegAlumnos2($db, "TIC_C",1));
var_dump(MarcoAntonioritmo(getNumElegAlumnos2($db, "TIC_C",1)));

/*$alumnos = getNumElegAlumnos($db,"TIC_C",1);
$x = 10000;
var_dump($alumnos);
for ($i = 0; $i < 1000000; $i++){
  $y = intval(BrioAlgoritmo($alumnos));
  if ($x > $y){
      $x = $y;
      echo "<br>Menor: ".$x."<br>";
  }
}*/
?>