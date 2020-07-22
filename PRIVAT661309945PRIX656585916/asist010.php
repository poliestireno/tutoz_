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
$_SESSION["sel11"]=$_POST["sel11"];
$posGuion = strrpos($_POST["sel11"], "--");
$idAs=substr($_POST["sel11"],$posGuion+2,strlen($_POST["sel11"]));
$_SESSION["idAsignatura"]=$idAs;
$_SESSION["dia"]=$_POST["datepicker"];
  

$dia = $_SESSION["dia"];
$idAsignatura = $_SESSION["idAsignatura"];
borrarFaltasAsignaturaDiaYFantasma($db,$idAsignatura,$dia);

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
  </script>
</head>
<body>
<form id="form1" method="post" action="asis00.php">
  <a onclick="managebuttonP()"  class="btn btn-danger btn-outline btn-wrap-text">Menu Principal</a> 
  <p/>
  <p><span class="label label-danger">FALTA RESETEADAS</span></p>
  <p><span class="label label-primary">DIA</span>
  <span class="label label-warning"><?php echo $dia;?></span></p>
  <p><span class="label label-primary">CURSO*ASIGNATURA</span>
  <span class="label label-warning"><?php echo substr($_SESSION["sel11"],0,strrpos($_SESSION["sel11"], "--"));?></span></p>
  
  </form>
</body>
</html>
