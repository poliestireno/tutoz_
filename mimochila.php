<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
$msg="";


if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{	
	header('location:index.php');
}
else{

	$CORREO = $_SESSION['alogin'];	
	$idAsignatura = getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$CORREO)['ID_CURSO'])[0]['ID'];
	$aPreguntas = getPreguntasFromAsignaturaID($dbh,$idAsignatura);
	if (Count($aPreguntas)==0)
	{
		$aPreguntas = getPreguntasTotal($dbh); 
	}
	$apreguntaa = $aPreguntas[rand(0,Count($aPreguntas)-1)];

	
function esDiferenteTipo($resDefinitiva,$isRespNum)
{
	$isRespNumNueva = is_numeric($resDefinitiva);
	return ($isRespNumNueva xor $isRespNum);
}


$numOpcionesPreguntas = getConfGeneral($dbh, "NUMERO_OPCIONES_PREGUNTAS");
$aRespuestas2 = array();
$aRespDB2 = explode(",", $apreguntaa['RESPUESTA']);
$aRespuestas2[] = $aRespDB2[rand(0,Count($aRespDB2)-1)];

//$aRespuestasOk = array();

$isRespNum = is_numeric($aRespuestas2[0]);
//$aRespuestasOk[] = $aRespuestas2[0];
for ($i=1; $i < $numOpcionesPreguntas; $i++) 
{ 
	$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
	while ($apreguntaa2['ID']==$apreguntaa['ID'])
	{
		$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
	}
	$aRespDB22 = explode(",", $apreguntaa2['RESPUESTA']);
	 $resDefinitiva= $aRespDB22[rand(0,Count($aRespDB22)-1)];
	$contt=0;
while (((in_array($resDefinitiva, $aRespuestas2)||(esDiferenteTipo($resDefinitiva,$isRespNum)))&&($contt<1000)))
	{
		$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
		while ($apreguntaa2['ID']==$apreguntaa['ID'])
		{
			$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
		}
		$aRespDB22 = explode(",", $apreguntaa2['RESPUESTA']);
	 	$resDefinitiva= $aRespDB22[rand(0,Count($aRespDB22)-1)];
		$contt++;
	}
	if ($contt>=1000)
	{
		$resDefinitiva= $aRespDB22[rand(0,Count($aRespDB22)-1)];
	}
	//$aRespuestasOk[]=$resDefinitiva;
	$aRespuestas2[]=$resDefinitiva;
	//var_export($aRespuestas2);
}

shuffle($aRespuestas2);


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
	
	<title>Mi Mochila</title>

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
	function testRapido()
	{       	
      	document.getElementById("idPreguntaNext").value=<?php echo $apreguntaa['ID']?>;
      	document.getElementById("ids_preguntas").value=<?php echo $apreguntaa['ID']?>;
      	document.getElementById("respuestaUsu").value='SABESQUESI';
      	document.getElementById("form1").submit(); 
	}
	function testLento()
	{ 
	}
function init()
	{ 
	}
</script>

</head>

<body onload="init()">


	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">



								<div class="panel panel-default">
									<div class="panel-heading">Mi Mochila</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data" id="form1" action="fastest.php">
<input type="hidden" name="respuestaUsu" id="respuestaUsu"/>
<input type="hidden" name="ids_preguntas" id="ids_preguntas" value=""/>
<input type="hidden" name="idPreguntaNext" id="idPreguntaNext" value=""/>
<input type="hidden" name="ids_respuestas" id="ids_respuestas" value="<?php echo implode(",", $aRespuestas2); ?>"/>

								<div class="panel panel-default col-sm-2">
									<div class="panel-heading">Test Rápido</div>
									<div class="panel-body">

<div class="form-group">
	<div class="col-sm-10">
		<label class="control-label">Test Rápido</label>
	</div>
</div>
<div class="form-group">

<div class="col-md-4">
  <a onclick="testRapido();" class="btn btn-info btn-outline btn-wrap-text">Hacer test</a>
</div> 

	</div>

									</div>


								</div>

								<div class="panel panel-default col-sm-2 col-sm-offset-1">
									<div class="panel-heading">Test Lento</div>
									<div class="panel-body">

<div class="form-group">
	<div class="col-sm-10">
		<label class="control-label">Test Lento</label>
	</div>
</div>
<div class="form-group">

<div class="col-md-4">
  <a onclick="testLento();" class="btn btn-info btn-outline btn-wrap-text">Hacer test</a>
</div> 

	</div>

									</div>


								</div>


</form>
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
					}, 3000);
					});

	</script>
</body>
</html>
<?php } ?>