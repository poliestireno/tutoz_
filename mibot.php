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
	modificarBot($dbh,$_SESSION['alogin'],
		(!isset($_POST['saludo']))?"":$_POST['saludo'],
		(!isset($_POST['palabra_clave']))?"":$_POST['palabra_clave'],
		(!isset($_POST['movilidad']))?"1":$_POST['movilidad'],
		(!isset($_POST['velocidad']))?"":$_POST['velocidad'],
		(!isset($_POST['localizacion']))?"":$_POST['localizacion'],
		(!isset($_POST['checkFantasma']))?"0":"1",
		(!isset($_POST['checkSaltando']))?"0":"1",
		(!isset($_POST['personaje']))?"0":$_POST['personaje']
	);
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
	
	<title>Editar Mi bot</title>

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
.funkyradio div {
  clear: both;
  overflow: hidden;
}

.funkyradio label {
  width: 100%;
  border-radius: 3px;
  border: 1px solid #D1D3D4;
  font-weight: normal;
}

.funkyradio input[type="radio"]:empty,
.funkyradio input[type="checkbox"]:empty {
  display: none;
}

.funkyradio input[type="radio"]:empty ~ label,
.funkyradio input[type="checkbox"]:empty ~ label {
  position: relative;
  line-height: 2.5em;
  text-indent: 3.25em;
  margin-top: 2em;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
}

.funkyradio input[type="radio"]:empty ~ label:before,
.funkyradio input[type="checkbox"]:empty ~ label:before {
  position: absolute;
  display: block;
  top: 0;
  bottom: 0;
  left: 0;
  content: '';
  width: 2.5em;
  background: #D1D3D4;
  border-radius: 3px 0 0 3px;
}

.funkyradio input[type="radio"]:hover:not(:checked) ~ label,
.funkyradio input[type="checkbox"]:hover:not(:checked) ~ label {
  color: #888;
}

.funkyradio input[type="radio"]:hover:not(:checked) ~ label:before,
.funkyradio input[type="checkbox"]:hover:not(:checked) ~ label:before {
  content: '\2714';
  text-indent: .9em;
  color: #C2C2C2;
}

.funkyradio input[type="radio"]:checked ~ label,
.funkyradio input[type="checkbox"]:checked ~ label {
  color: #777;
}

.funkyradio input[type="radio"]:checked ~ label:before,
.funkyradio input[type="checkbox"]:checked ~ label:before {
  content: '\2714';
  text-indent: .9em;
  color: #333;
  background-color: #ccc;
}

.funkyradio input[type="radio"]:focus ~ label:before,
.funkyradio input[type="checkbox"]:focus ~ label:before {
  box-shadow: 0 0 0 3px #999;
}

.funkyradio-default input[type="radio"]:checked ~ label:before,
.funkyradio-default input[type="checkbox"]:checked ~ label:before {
  color: #333;
  background-color: #ccc;
}

.funkyradio-primary input[type="radio"]:checked ~ label:before,
.funkyradio-primary input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #337ab7;
}

.funkyradio-success input[type="radio"]:checked ~ label:before,
.funkyradio-success input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #5cb85c;
}

.funkyradio-danger input[type="radio"]:checked ~ label:before,
.funkyradio-danger input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #d9534f;
}

.funkyradio-warning input[type="radio"]:checked ~ label:before,
.funkyradio-warning input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #f0ad4e;
}

.funkyradio-info input[type="radio"]:checked ~ label:before,
.funkyradio-info input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #5bc0de;
}
		</style>


</head>

<body>
<?php
		$CORREO = $_SESSION['alogin'];

		$bot = getBot($dbh,$CORREO);
		$alumnoo = getAlumnoFromCorreo($dbh,$CORREO);
		//$bot['SALUDO']="HALLO";
		//$bot['PALABRA_CLAVE']="NAJAS";

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
									<div class="panel-heading">NANOPROGRAMACIÓN DE TU BOT</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data">


<?php
//$getPropsAlummo =  getPropsVisiblesbot($dbh,$_SESSION['alogin']);
$getPropsAlummo['saludo']=1;
$getPropsAlummo['palabra_clave']=1;
$getPropsAlummo['movilidad']=1;
$getPropsAlummo['velocidad']=1;
$getPropsAlummo['saltando']=1;
$getPropsAlummo['fantasma']=1;
$getPropsAlummo['localizacion']=1;
$getPropsAlummo['personajes']=1;
?>
<div class="form-group">
	<?php if ($getPropsAlummo['saludo']==1) {?>
	<label class="col-sm-2 control-label">Saludo</label>
	<div class="col-sm-4">
	<input type="text" maxlength = "50" name="saludo" class="form-control"  value="<?php echo htmlentities($bot['SALUDO']);?>">
	</div>
	<?php } if ($getPropsAlummo['palabra_clave']==1) {?>
	<label class="col-sm-2 control-label">Palabra Clave</label>
	<div class="col-sm-4">
	<input type="text" name="palabra_clave" maxlength = "50" class="form-control"  value="<?php echo htmlentities($bot['PALABRA_CLAVE']);?>">
	</div>
	<?php } ?>
</div>
<div class="form-group">
	<?php if ($getPropsAlummo['movilidad']==1) {?>
	<label class="col-sm-2 control-label">Modo Movilidad</label>
	<div class="col-sm-4">
	<select name="movilidad" class="form-control">
		<option value="0" <?php echo (($bot['MOVILIDAD']=="0")?" selected='selected' ":"")?>>
		Parado
		</option>
		<option value="1" <?php echo (($bot['MOVILIDAD']=="1")?" selected='selected' ":"")?>>
		Aleatorio
		</option>
		<option value="2" <?php echo (($bot['MOVILIDAD']=="2")?" selected='selected' ":"")?>>
		Zombie
		</option>
		<option value="3" <?php echo (($bot['MOVILIDAD']=="3")?" selected='selected' ":"")?>>
		Miedo
		</option>
	</select>
	</div><?php } ?>
	<?php if ($getPropsAlummo['velocidad']==1) {?>
	<label class="col-sm-2 control-label">Velocidad</label>
	<div class="col-sm-4">
	<select name="velocidad" class="form-control">
		<option value="1" <?php echo (($bot['VELOCIDAD']=="1")?" selected='selected' ":"")?>>
		Caracol
		</option>
		<option value="2" <?php echo (($bot['VELOCIDAD']=="2")?" selected='selected' ":"")?>>
		Tortuga
		</option>
		<option value="3" <?php echo (($bot['VELOCIDAD']=="3")?" selected='selected' ":"")?>>
		Burro
		</option>
		<option value="4" <?php echo (($bot['VELOCIDAD']=="4")?" selected='selected' ":"")?>>
		Humano
		</option>
		<option value="5" <?php echo (($bot['VELOCIDAD']=="5")?" selected='selected' ":"")?>>
		Rádido
		</option>
		<option value="6" <?php echo (($bot['VELOCIDAD']=="6")?" selected='selected' ":"")?>>
		Rapidiiisimo
		</option>
	</select>
	</div><?php } ?>
</div>

<div class="form-group">
	<?php if ($getPropsAlummo['localizacion']==1) {?>
	<label class="col-sm-2 control-label">Localización</label>
	<div class="col-sm-4">
	<select name="localizacion" class="form-control">
		<option value="9" <?php echo (($bot['ID_MAPA']=="9")?" selected='selected' ":"")?>>
		Patio
		</option>
		<option value="5" <?php echo (($bot['ID_MAPA']=="5")?" selected='selected' ":"")?>>
		Pasillo principal
		</option>
	<option value="4" <?php echo (($bot['ID_MAPA']=="4")?" selected='selected' ":"")?>>
		Clase puerta abajo 2ºB 
		</option>
	</select>
	</div><?php } ?>
	<?php if ($getPropsAlummo['velocidad']==1) {?><?php } ?>
</div>
<div class="form-group">
	<?php if ($getPropsAlummo['saltando']==1) {?>
	<label class="col-sm-2 control-label"></label>
		
		<div class="col-sm-4 funkyradio">
			<div class="funkyradio-success">
	            <input type="checkbox" name="checkSaltando" id="radio1" <?php echo (($bot['SALTANDO']=="1")?" checked='checked' ":"")?>/>
	            <label for="radio1">Ir saltando</label>
	        	</div>
    	</div>


	<?php } if ($getPropsAlummo['fantasma']==1) {?>
		<label class="col-sm-2 control-label"></label>
			<div class="col-sm-4 funkyradio">
			<div class="funkyradio-success">
	            <input type="checkbox" name="checkFantasma" id="radio12" <?php echo (($bot['FANTASMA']=="1")?" checked='checked' ":"")?>/>
	            <label for="radio12">Modo fantasma</label>
	        	</div>
    	</div>
	<?php } ?>
</div>
<div class="form-group">
	<?php if ($getPropsAlummo['personajes']==1) {?>
	<label class="col-sm-2 control-label">Personajes disponibles</label>
	<div class="col-sm-4">	
	<?php 
	if ($alumnoo['gender']=='f')
		echo '<img src="img/chicasReferencia01.png">';		
	else 	
		echo '<img src="img/chicosReferencia01.png">';
	?>									
	</div><?php } ?>
	<?php if ($getPropsAlummo['personajes']==1) {?>
	<label class="col-sm-2 control-label">Elegir personaje</label>
	<div class="col-sm-4">
	<select name="personaje" class="form-control">
		<option value="0" <?php echo (($bot['PERSONAJE']=="0")?" selected='selected' ":"")?>>
		</option>		
		<option value="1" <?php echo (($bot['PERSONAJE']=="1")?" selected='selected' ":"")?>>
		1
		</option>
		<option value="2" <?php echo (($bot['PERSONAJE']=="2")?" selected='selected' ":"")?>>
		2
		</option>
		<option value="3" <?php echo (($bot['PERSONAJE']=="3")?" selected='selected' ":"")?>>
		3
		</option>
		<option value="4" <?php echo (($bot['PERSONAJE']=="4")?" selected='selected' ":"")?>>
		4
		</option>
		<option value="5" <?php echo (($bot['PERSONAJE']=="5")?" selected='selected' ":"")?>>
		5
		</option>
		<option value="6" <?php echo (($bot['PERSONAJE']=="6")?" selected='selected' ":"")?>>
		6
		</option>
		<option value="7" <?php echo (($bot['PERSONAJE']=="7")?" selected='selected' ":"")?>>
		7
		</option>
		<option value="8" <?php echo (($bot['PERSONAJE']=="8")?" selected='selected' ":"")?>>
		8
		</option>
		<option value="9" <?php echo (($bot['PERSONAJE']=="9")?" selected='selected' ":"")?>>
		9
		</option>
		<option value="10" <?php echo (($bot['PERSONAJE']=="10")?" selected='selected' ":"")?>>
		10
		</option>
	</select>
	</div><?php } ?>
</div>



	<div class="col-sm-4 col-sm-offset-2">
		<button class="btn btn-primary" name="submit" type="submit">Guardar cambios</button>
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