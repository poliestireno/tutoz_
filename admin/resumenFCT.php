<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";
//var_export($_POST);
try
  {
$sql = "SELECT username from admin where username='ADMIN_FCT'";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $result=$query->fetch(PDO::FETCH_OBJ);

if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))
  { 
header('location:index.php');
}
else{

//var_export($aPracticas);

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
	
	<title>FCT RESUMEN</title>

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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

	<style>
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
	background: #5cb85c;
	color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>


</head>

<body>


	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">


<form method="post" id="form1" class="form-horizontal" enctype="multipart/form-data" >
<h3>Pincha en la tabla correspondiente para poder insertar/modificar/borrar datos</h3>





  <h2 style="color: #c9cccf;">TABLAS</h2>

						<!-- Image Map Generated by http://www.image-map.net/ -->
<img src="img/tablas_FCT.png" usemap="#image-map"/>

<map name="image-map">
    <area target="_blank" alt="FCT_ALUMNOS" title="FCT_ALUMNOS" href="manageTabla.php?tabla=FCT_ALUMNOS" coords="225,177,65,34" shape="rect">
    <area target="_blank" alt="FCT_CICLOS" title="FCT_CICLOS" href="manageTabla.php?tabla=FCT_CICLOS" coords="430,92,655,186" shape="rect">
    <area target="_blank" alt="FCT_TUTORES_PROFES" title="FCT_TUTORES_PROFES" href="manageTabla.php?tabla=FCT_TUTORES_PROFES" coords="960,140,751,14" shape="rect">
    <area target="_blank" alt="FCT_PERIODOS" title="FCT_PERIODOS" href="manageTabla.php?tabla=FCT_PERIODOS" coords="17,256,232,398" shape="rect">
    <area target="_blank" alt="FCT_PRACTICAS" title="FCT_PRACTICAS" href="manageTabla.php?tabla=FCT_PRACTICAS" coords="335,238,605,384" shape="rect">
    <area target="_blank" alt="FCT_EMPRESAS" title="FCT_EMPRESAS" href="manageTabla.php?tabla=FCT_EMPRESAS" coords="670,269,991,461" shape="rect">
</map>

<?php

echo ' PENDIENTE RESUMENES :NÚMERO DE ALUMNOS, PRÁCTICAS, CICLOS, PERIODO ACTUAL, NUMERO APTOS, EMPRESAS POR CICLO,...,';

?>
</form>
		</div>

	</div>



</div>

	<!-- Loading Scripts -->
	<script type="text/javascript">
				 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 5000);
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