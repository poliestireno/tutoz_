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

$idAlu="";
if (isset($_GET['a']))
{
  $idAlu=$_GET['a'];
}
else
{
  $idAlu=$_POST['idAlumnoHid'];
}
$idCur="";
if (isset($_GET['c']))
{
  $idCur=$_GET['c'];
}
else
{
  $idCur=$_POST['idCursoHid'];
}
if (isset($_POST['filtro']))
{
  insertarBono($dbh,$idAlu,$idCur,$_POST['filtro'],$_POST['nombre']);
  $lastInsertId = $dbh->lastInsertId();
  if($lastInsertId)
  {
      $msg="El bono ".$_POST['nombre']." de ".$_POST['filtro']." estrellas se ha aplicado correctamente";
  }
  else
  {
      $msg="Error al aplicar el Bono";
  }
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
  
  <title>Dar Bono</title>

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
  function managebuttonB()
  {
      document.getElementById("form2").action="dar_bono.php";
      document.getElementById("form2").submit(); 
  }

</script>
</head>

<body>
  <?php include('includes/header.php');?>
  <div class="ts-main-content">
  <?php include('includes/leftbar.php');?>
    <div class="content-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <h3 class="page-title">Dar bono</h3>
            <div class="row">

                <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading">Dar BONO<?php 

                    $alu = getAlumnoFromID($dbh,$idAlu);
                    echo " a ".$alu['NOMBRE']." ".$alu['APELLIDO1']." ".$alu['APELLIDO2']." del curso ".getCursoFromCursoID($dbh,$idCur)['NOMBRE'] ?></div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

  <form id="form2" method="post" action="admin_cromos.php">
      <input type='hidden' name='idAlumnoHid' id='idAlumnoHid' value='<?php echo $idAlu?>'/>
      <input type='hidden' name='idCursoHid' id='idCursoHid' value='<?php echo $idCur?>'/>
    
    <br/>

<div class="form-group">
<label class="col-sm-2 control-label">Nombre</label>
<div class="col-sm-4">
<input type="text" name="nombre" class="form-control" required value="">
</div>


<label class="col-sm-2 control-label">Nº Estrellas</label>
<div class="col-sm-4">
<input type="text" class="form-control" id="filtro" required name = "filtro" value ="">
</div>
</div>


    <div class="form-group col-md-4">
      <a onclick="managebuttonB()"  class="btn btn-danger btn-outline btn-wrap-text">Dar Bono</a>
    </div>
  </div>
    <br/><br/><br/>
  
  </form>
               </div>
                </div>




<?php }?>
</div>





                  </div>
                </div>              




              </div>
              <!--div class="form-group">
  <div class="col-sm-5">
    <button class="btn btn-primary" name="submit" type="submit">Save Changes</button>
  </div>
</div> 
            </div-->
           
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
<?php 

} 



  catch (Exception $ex)
  {
      echo "Error:".$ex->getMessage();
  }  

  ?>