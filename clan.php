<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
$msg=0;
//var_export($_POST);
//var_export($_FILES['image']);
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{	
	header('location:index.php');
}
else{
	
if(isset($_POST['submit3']))
{
	$CORREO = $_SESSION['alogin'];
	$result=getClanFromCorreo($dbh,$CORREO);
	if ($result!=NULL)
	{
		borrarAlumnoFromClanId($dbh,getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['ID'],$result['ID']);
		$aIntegrantes = getAlumnosClan($dbh,$result['ID']);
		$msg = "Saliste del clan!";
		if (Count($aIntegrantes)==0)
		{
			borrarClanFromId($dbh,$result['ID']);
			$msg = "Saliste del clan y se borró el clan!";
		}		
		
	}
	
}	
else if(isset($_POST['submit2']))
{
	$CORREO = $_SESSION['alogin'];
	$result=getClanFromCorreo($dbh,$CORREO);
	if ($result!=NULL)
	{
		borrarAlumnosClanFromClanId($dbh,$result['ID']);
		borrarClanFromId($dbh,$result['ID']);
		$msg = "Clan borrado!";
	}
	
}	
else if(isset($_POST['submit']))
  {	
	$name=$_POST['name'];
    $descripcion=$_POST['DESCRIPCION'];	
	$IDedit=$_POST['editID'];
  	$image=$_POST['image'];
	
	// Si el valor de la ubicacion de la imagen es vacio, se asigna la imagen que esta en el servidor por defecto.
	if (empty($file_loc)) {
		$file_loc="anonimous_clan.jpg";
	} {
		$file_loc=$image;
	}
	// Si hay una imagen seleccionada, se sube a la carpeta de imagenes.
	// Es decir si se ha seleccionado el boton de subir imagen, se sube la imagen a la carpeta de imagenes.
	if (isset($_FILES['uploadimage'])) {
		try {
		// Si esta solicitud entra en algun error, se trata como no válida, invalidando completamente la subida de archivos que no sean imagenes.
		if (
			!isset($_FILES['uploadimage']['error']) ||
			is_array($_FILES['uploadimage']['error'])
		) {
			throw new RuntimeException('Parámetro no válido.');
		}
		// Comprueba el valor de $_FILES['uploadimage']['error'].
		switch ($_FILES['uploadimage']['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new RuntimeException('No se ha enviado ningún archivo.');
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new RuntimeException('Se ha superado el límite del tamaño de los archivo.');
			default:
				throw new RuntimeException('Error desconocido.');
		}
	
		// Aquí comprueba el tamaño de los archivos.
		if ($_FILES['uploadimage']['size'] > 1000000) {
			throw new RuntimeException('Se ha superado el límite del tamaño del archivo.');
		}
	
		// ¡¡¡No confíes en el valor de $_FILES['uploadimage']['mime']!!!
		// Compruebe usted mismo el tipo MIME. Porque puede ser una imagen JPEG que se sube como text/plain. y se podria bypassear este filtro
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		if (false === $ext = array_search(
			$finfo->file($_FILES['uploadimage']['tmp_name']),
			array(
				'jpg' => 'image/jpeg',
				'png' => 'image/png',
				'gif' => 'image/gif',
			),
			true
		)) {
			throw new RuntimeException('Formato del archivo no válido.');
		}
	
		// El valor de la imagen hasheado se guarda en la variable $file_loc.
		// Porque el archivo temporal se borra despues de subirse, por lo que primero se hashea el archivo para tener el valor y luego se sube a la carpeta de imagenes.
		$file_loc=sprintf('%s.%s',
		sha1_file($_FILES['uploadimage']['tmp_name']),$ext);

		// Aqui genera una string hasheada del archivo temporal y la sube a la carpeta /images/.
		// Gracias a esto conseguimos que el nombre del archivo sea unico y no se repita.
		// Con 'sprintf' creamos una cadena con el nombre del archivo y la extensión.
		// No cambiar a $_FILES['uploadimage']['name'] dado a que perderia completamente la seguridad de la subida de archivos.
		if (!move_uploaded_file(
			$_FILES['uploadimage']['tmp_name'],
			sprintf('./images/%s.%s',
				sha1_file($_FILES['uploadimage']['tmp_name']),
				$ext
			)
		)) {
			throw new RuntimeException('No se ha podido subir el archivo.');	
		}
	
		} catch (RuntimeException $e) {
			echo $e->getMessage();
		}
	}

	$image=$file_loc;
	$maxNumAlumClan = getConfGeneral($dbh, "NUMERO_ALUMNOS_CLAN");
	$aAlumnos = array ();
	for ($i=1; $i <=$maxNumAlumClan ; $i++) 
	{
	 if ($_POST["integrante".$i]!='')
	 {
	 	$aAlumnos[] = bindec($_POST["integrante".$i]);
	 }	 
	}
	//var_export($aAlumnos);
	$msg="Información actualizada correctamente.";
	foreach ($aAlumnos as $idAlumno) 
  	{
		if (!existeAlumnoId($dbh,$idAlumno))
		{
	    	$msg = "NO EXISTE ID DE ALUMNO INTRODUCIDO ";
	    	break;
		}
	}
	if ($msg=="Información actualizada correctamente.")
	{
		
		
		//var_export($aAlumnos);
		$CORREO = $_SESSION['alogin'];
		$result=getClanFromCorreo($dbh,$CORREO);
		$idClan = NULL;
		if ($result==NULL)
		{
			$idClan = insertarClan($dbh,$name,$image,$descripcion);
			
		}
		else
		{
			$idClan = $result['ID'];
		}
		$idAlumnoYaExistente = existeAlgunAlumnoFueraDeIdClan($dbh,$idClan,$aAlumnos);
		if ($idAlumnoYaExistente!=NULL)
		{
			$msg = "Uno de los ids de alumnos introducidos ya pertenace a otro clan.";
		}
		else
		{
			modificarClan($dbh,$idClan,$name,$image,$descripcion);
			borrarAlumnosClanFromClanId($dbh,$idClan);
			insertarAlumnosClan($dbh,$idClan,$aAlumnos);
		}

	}
	
}


function getAsteriscos($nBin)
{
	$sBin  = $nBin."";
	$arrayB = str_split($sBin);
	$sRe = "";
	foreach ($arrayB as $charB) 
	{
 		$sRe.="*";
	}
	return $sRe;
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
	
	<title>Editar Clan</title>

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
									<div class="panel-heading">Mi Clan</div>

<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data">
<h1 class="Oli2"><?php echo htmlentities(($result==NULL)?'':$result['NOMBRE']);?></h1>

<div class="form-group">
	<div class="col-sm-4">

	</div>
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
		<input type="file" 	name="uploadimage" class="form-control">
		<input type="hidden" name="image" class="form-control" value="<?php echo htmlentities($result['IMAGEN']);?>">
	</div>
	<div class="col-sm-4">
	</div>
</div>

<div class="form-group">
	<label maxlength = "100"  class="col-sm-2 control-label">NOMBRE<span style="color:red">*</span></label>
	<div class="col-sm-4">
	<input type="text" name="name" class="form-control" required value="<?php echo htmlentities(($result==NULL)?'':$result['NOMBRE']);?>">
	</div>

	<label maxlength = "500" class="col-sm-2 control-label">DESCRIPCIÓN<span style="color:red">*</span></label>
	<div class="col-sm-4">
	<input type="text" name="DESCRIPCION" class="form-control" required value="<?php echo htmlentities(($result==NULL)?'':$result['DESCRIPCION']);?>">
	</div>
</div>

<div class="form-group">

<label class="col-sm-2 control-label">IdAlumno_1(binario)</label>
<div class="col-sm-4">
<input readonly="readonly" type="text" name="integranteaa" class="form-control" value="<?php echo getAsteriscos(decbin(getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['ID']))?>">

</div>
	
	<div class="col-sm-6"><label readonly="readonly" class="form-control"><?php echo getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['NOMBRE']." ".getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['APELLIDO1'];?></label></div>
<?php 
$maxNumAlumClan = getConfGeneral($dbh, "NUMERO_ALUMNOS_CLAN");
//echo 'clanid:'.$result['ID'];
if (isset($result))
{
	$aIntegrantes = getAlumnosClan($dbh,$result['ID']);
}
else
{
	$aIntegrantes = array();
}

//echo 'aIntegrantes:';
//var_export($aIntegrantes);
$contInte = 0;
for ($i=2; $i <=$maxNumAlumClan ; $i++) 
{ 
	$valueIntegrante="";
	if ($contInte<Count($aIntegrantes))
	{
		if ($aIntegrantes[$contInte]['ID_ALUMNO']==getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['ID'])
		{
			$contInte++;
		}
		if ($contInte<Count($aIntegrantes))
		{
			$valueIntegrante=$aIntegrantes[$contInte]['ID_ALUMNO'];
			$contInte++;
		}
	}
?>

	<label class="col-sm-2 control-label">IdAlumno_<?php echo $i?>(binario)</label>
	<div class="col-sm-4">
	<input type="text" name="integrante<?php echo $i?>" class="form-control" value="<?php echo ($valueIntegrante=="")?"":decbin($valueIntegrante);?>">
	</div>
		<div class="col-sm-6"><label readonly="readonly" class="form-control"><?php echo ($valueIntegrante=="")?"":(getAlumnoFromId($dbh,$valueIntegrante)['NOMBRE']." ".getAlumnoFromId($dbh,$valueIntegrante)['APELLIDO1'])	;?></label></div>
<?php
}
?>

</div>
<input type="hidden" name="editID" class="form-control" required value="<?php echo htmlentities(($result==NULL)?'':$result['ID']);?>">
<input type="hidden" name="integrante1" class="form-control" required value="<?php echo decbin(getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['ID']);?>">



<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2">
		<button class="btn btn-primary" name="submit" type="submit">Guardar cambios</button>
	</div>
</div>

</form>
<form method="post" class="form-horizontal" onsubmit="return validateForm3()">

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