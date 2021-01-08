<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";
$sql = "SELECT username from admin;";
		$query = $dbh -> prepare($sql);
		$query->execute();
		$result=$query->fetch(PDO::FETCH_OBJ);

if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))
	{	
header('location:index.php');
}
else{
$idCur="";

	//var_export($_POST);	
		if (isset($_GET['idc']))
		{
		  $idCur=$_GET['idc'];
		}
		else
		{
		  $idCur=$_POST['idc3'];
		}

//	$msg="todo ok";


	  
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
	
	<title>Resultados Juicios</title>

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

</head>

<body>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h3 class="page-title">Resultados Juicios de clase <?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE']?></h3>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Juicios</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">

<form action="resultados_juicios.php" id="form3" method="post">
  <input type="hidden" name="idc3" id="idc3" value="<?php echo $_POST['idc3']?>">

	<div class="form-group">
<label class="col-sm-1 control-label">Juicios</label>
                            <div class="col-sm-5">
  <select class="form-control col-md-2" ID="sJuicioId" name="sJuicioId">
  	<option></option>
<?php
	$aJuicios = getJuiciosFromAsignatura($dbh,getAsignaturasFromCurso($dbh,$idCur)[0]['ID']);
    foreach ($aJuicios as $juicioI) 
    {
echo "<option value='".$juicioI['ID']."'>".$juicioI['NOMBRE']." (".$juicioI['FECHA'].")</option>";
    }

?>
  </select>
                            </div>                          

	</div>
	<div class="col-sm-8 col-sm-offset-2"></div>
	<div class="col-sm-8 col-sm-offset-2"></div>
    <div class="form-group">
  <div class="col-sm-2 col-sm-offset-2">
    <button class="btn btn-warning" name="submit" type="submit">Ver resultado</button>
  </div>
</div>
</form>		
<br/><br/><br/><br/><br/>
<div class="form-group">
<?php
if (isset($_POST['submit']))
{


$tiposBotones = array( "btn btn-primary", "btn btn-success", "btn btn-warning", "btn btn-info");
$alumnoDB = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);

$juicioElegido = getJuicioFromId($dbh,$_POST['sJuicioId']);

echo '<div class="form-group"><label class="col-sm-4 control-label">'.$juicioElegido['NOMBRE'].'('.$juicioElegido['FECHA'].')</label></div><br/><br/><div class="form-group"><label class="col-sm-8 control-label"> '.$juicioElegido['DESCRIPCION'].'</label></div>';
	
	$aOpciones = explode(",", $juicioElegido['OPCIONES']);

	$hOpcionesNumero = array();
	foreach ($aOpciones as $opcionI) 
	{
		$hOpcionesNumero[$opcionI]=0;
	}
	//var_export($hOpcionesNumero);

	$aVotaAlumJui = getVotacionesAlumnosJuicio($dbh,$_POST['sJuicioId']);
	$totalVotos = 0;
	foreach ($aVotaAlumJui as $votaI) {
		$hOpcionesNumero[$votaI['OPCION']]++;
		$totalVotos++;
	}
	//var_export($hOpcionesNumero);
	$i=0;
	foreach ($hOpcionesNumero as $key => $value) 
	{
		$porcentaje = round((($value/(($totalVotos>0)?$totalVotos:1))*100),1);
		if ($porcentaje<10)
		{
			$porcentaje="0".$porcentaje;
		}
		$porcentaje.="";
		if (ctype_digit($porcentaje)) 
		{
			$porcentaje.=".0";
		}

		echo '<div id="aa_0" class="btn-group btn-group-justified" ><a id="bb_0" style="text-align:left;font-family:Courier; font-size:40px; height: 90px" class="'.$tiposBotones[$i % sizeof($tiposBotones)	].'">'.$porcentaje.'%-'.$key.' ('.$value.')</a></div>';
		$i++;
	}
	echo '<label class="col-sm-8 control-label">Votos totales: '.$totalVotos.'</label>';

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
					}, 5000);
					});
	</script>
</body>
</html>
<?php } ?>