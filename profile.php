<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
$msg=0;
$msgError=0;
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{	
	header('location:index.php');
}
else{
	
if(isset($_POST['submit']))
  {	
	$name=$_POST['name'];
	$APELLIDO1no=$_POST['APELLIDO1'];
	$APELLIDO2=$_POST['APELLIDO2'];
    $IDedit=$_POST['editID'];
	$image=$_POST['image'];
	
	// Si el valor de la ubicacion de la imagen es vacio, se asigna la imagen que esta en el servidor por defecto.
	if (empty($file_loc)) {
		$file_loc="anonimous_profile.jpg";
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
		// Comprueba el valor de $_FILES['image']['error'].
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
	
		// ¡¡¡No confíes en el valor de $_FILES['image']['mime']!!!
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
		// No cambiar a $_FILES['image']['name'] dado a que perderia completamente la seguridad de la subida de archivos.
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
	// Medida de seguridad extra para evitar futuros ataques en cuanto a la escalabilidad de la plataforma.
	// Funcion que comprueba si una cadena contiene un simbolo especial.
	function validar_inputs($input_nombre) {
		return preg_match("/^[a-zA-Z0-9]+$/", $input_nombre);
	}
	// Si el nombre de usuario no contiene un simbolo especial, se sube a la base de datos.
	if (validar_inputs($name) && validar_inputs($APELLIDO1no) && validar_inputs($APELLIDO2)) {

		$sql="UPDATE ALUMNOS SET NOMBRE=(:name), APELLIDO1=(:APELLIDO1no), APELLIDO2=(:APELLIDO2), Image=(:image) WHERE ID=(:IDedit)";
		$query = $dbh->prepare($sql);
		$query-> bindParam(':name', $name, PDO::PARAM_STR);
		$query-> bindParam(':APELLIDO1no', $APELLIDO1no, PDO::PARAM_STR);
		$query-> bindParam(':APELLIDO2', $APELLIDO2, PDO::PARAM_STR);
		$query-> bindParam(':image', $image, PDO::PARAM_STR);
		$query-> bindParam(':IDedit', $IDedit, PDO::PARAM_STR);
		$query->execute();
		$msg="Información actualizada correctamente.";
   }

	// if(preg_match("/([.%\$#\*]+)/", $name)|| preg_match("/([.%\$#\*]+)/", $APELLIDO1) || preg_match("/([.%\$#\*]+)/", $APELLIDO2))
	// {
	// 	$msgError="El nombre y apellidos no pueden contener caracteres especiales.";
	// }	
	// Si el nombre o apellidos usa un simbolo especial, rechaza la request.
	else {
	$msgError="El Nombre y Apellidos no pueden contener carácteres especiales.";
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
	
	<title>Editar Perfil</title>

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
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

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
.msgError{
    padding: 10px;
    margin: 0 0 20px 0;
	background: red;
	color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
		</style>


</head>

<body onload="showNotis()">
<?php
		$CORREO = $_SESSION['alogin'];
		$sql = "SELECT * from ALUMNOS where CORREO = (:CORREO);";
		$query = $dbh -> prepare($sql);
		$query-> bindParam(':CORREO', $CORREO, PDO::PARAM_STR);
		$query->execute();
		$result=$query->fetch(PDO::FETCH_OBJ);
		$cnt=1;	

$fi = new FilesystemIterator("img/comics", FilesystemIterator::SKIP_DOTS);
$numeroComics = iterator_count($fi)-1;
//echo "comic:".$numeroComics;
$nDayOfYear = date('z') + 1;
//echo "nDayOfYear:".$nDayOfYear;
$nombreComic = $nDayOfYear % $numeroComics;
//echo "nombreComic:".$nombreComic;

$randomColorB4 = array("primary","secondary","success","danger","warning","info","dark")[rand(0,6)];
?>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>

		<div class="content-wrapper">
			<div class="container-fluid">
				<div class='alert alert-<?php echo $randomColorB4?> alert-dismissible'>
  <button type="button" class="close" data-dismiss="alert">&times;</button> 
  <img align = "center" class="img-fluid mx-auto d-block" 
  src="img/comics/<?php echo $nombreComic ?>.gif". alt="Chania">
</div>
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">

									<div class="panel-heading">Perfil</div>

<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>
<?php if($msgError){?><div class="msgError"><strong>ERROR: </strong><?php echo htmlentities($msgError); ?> </div><?php }?>
									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data">

<div class="form-group">
	<div class="col-sm-4">
	</div>
	<div class="col-sm-4 text-center">
		<img src="images/<?php 
		$dbImage = htmlentities($result->image);
		if (($dbImage!="")&&(file_exists("images/".$dbImage)))
		{   
			echo $dbImage;
		}
		else
		{
			echo "anonimous_profile.jpg";
		}
		?>" style="width:200px; border-radius:50%; margin:10px;">
		<input type="file" name="uploadimage" class="form-control">
		<input type="hidden" name="image" class="form-control" value="<?php echo htmlentities($result->image);?>">
	</div>
	<div class="col-sm-4">
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label">NOMBRE<span style="color:red">*</span></label>
	<div class="col-sm-4">
	<input type="text" name="name" class="form-control" required value="<?php echo htmlentities($result->NOMBRE);?>">
	</div>

	<label class="col-sm-2 control-label">APELLIDO1<span style="color:red">*</span></label>
	<div class="col-sm-4">
	<input type="text" name="APELLIDO1" class="form-control" required value="<?php echo htmlentities($result->APELLIDO1);?>">
	</div>
</div>

<div class="form-group">


	<label class="col-sm-2 control-label">APELLIDO2<span style="color:red">*</span></label>
	<div class="col-sm-4">
	<input type="text" name="APELLIDO2" class="form-control" required value="<?php echo htmlentities($result->APELLIDO2);?>">
	</div>
	<label class="col-sm-2 control-label">CURSO<span style="color:red"></span></label>
	<div class="col-sm-4">
	<input type="text" name="Curso" class="form-control" readonly="readonly" value="<?php echo htmlentities(getNombreCursoFromID($dbh,$result->ID_CURSO));?>">
	</div>


</div>
<input type="hidden" name="editID" class="form-control" required value="<?php echo htmlentities($result->ID);?>">

<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2">
		<button class="btn btn-primary" name="submit" type="submit">Guardar cambios</button>
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
	</script>
</body>
</html>
<?php } ?>