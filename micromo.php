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
//var_dump($_POST);
if(isset($_POST['submit']))
{	
	
	if (isset($_FILES['image']))
	{
		$file = $_FILES['image']['name'];
		$file_loc = $_FILES['image']['tmp_name'];
		$folder="imagesCromos/";
		$new_file_name = strtolower($file);
		$final_file=str_replace(' ','-',$new_file_name);
		$alumno1 = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);
		$name1=$alumno1['NOMBRE'];
		$APELLIDO1no=$alumno1['APELLIDO1'];
		$APELLIDO2=$alumno1['APELLIDO2'];
		$final_file=$name1.$APELLIDO1no.$APELLIDO2.$final_file;
    	$final_file=remove_accents($final_file);
		$image="";
		if(move_uploaded_file($file_loc,$folder.$final_file))
		{
			$image=$final_file;
		}
		$_POST['image']=$image;
	}
	modificarCromo($dbh,$_SESSION['alogin'],
		(!isset($_POST['nombre']))?"":$_POST['nombre'],
		(!isset($_POST['color']))?"White":$_POST['color'],
		(!isset($_POST['nestrellas']))?"":$_POST['nestrellas'],
		(!isset($_POST['atributo']))?"":$_POST['atributo'],
		'Common',
		(!isset($_POST['descripcion']))?"":$_POST['descripcion'],
		(!isset($_POST['artista']))?"":$_POST['artista'],
		(!isset($_POST['firma']))?$_POST['firmaini']:$_POST['firma'],
		(!isset($_POST['image']))?"":$_POST['image']);
	$msg=" Información actualizada correctamente";
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
	
	<title>Editar Mi cromo</title>

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
<?php
		$CORREO = $_SESSION['alogin'];

		$cromo = getCromo($dbh,$CORREO);


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
									<div class="panel-heading">MI CROMO</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }

function url(){
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}

				?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data">

<input type="hidden" name="nestrellas" class="form-control" value="<?php echo htmlentities($cromo['mana_w']);?>">
<input type="hidden" name="firmaini" class="form-control" value="<?php echo htmlentities($cromo['bottom']);?>">

<div class="form-group">
	<div class="col-sm-4">
	</div>
<div class="col-sm-4 text-center">
		<img src="https://www.mtgcardmaker.com/mcmaker/createcard.php?name=<?php echo $cromo['name'];?>&color=<?php echo $cromo['color'];?>&mana_w=<?php echo $cromo['mana_w'];?>&picture=<?php echo htmlentities(substr(url(),0,strrpos(url(), '/')).'/imagesCromos/'.$cromo['picture'])?>&cardtype=<?php echo 
		(($cromo['cardtype']!='')?(
		((getValorAtributo($dbh,$CORREO)>=0)?'%2B':'').getValorAtributo($dbh,$CORREO).'  '
		):'').$cromo['cardtype'];?>&rarity=<?php echo $cromo['rarity'];?>&cardtext=<?php echo $cromo['cardtext'];?>&power=&toughness=<?php echo $cromo['toughness'];?>&artist=<?php echo $cromo['artist'];?>&bottom=<?php echo $cromo['bottom'];?>" style="width:250px; border-radius:5%; margin:10px;">
	</div>
	<div class="col-sm-4">
	</div>
</div>
<?php
$getPropsAlummo =  getPropsVisiblesCromo($dbh,$_SESSION['alogin']);
//var_dump($getPropsAlummo);

?>
<div class="form-group">
	<?php if ($getPropsAlummo['nombre']==1) {?>
	<label class="col-sm-2 control-label">Nombre</label>
	<div class="col-sm-4">
	<input type="text" maxlength = "20" name="nombre" class="form-control"  value="<?php echo htmlentities($cromo['name']);?>">
	</div>
	<?php } if ($getPropsAlummo['artista']==1) {?>
	<label class="col-sm-2 control-label">Artista</label>
	<div class="col-sm-4">
	<input type="text" name="artista" maxlength = "40" class="form-control"  value="<?php echo htmlentities($cromo['artist']);?>">
	</div>
	<?php } ?>
</div>

<div class="form-group">
	<?php if ($getPropsAlummo['atributo']==1) {?>
	<label class="col-sm-2 control-label">Atributo<span style="font-size: 200%;"><?php echo ((($cromo['cardtype']!=''))?(" (".getValorAtributo($dbh,$CORREO).")"):"")?></span></label>
	<div class="col-sm-4">
	<input type="text" maxlength = "20" name="atributo" class="form-control"  value="<?php echo htmlentities($cromo['cardtype']);?>">
	</div>
	<?php } if ($getPropsAlummo['descripcion']==1) {?>
	<label class="col-sm-2 control-label">Descripción</label>
	<div class="col-sm-4">
	<input type="text" name="descripcion" maxlength = "299" class="form-control"  value="<?php echo htmlentities($cromo['cardtext']);?>">
	</div>
	<?php } ?>
</div>	
<div class="form-group">
	<?php if ($getPropsAlummo['color']==1) {?>
	<label class="col-sm-2 control-label">Color</label>
	<div class="col-sm-4">
	<!--input type="text" name="color" class="form-control"  value="<?php echo htmlentities($cromo['color']);?>"-->
	<select name="color" class="form-control">
<option value="White" <?php echo (($cromo['color']=="White")?" selected='selected' ":"")?>>
Blanco
</option>

<option value="Blue"<?php echo (($cromo['color']=="Blue")?" selected='selected' ":"")?>>
Azul
</option>

<option value="Black"<?php echo (($cromo['color']=="Black")?" selected='selected' ":"")?>>
Negro
</option>

<option value="Red"<?php echo (($cromo['color']=="Red")?" selected='selected' ":"")?>>
Rojo
</option>

<option value="Green"<?php echo (($cromo['color']=="Green")?" selected='selected' ":"")?>>
Verde
</option>

<option value="Gold"<?php echo (($cromo['color']=="Gold")?" selected='selected' ":"")?>>
Oro
</option>

</select>
	</div>
	<?php } if ($getPropsAlummo['firma']==1) {?>
	<label class="col-sm-2 control-label">Firma</label>
	<div class="col-sm-4">
	<input type="text" maxlength = "50" name="firma" class="form-control"  value="<?php echo htmlentities($cromo['bottom']);?>">
	</div>
	<?php } ?>
</div>
<!--div class="form-group">
	<label class="col-sm-2 control-label">Orden</label>
	<div class="col-sm-4">
	<input type="text" readonly="readonly" name="orden" class="form-control"  value="<?php echo htmlentities($cromo['power']);?>">
	</div>
	<label class="col-sm-2 control-label">Nº Cromos totales</label>
	<div class="col-sm-4">
	<input type="text" readonly="readonly" name="ncrto" class="form-control"  value="<?php echo htmlentities($cromo['toughness']);?>">
	</div>
</div-->


<div class="form-group">
	<?php if ($getPropsAlummo['imagen']==1) {?>
	<label class="col-sm-2 control-label">imagen<span style="color:red"></span></label>
	<div class="col-sm-4 text-center">
		<img src="<?php echo htmlentities(substr(url(),0,strrpos(url(), '/')).'/imagesCromos/'.$cromo['picture'])?>" style="width:200px;height: 100px; border-radius:3%; margin:10px;">
		<input type="file" name="image" class="form-control">
		<input type="hidden" name="image" class="form-control" value="<?php echo htmlentities($cromo['picture']);?>">
	</div>
	<?php }?>
	<div class="col-sm-4 col-sm-offset-2">
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