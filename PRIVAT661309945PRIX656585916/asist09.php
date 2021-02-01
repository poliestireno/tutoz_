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


function respuestaOk($respDB,$resUsu)
{  
  $aRespDB = explode(",", $respDB);
  foreach ($aRespDB as $respuestaI) 
  {
    if (strcasecmp($respuestaI,$resUsu) == 0)
    {
      return true;
    }
  }
  return false;
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
$apreguntaa['ID'] = "1";
$apreguntaa['PREGUNTA'] = "";
if (isset($_POST['filtro'])){
 // var_export($_POST);
  if ($_POST['tipoPet']=='P')
  {
    if(isset($_POST['todasPreguntas']))
    {
      $aPreguntas = getPreguntasTotal($dbh);
    }
    else
    {
      $aPreguntas = getPreguntasFromAsignaturaID($dbh,$idAsignatura);
      if (Count($aPreguntas)==0)
      {
        $aPreguntas = getPreguntasTotal($dbh); 
      }
    }  
    $apreguntaa = $aPreguntas[rand(0,Count($aPreguntas)-1)];
  }
  else if ($_POST['tipoPet']=='C') 
  {
    $apreguntaa = getPreguntaFromID($db,$_POST["preguntaId"]);
    
  }
}
else{
  $_POST["sel11"] = $_SESSION["sel11"];
}





?>
<html lang="es">
<head>

  <title>Trivial</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="../UTILS/mi.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
  <script>

     
  function managebuttonP()
  {
    document.getElementById("form1").action="asist00.php";
    document.getElementById("form1").submit(); 
  }   
     function managebuttonB(pet)
  {
    document.getElementById("tipoPet").value=pet;
    document.getElementById("preguntaId").value=<?php echo $apreguntaa['ID'];?>;
    document.getElementById("form2").action="asist09.php";
    document.getElementById("form2").submit(); 
  }



function cargaInicial()
{
<?php
if (isset($_POST['filtro']) && ($_POST['tipoPet']=='C'))
{
 if (respuestaOk($apreguntaa['RESPUESTA'], $_POST['filtro']))
  {
    echo 'swal.fire("Correcto!", "Tu respuesta:'.$_POST['filtro'].'", "success")';
  }
  else
  {
    echo 'swal.fire("Regular!", "Tu respuesta:'.$_POST['filtro'].'", "error")';  
  }
}

?>


}
  </script>
</head>
<body onload="cargaInicial()">
  <form id="form2" method="post" action="asist09.php">
    <input type='hidden' name='tipoPet' id='tipoPet'/>
    <input type='hidden' name='preguntaId' id='preguntaId'/>
    <p><span class="label label-primary">CURSO*ASIGNATURA</span>
  <span class="label label-warning"><?php echo substr($_SESSION["sel11"],0,strrpos($_SESSION["sel11"], "--"));?></span></p>
          <span class="input-group-text"><span class="label label-info">Preguntas globales</span></span>
<input type="checkbox" class="form-check-input" id="todasPreguntas" name="todasPreguntas" <?php echo (isset($_POST['todasPreguntas']))?'checked="checked"':''?>>
      <h1><?php echo $apreguntaa['PREGUNTA'];?></h1>
      <input type = "text" id = "filtro" name = "filtro"/>
    <a onclick="managebuttonB('C')"  class="btn btn-danger ">Contestar</a><br/><br/>
    <a onclick="managebuttonB('P')"  class="btn btn-primary btn-outline btn-wrap-text">Nueva pregunta</a>       
    <br>  
  </form>
<form id="form1" method="post" action="asis00.php">
  <a onclick="managebuttonP()"  class="btn btn-danger btn-outline btn-wrap-text">Menu Principal</a> 
  </form>
</body>
</html>