<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
$msg="";
$msg2="";
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{	
	header('location:index.php');
}
else{
//var_export($_POST);
//var_export($_SESSION);
$CORREO = $_SESSION['alogin'];
$idAlumno = getAlumnoFromCorreo($dbh,$CORREO)['ID'];
 
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
	
	<title>Ver empresas FCT</title>

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
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-lightness/jquery-ui.css">


</head>

<body>
<?php
		

		//$bot = getBot($dbh,$CORREO);
		//$alumnoo = getAlumnoFromCorreo($dbh,$CORREO);
		//$bot['SALUDO']="HALLO";
		//$bot['PALABRA_CLAVE']="NAJAS";

?>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">

<?php 

$aDataTotal = ejecutarQuery($dbh,"SELECT * FROM FCT_EMPRESAS WHERE DESCRIPCION LIKE '%2022%'");
			$sValorSeleccionado = "";
			foreach ($aDataTotal as $filaTabla) 
			{
?>
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">ID:<?php echo $filaTabla['ID']?>   EMPRESA:<?php echo $filaTabla['NOMBRE']?></div>

<div style="background: #FFB9B3;" class="panel-body">


<div class="form-group">
	
<label class="col-sm-8 control-label" style="font-size: 12px"><?php echo $filaTabla['DESCRIPCION']?></label><br/>
</div>


									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>


<?php 

}
?>




		</div>
	</div>
	<!-- Loading Scripts -->
	<script src="js/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
</body>
</html>
<?php } ?>