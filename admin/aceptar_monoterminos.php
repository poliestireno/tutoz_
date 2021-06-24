<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";
//var_export($_POST);
try
  {
$sql = "SELECT username from admin;";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $result=$query->fetch(PDO::FETCH_OBJ);

if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))
  { 
header('location:index.php');
}
else{


if (isset($_GET['idc6']))
{
  $idCur=$_GET['idc6'];
}
else
{
  $idCur=$_POST['idc6'];
}
//var_export($_POST);
if(isset($_POST['bOk']))
{ 
  
  $idSet = getIdSetPreguntaFromIdAsignatura($dbh,getAsignaturasFromCurso($dbh,$idCur)[0]['ID']);
  insertarPregunta($dbh,$idSet,$_POST['iPregunta'],$_POST['iRespuesta']);
  $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {
      borrarMonoterminoFromId($dbh,$_POST['monoterminoId']);
    }
    $msg="Monotérmino ACEPTADO correctamente";
}
if(isset($_POST['bKo']))
{
  borrarMonoterminoFromId($dbh,$_POST['monoterminoId']);
  $msg="Monotérmino RECHAZADO correctamente";
}   
?>

<!doctype html>
<html lang="en" class="no-js">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="theme-color" content="#3e454c">
  
  <title>Gestionar Monoterminos</title>

  <!-- Font awesome -->
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <!-- Sandstone Bootstrap CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- Bootstrap Datatables -->
  <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
  <!-- Bootstrap social button library -->
  <link rel="stylesheet" href="css/bootstrap-social.css">
  <!-- Bootstrap select -->
  <link rel="stylesheet" href="css/bootstrap-select.css">
  <!-- Bootstrap file input -->
  <link rel="stylesheet" href="css/fileinput.min.css">
  <!-- Awesome Bootstrap checkbox -->
  <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
  <!-- Admin Stye -->
  <link rel="stylesheet" href="css/style.css">

  <style>
  .errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
  background: #dd3d36;
  color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
  background: #5cb85c;
  color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
    </style>
<script type="text/javascript">


</script>
</head>

<body>
<?php

?>
  <?php include('includes/header.php');?>
  <div class="ts-main-content">
  <?php include('includes/leftbar.php');?>
    <div class="content-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <h3 class="page-title">Gestionar Monoterminos de clase <?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
            $aMonoterminos = getMonoterminosFromIdCurso($dbh,$idCur);
  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE']?></h3>
            <div class="row">

              <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading">Gestionar <?php echo count($aMonoterminos)?> Monoterminos</div>
                  <?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>
                  <div class="panel-body">


<?php

foreach ($aMonoterminos as $monoterminoI) {


?>

<form method="post" class="form-horizontal" enctype="multipart/form-data" >
<input type="hidden" name="idc6" value="<?php echo $idCur;?>">
<input type="hidden" name="monoterminoId" value="<?php echo $monoterminoI['ID'];?>">
<div class="form-group">
  <div class="col-sm-4">
  </div>
  <div class="col-sm-4 text-center">
    <img src="" style="width:200px; border-radius:50%; margin:10px;">
  </div>
  <div class="col-sm-4">
  </div>
</div>

<div class="form-group">
  <label class="col-sm-2 control-label">Pregunta:</label>
  <div class="col-sm-9">
  <input type="text" name="iPregunta" id="iPregunta" class="form-control" required value="<?php echo $monoterminoI['PREGUNTA']?>">
  </div>
</div>
<div class="form-group">
  <label class="col-sm-2 control-label">Respuesta (una palabra o palabras entre comas):</label>
  <div class="col-sm-4">
  <input type="text" name="iRespuesta" id="iRespuesta" class="form-control" required value="<?php echo $monoterminoI['RESPUESTAS']?>">
  </div><label class="col-sm-2 control-label">
<?php $ali = getAlumnoFromId($dbh,$monoterminoI['ID_ALUMNO']);
    echo "(".$ali['NOMBRE'].' '.$ali['APELLIDO1'].' '.$ali['APELLIDO2'].")";
?></label>
</div>


<div class="form-group">
  <div class="col-sm-8 col-sm-offset-2">
    <button class="btn btn-success" name="bOk" type="submit">Aceptar</button>
    <button class="btn btn-danger" name="bKo" type="submit">Rechazar</button>
  </div>
</div>

</form>

<?php

}
?>

                  </div>





                  </div>
                </div>              


              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap-select.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap.min.js"></script>
  <script src="js/Chart.min.js"></script>
  <script src="js/fileinput.js"></script>
  <script src="js/chartData.js"></script>
  <script src="js/main.js"></script>
  <script type="text/javascript">
         $(document).ready(function () {          
          setTimeout(function() {
            $('.succWrap').slideUp("slow");
          }, 3000);
          });
  </script>
</body>
</html>
<?php } 


}
  catch (Exception $ex)
  {
      echo "Error:".$ex->getMessage();
  }  

  ?>