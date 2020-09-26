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

$nSesiones = $_SESSION["nSesiones"];
$dia = $_SESSION["dia"];
$idAsignatura = $_SESSION["idAsignatura"];
// Se inserta el registro fantasma
if(!existeFantasma($db,$_SESSION["idAsignatura"],$_SESSION["dia"])){
  insertarFalta($db,-1,$_SESSION["idAsignatura"],$_SESSION["nSesiones"],$_SESSION["dia"]);
}
else
{
  modificarFantasma($db,$_SESSION["idAsignatura"],$_SESSION["dia"],$_SESSION["nSesiones"]);
}

borrarFaltasAsignaturaDia($db,$idAsignatura,$dia);

foreach($_SESSION['vAlumnos'] as $alumno)
{
  $pos = strrpos($alumno, "--");
  $idAlum=substr($alumno,$pos+2,strlen($alumno));
  insertarFalta($db,$idAlum,$idAsignatura,$nSesiones,$dia);
}
$_SESSION['contador']=$_SESSION['contador']-1;
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
  function managebutton()
  {
    document.getElementById("form1").action="asist01.php";
    document.getElementById("form1").submit(); 
  }      
  function managebuttonP()
  {
    document.getElementById("form1").action="asist00.php";
    document.getElementById("form1").submit(); 
  }      
  </script>
</head>
<body>
<form id="form1" method="post" action="asis01.php">
  <input type='hidden' name='altura' id='altura' value='<?php echo $_POST["altura"]?>'/>
  <input type='hidden' name='asis02' id='asis02' value='asis02'/>
  <a onclick="managebutton()"  class="btn btn-danger btn-outline btn-wrap-text">Seguir Lista</a> 
  <a onclick="managebuttonP()"  class="btn btn-danger btn-outline btn-wrap-text">Menu Principal</a> 
  <p/>
  <p><span class="label label-danger"><?php echo count($_SESSION['vAlumnos'])." " ?>FALTA<?php echo (count($_SESSION['vAlumnos'])>1)?"S" :"" ?> INSERTADA<?php echo (count($_SESSION['vAlumnos'])>1)?"S" :"" ?></span></p>
  <p><span class="label label-primary">DIA</span>
  <span class="label label-warning"><?php echo $dia;?></span></p>
  <p><span class="label label-primary">CURSO*ASIGNATURA</span>
  <span class="label label-warning"><?php echo substr($_SESSION["sel11"],0,strrpos($_SESSION["sel11"], "--"));?></span></p>
  <p><span class="label label-primary">NÚMERO SESIONES</span>
  <span class="label label-warning"><?php echo $nSesiones;?></span></p>
  <?php
    foreach($_SESSION['vAlumnos'] as $alumno)
    {
      $pos = strrpos($alumno, "--");
      $nombreAlum=substr($alumno,0,$pos);
      echo '<p><span class="label label-default">'.$nombreAlum.'</span></p>';  
    }
  $cont = 1;
  $contElegidos = 0;
  $text = "";
  $listaElegidos="";
  $comma="";
  //var_export($_SESSION);
    foreach($_SESSION['vAlumnos2'] as $alumno)
    {
      
      if (in_array($cont, $_SESSION['vElegidos'])){
        $pos = strrpos($alumno, "--");
        $idAlumno= substr($alumno,$pos+2);
 
        $confAsig = getConfAsignaturaFromID($db,getAsignaturasFromCurso($db,getAlumnoFromId($db,$idAlumno)['ID_CURSO'])[0]['ID_CONF_ASIGNATURAS'])['NOMBRE'];
        //echo('confAsig:'.$confAsig);
        //mi_info_log('confAsig:'.$confAsig);

        if ($confAsig!='MENU_SIMPLON')
        {
          insertarBono($db,$idAlumno,getAlumnoFromId($db,$idAlumno)['ID_CURSO'],getConfGeneral($dbh, "NUM_ESTRELLAS_ENHORABUENA"),"Enhorabuena de la buena (".$dia.")");
        }
        $listaElegidos.=$comma .  $idAlumno;
        $comma=",";
        $nombreAlum=substr($alumno,0,$pos);
        $text.= '<p><span class="label label-default">'.$nombreAlum.'</span></p>';  
        $contElegidos++;
      }
      $cont++;
    }
  if($listaElegidos!="")
  {
    $fila =  getFantasma($db,$idAsignatura,$dia);
    if($fila["ELEGIDOS"]!=""){
      $listaElegidos.=",".$fila["ELEGIDOS"];
    }
    insertarElegidosEnFantasma($db,$idAsignatura,$dia,$listaElegidos);
    
  }

   ?>  
  
  <p><span class="label label-primary">NÚMERO ELEGIDOS</span>
  <span class="label label-warning"><?php echo $contElegidos;?></span></p><?php echo $text; ?>
  <a href="https://lasalleinstitucion.sallenet.org/mod/sallenet/calendario/index.php?op=incidencias&menu=ahora" target="_blank">Sallenet</a>
  </form>
</body>
</html>
