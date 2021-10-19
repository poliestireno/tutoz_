<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
$msg=0;
//var_export($_POST);

if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{	
	header('location:index.php');
}
else if ($_GET['ida']==getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['ID']) {

if(isset($_POST['submitGuardar']))
{	
	$CORREO = $_SESSION['alogin'];
	$result=getClanFromCorreo($dbh,$CORREO);
	$aIntegrantes = getAlumnosClan($dbh,$result['ID']);
	if (count($aIntegrantes)>0)
	{
		for ($i=0; $i <count($aIntegrantes) ; $i++) 
		{
		 if ($_POST["integrante".$i]!='')
		 {
		 	modificarAutoEvaAlumno($dbh,$_POST["idAutoEva"],$_POST["idAlumno".$i],$_POST["integrante".$i]);
		 }	 
		}
	}
	else
	{
modificarAutoEvaAlumno($dbh,$_POST["idAutoEva"],$_GET['ida'],$_POST["integrante0"]);
	}
	$msg = "Evaluaciones modificadas";
}



$filaAutoEva = getAutoevaluacion($dbh,$_GET['idt'],$_GET['ida']);

if (!$filaAutoEva)
{
	// creamos evaluaciones desde el clan actual

	$CORREO = $_SESSION['alogin'];
	$result=getClanFromCorreo($dbh,$CORREO);
	$aIntegrantes = getAlumnosClan($dbh,$result['ID']);
	$idAutoEva = insertarAutoEvaluacion($dbh,$_GET['ida'],$_GET['idt']);
	if (count($aIntegrantes)>0)
	{
		foreach ($aIntegrantes as $inteI) 
		{
	    	insertarAutoEvaAlumno($dbh,$idAutoEva,$inteI['ID_ALUMNO'],-1);
		}
	}
	else
	{
		insertarAutoEvaAlumno($dbh,$idAutoEva,$_GET['ida'],-1);
	}
}
else
{
	$idAutoEva = $filaAutoEva['ID'];
}

$aIntegrantes = getAutoEvaAlumnos($dbh,$idAutoEva);

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
	
	<title>Autoevaluación</title>

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

@font-face {
  font-family: 'Oli2';
  src: 
  	url('fonts/OlivettiType2.ttf') format('woff'), 
	url('fonts/OlivettiType2.ttf') format('truetype');
}

.Oli2	 {
    font-family: 'Oli2';
    text-align: center;
}
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

<body onload="showNotis()">
<?php
		$CORREO = $_SESSION['alogin'];
		$result=getClanFromCorreo($dbh,$CORREO);
?>
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
									<div class="panel-heading">MI AUTOEVALUACIÓN</div>

<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data">
<h1 class="Oli2"><?php echo ($result==NULL)?'':'Evaluación de mi clan '.$result['NOMBRE'].'</h1><h1 class="Oli2">sobre el reto '.getTareaFromID($dbh,$_GET['idt'])['NOMBRE'];?></h1>

<div class="form-group">
	<div class="col-sm-4">

	</div>
	<?php
	if (count($aIntegrantes)>1)
	{
	?>
	<div class="col-sm-4 text-center">
		<img src="images/<?php 


		$dbImage = htmlentities($result['IMAGEN']);
		if (($dbImage!="")&&(file_exists("images/".$dbImage)))
		{   
			echo $dbImage;
		}
		else
		{
			echo "anonimous_clan.jpg";
		}

		?>" style="width:200px; border-radius:50%; margin:10px;">
		
	</div>
	<?php
	}
	?>

	<div class="col-sm-4">
	</div>
</div>

<div class="form-group">

</div>

<div class="form-group">



<input type="hidden" name="idAutoEva" class="form-control" required value="<?php echo $idAutoEva;?>">

<?php 
$i=0;
foreach ($aIntegrantes as $inteI) 
{
	$valueIntegrante=$inteI['ID_ALUMNO'];
?>



	<label class="col-sm-2 control-label">Nota [0..10]</label>
	<div class="col-sm-4">
	<input type="number" min="0" max="10" placeholder="nota entre 0 y 10" id="idNota" name="integrante<?php echo $i?>" class="form-control" value="<?php echo $inteI['NOTA']?>">
	<input type="hidden" name="idAlumno<?php echo $i?>" class="form-control" required value="<?php echo $inteI['ID_ALUMNO'];?>">
	</div>
		<div class="col-sm-6"><label readonly="readonly" class="form-control"><?php echo ($valueIntegrante=="")?"":(getAlumnoFromId($dbh,$valueIntegrante)['NOMBRE']." ".getAlumnoFromId($dbh,$valueIntegrante)['APELLIDO1'])	;?></label></div>
<?php
$i++;
}
?>

</div>



<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2">
		<button class="btn btn-primary" name="submitGuardar" >Guardar cambios</button>
	</div>
</div>

</form>

<!--<form method="post" class="form-horizontal" onsubmit="return validateForm3()">

<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2">
		<button class="btn btn-warning" name="submit3" type="submit">Salirme del Clan</button>
	</div>
</div>

</form>
<form method="post" class="form-horizontal" onsubmit="return validateForm2()">

<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2">
		<button class="btn btn-danger" name="submit2" type="submit">Borrar Clan</button>
	</div>
</div>

</form>
-->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<label>   [Información casi totalmente confidencial] [solo la ve el profesor]</label>
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
	function showNotis()
	{		
		<?php
		$notisOff = getNotificationsOff($dbh,$_SESSION['alogin']);
		$i=1;
		$notas="";
		foreach ($notisOff as $noti) 
		{
			$nota = preg_replace( "/\r|\n/", "", $noti["notitype"] );
			$notas.="<p>".$i.".- ".$nota."</p>";
			$i++;
		}
		setNotificationsOff($dbh,$_SESSION['alogin']);
		
		$alumnoDB = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);
		$notisGene = getNotificationsGenerales($dbh,getAsignaturasFromCurso($dbh,$alumnoDB['ID_CURSO'])[0]['NOMBRE'],$alumnoDB['ULTIMA_FECHA_NOTI_GENERAL']);
		foreach ($notisGene as $noti) 
		{
			$nota = preg_replace( "/\r|\n/", "", $noti["notitype"] );
			$notas.="<p>".$i.".- ".$nota."</p>";
			$i++;
		}
		setNowUltimaFechaNotiGeneralAlumno($dbh,$_SESSION['alogin']);
		if ($notas!="")
		{
		?>
				const { value: formValues } =  Swal.fire({
  title: 'Notas pendientes:',
         showConfirmButton: false,
  html:
        '<?php echo $notas?>'
        ,
        showCloseButton: true,
  focusConfirm: false,
  
});
	<?php }?>	
	}



function validateForm2()
{
	return confirm('¿quieres borrar realmente el clan <?php echo htmlentities(($result==NULL)?'':$result['NOMBRE']);?>?');
}
function validateForm3()
{
	return confirm('¿quieres realmente salirte del clan <?php echo htmlentities(($result==NULL)?'':$result['NOMBRE']);?>?');
}
	</script>
</body>
</html>
<?php } ?>