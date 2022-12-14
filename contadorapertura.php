<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
	{	
header('location:index.php');
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
	
	<title>Abriendo sobre</title>

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
 #clock {
	width: auto;
	height: auto;
	border-radius: 20%;
	background-color: red;
	margin: auto;
}

span {
	display: block;
	width: 100%;
	margin: auto;
	padding-top: 60px;
	text-align: center;
	font-size: 300px;
	color: white;
	height: auto;
} 
		</style>


</head>

<body>



<form id="form1" method="post" class="form-horizontal" enctype="multipart/form-data" action="nuevocromo.php">

<div id="clock">
	<span id="seconds"><?php echo getNumeroSegundosAlumno($dbh,$_SESSION['alogin'])?></span>
</div>

</form>


	<!-- Loading Scripts -->
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
	

setState = document.getElementById("seconds").innerHTML;
//setState = 300;
cambioColor = Math.round(setState/4);
aColores = ["red", "orange", "yellow", "green"];
indiceColor=0;
contador = 0;
function countdown() {
	setState--;
	contador++;
	if (contador % cambioColor == 0)
	{
		indiceColor++;
	}
	if (setState==0)
	{
		document.getElementById("form1").submit(); 
	}
	else
	{
		document.getElementById('clock').style.backgroundColor = aColores[indiceColor];
		document.getElementById("seconds").innerHTML = setState;
	}
	
	if (setState > 0) {
		setTimeout(countdown, 1000);
	}
};

setTimeout(countdown, 1000);

	</script>
</body>
</html>	