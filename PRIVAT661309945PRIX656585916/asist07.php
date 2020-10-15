<?php
 session_start();
 include('../includes/config.php');
 require_once("../UTILS/dbutils.php");
  $sql = "SELECT username from admin;";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $result=$query->fetch(PDO::FETCH_OBJ);
if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))
{ 
  header('location:../admin/index.php');
}
$db=conectarDB();
if (!isset($_POST["sel11"])){
  $_POST["sel11"] = $_SESSION["sel11"];
}
$_SESSION["sel11"]=$_POST["sel11"];
$posGuion = strrpos($_POST["sel11"], "--");
$idAs=substr($_POST["sel11"],$posGuion+2,strlen($_POST["sel11"]));
$_SESSION["idAsignatura"]=$idAs;

$idAsignatura = $_SESSION["idAsignatura"];
if (isset($_POST['filtro'])){
  $vectorAlumnos = buscarAlumnos($db, $_POST['filtro']);
}
else{
  $_POST["sel11"] = $_SESSION["sel11"];
}

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
     
  function managebuttonP()
  {
    document.getElementById("form1").action="asist00.php";
    document.getElementById("form1").submit(); 
  }   
     function managebuttonB()
  {
    document.getElementById("form2").action="asist07.php";
    document.getElementById("form2").submit(); 
  }    
  </script>
</head>
<body>
  <form id="form2" method="post" action="asist07.php">
      <input type = "text" id = "filtro" name = "filtro"/>
    <a onclick="managebuttonB()"  class="btn btn-danger btn-outline btn-wrap-text">Buscar</a> 
      
    <br>
    <?php 
      if (isset($_POST['filtro'])){
        //var_dump($vectorAlumnos);
        foreach ($vectorAlumnos as $alumno)
        {
            echo '<span class="label label-primary">'.$alumno['nCompleto'].'</span>';
            echo '<span class="label label-primary">'."Faltas: ".getFaltasFromAlumnoId($db, $alumno['ID']).'</span>';   
            echo '<span class="label label-primary">'."Estrellas: ".getEstrellasFromAlumnoId($db, $alumno['ID']).'</span>';   
            echo '<span class="label label-danger">'."Curso: ".getNombreCursoFromAlumno($db, $alumno['ID']).'</span>';
            echo '<span class="label label-info">'."Numero de veces elegido: ".getNumeroElegidosPorIdAlumno($db,$alumno['ID']).'</span>';
            echo '<br/><br/>';
       } 
      }
    $nSesionesTotales = getNumeroSesionesFromAsignatura($db,$idAsignatura);
    $nSesionesTotalesEstrellas = getNumeroSesionesEstrellasFromAsignatura($db,$idAsignatura);
    ?>
  </form>
<form id="form1" method="post" action="asis00.php">
  <a onclick="managebuttonP()"  class="btn btn-danger btn-outline btn-wrap-text">Menu Principal</a> 
  <p/>
  <p><span class="label label-primary">CURSO*ASIGNATURA</span>
  <span class="label label-warning"><?php echo substr($_SESSION["sel11"],0,strrpos($_SESSION["sel11"], "--"));?></span></p>
  <p><span class="label label-primary">SESIONES TOTALES</span>
  <span class="label label-warning"><?php echo $nSesionesTotales?></span></p>
   <span class="label label-danger">INFORME FALTAS</span>
  <br/><br/>
  <?php 
  $arrayAlumnosFaltones = getAlumnosInforme($db,$idAsignatura);
  $_SESSION["sel11"]=$_POST["sel11"];
  $pos = strrpos($_POST["sel11"], "*");
  $grado=substr($_POST["sel11"],0,$pos-1);
  $nivel=substr($_POST["sel11"],$pos-1,1);
  $listaAlumnos = getAlumnosGradoNivel($db,$grado,$nivel);
  //var_dump($listaAlumnos);
  //echo "<br>";
  //var_dump($arrayAlumnosFaltones);
    for($i = 0;$i< Count($arrayAlumnosFaltones);$i++){
     
      $fila = $arrayAlumnosFaltones[$i];
      echo '<span class="label label-primary">'.($i+1).' '.$fila["Nombre"].' '.$fila["Apellido"].'</span>';
      echo '<span class="label label-success">'.$fila["NFALTAS"].'</span>';
      if ($nSesionesTotales>0)
      {
        echo '<span class="label label-success">'.number_format(($fila["NFALTAS"]*100)/$nSesionesTotales,2).'%</span>';
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

  <span class="label label-danger">INFORME ESTRELLAS</span>
  <br/><br/>
  <p><span class="label label-primary">SESIONES TOTALES ESTRELLAS</span>
  <span class="label label-warning"><?php echo $nSesionesTotalesEstrellas?></span></p>
  <p><span class="label label-primary">MÁXIMO ESTRELLAS POSIBLES</span>
  <span class="label label-warning"><?php echo $nSesionesTotalesEstrellas*4?></span></p>
 
  <?php 
  $arrayAlumnosFaltones = getAlumnosInformeEstrellas($db,$idAsignatura);
  $_SESSION["sel11"]=$_POST["sel11"];
  $pos = strrpos($_POST["sel11"], "*");
  $grado=substr($_POST["sel11"],0,$pos-1);
  $nivel=substr($_POST["sel11"],$pos-1,1);
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
        echo '<span class="label label-success">'.number_format(($fila["NFALTAS"]*100)/($nSesionesTotalesEstrellas*4),2).'%</span>';
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
  <span class="label label-danger">INFORME ELEGIDOS</span>
  <br/><br/>
 
  <?php 
  $arrayFantamas = getFantasmasFromAsignaturaID($db,$idAsignatura);
  $arrayTotalElegidos = array();
  
  foreach ($arrayFantamas as $fantasma) 
  {    
    echo '<p><span class="label label-warning">'.$fantasma['DIA'].'</span>';
    $vectorElegidos = explode(",", $fantasma['ELEGIDOS']);
    if ($vectorElegidos[0]!='')
    {
      $cont2=1;
      for ($i=0; (($i < Count($vectorElegidos))&&($i<3)); $i++) 
      { 
        if (!array_key_exists($vectorElegidos[$i], $arrayTotalElegidos)) 
        {
          $arrayTotalElegidos[$vectorElegidos[$i]]=0;
        }
        $arrayTotalElegidos[$vectorElegidos[$i]]=$arrayTotalElegidos[$vectorElegidos[$i]]+1;
        $alumno = getAlumnoFromID($db,$vectorElegidos[$i]);
        //var_export($alumno);
      echo '<span class="label label-primary">'.$cont2.' '.$alumno['NOMBRE'].' '.$alumno['APELLIDO1'].'</span>';
      $cont2=$cont2+1;
      }
    }
  echo '</p>';
  }
  
  $groups = array();
  foreach ($arrayTotalElegidos as $k => $v) {
    $groups[$v][] = $k;
  }
  krsort($groups);
  $sorted = array();
  foreach ($groups as $value => $group) {
    foreach ($group as $key) {
        $sorted[$key] = $value;
    }
  }
  echo '<br/><p><span class="label label-danger">RANKING ELEGIDOS</span>
  </p>';
  $cont=1;
  $contIAnt=0;
  foreach ($sorted as $alumId => $contI ) 
  {
    if ($contIAnt>$contI)
    {
      $cont=$cont+1;     
    }
    $contIAnt=$contI;
    $alumno = getAlumnoFromID($db,$alumId);
    echo '<p><span class="label label-primary">'.$cont.' '.$alumno["NOMBRE"].' '.$alumno["APELLIDO1"].'</span>';
    echo '<span class="label label-success">'.$contI.'</span></p>';
  }





  ?>
  </form>
</body>
</html>