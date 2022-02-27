<?php
include('../includes/config.php');
require_once("dbutils.php");

$db=conectarDB();


$filaSitio = getSitioFromMapID($dbh,9);
$posx = rand($filaSitio['INI_X'],$filaSitio['MAX_X']);
$posy = rand($filaSitio['INI_Y'],$filaSitio['MAX_Y']);
$cont = 1000;
while (existeLugar($dbh,$filaSitio['ID'],$posx,$posy)) 
{
    $posx = rand($filaSitio['INI_X'],$filaSitio['MAX_X']);
    $posy = rand($filaSitio['INI_Y'],$filaSitio['MAX_Y']);
    $cont--;
    if ($cont == 0)
    {
        //$haySitio=false; siempre tiene que haber sitio en exteriores
        break;
    }
}

// se inserta su bot inicial
insertarBot($dbh,"HOLA","","",1,2,0,0,0,"",0,0,0,"",9,$posx,$posy);
$lastInsertIdBot = $dbh->lastInsertId();

// se inserta su actor (jugador) inicial
insertarActor($dbh,500,100,0,50);
$lastInsertIdActor = $dbh->lastInsertId();

echo "iB:".$lastInsertIdBot." iA:".$lastInsertIdActor;

//$listaAlumnos = getAlumnosGradoNivel($db,"DAM",2);

//$listaAlumnos = getAlumnosGradoNivel($db,"ASIR",1);

//var_dump(getAsignaturasConCurso($db));
//var_dump(getCursosGradoNivel($db));
//echo $fecha->format('Y-m-d');
//var_dump(getDate()["wday"]);
//var_dump(getSesionesAsignaturas($db));
//var_dump(getNumElegAlumnos2($db, "TIC_C",1));
//var_dump(MarcoAntonioritmo(getNumElegAlumnos2($db, "TIC_C",1)));

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