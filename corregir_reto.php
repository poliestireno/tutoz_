<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
$msg=0;
//var_export($_POST);

if (!isset($_GET['idt']) && (!isset($_GET['ida'])))
{
	if (isset($_POST['ida']))
	{
		$_GET['ida']=$_POST['ida'];
	}
	if (isset($_POST['idt']))
	{
		$_GET['idt']=$_POST['idt'];
	}

}


if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{	
	header('location:index.php');
}
else if ($_GET['ida']==getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['ID']) {
$CORREO = $_SESSION['alogin'];


if(isset($_POST['submitGuardar']))
{
	//var_export($_POST);
	modificarAlumnoTareaNotaCorregida($dbh,$_GET['idt'],getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['ID'],$_POST['nameNota']);
	modificarAlumnoTareaComentCorreccion($dbh,$_GET['idt'],getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['ID'],$_POST['textComent']);	
}
	$alumnoTareaYo = getDatosAlumnoTarea($dbh,$CORREO,$_GET['idt']);
	$alumnoAcorregir = getAlumnoFromID($dbh,$alumnoTareaYo['ID_ALUMNO_A_CORREGIR']);

$alumnoTareaACorregir = getDatosAlumnoTarea($dbh,$alumnoAcorregir['CORREO'],$_GET['idt']);
$comentarioEntrega = $alumnoTareaACorregir['COMENTARIO'];
$datosReto = getTareaFromID($dbh,$_GET['idt']);


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
								<h1 class="Oli2">Corrección del reto <?php echo getTareaFromID($dbh,$_GET['idt'])['NOMBRE'];?></h1>
								<div class="panel panel-default">

<div class="panel-heading">LA ENTREGA A CORREGIR</div>


									<div class="panel-body">
<?php echo $comentarioEntrega;?>
</div>
</div>
								<div class="panel panel-default">

<div class="panel-heading">INSTRUCCIONES PARA LA CORRECCIÓN</div>


									<div class="panel-body">
<?php echo $datosReto['RUBRICA'];?>

</div>
</div>
<div class="panel panel-default">
									<div class="panel-heading">MI CORRECCIÓN</div>

<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data" method="post">

<input type="hidden" name="idt" value="<?php echo $_GET['idt'];?>">
<input type="hidden" name="ida" value="<?php echo $_GET['ida'];?>">



<div class="form-group">
	<label class="col-sm-2 ">Nota [0..10]</label>
</div>
<div class="form-group">
	<div class="col-sm-4">
<input type="number" min="0" max="10" placeholder="nota entre 0 y 10" id="idNota" name="nameNota" class="form-control" value="<?php echo $alumnoTareaYo['NOTA_CORREGIDA'];?>" required min="0" step=".01">
</div>

</div>
<div class="form-group">
	<label class="col-sm-12 ">Comentarios sobre la corrección (lo que se ha puesto en cada % de las instrucciones y porqué)</label>
</div>
  <div>
    <!-- para el texto enriquecido, pero no lo pongo porque en el post envia etiquetas de formato <b> ... textarea name="textComent" id="summernote"></textarea-->  

        <textarea required name="textComent" class="form-control"><?php echo $alumnoTareaYo['COMENT_CORRECCION'];?></textarea>  

</div>
<div class="form-group">
	<div class="col-sm-4">

	</div>

	
	<div class="col-sm-4">
	</div>
</div>

<div class="form-group">
	<div class="col-sm-2">
		<button class="btn btn-primary" name="submitGuardar" >Calificar reto</button>
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
	<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
	<script type="text/javascript">

				 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 3000);
					});
    $(document).ready(function() {
        $('#summernote').summernote();
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


	</script>
</body>
</html>
<?php } ?>