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
var_export($CORREO);
$idAlumno = getAlumnoFromCorreo($dbh,$CORREO)['ID'];

if (isset($_GET['idc']))
{
  $idAsignatura=getAsignaturasFromCurso($dbh,$_GET['idc'])[0]['ID'];
}
else
{
$idAsignatura = getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$CORREO)['ID_CURSO'])[0]['ID'];	
}
$aAlumnosAsig = getAlumnosFromAsignaturaID($dbh,$idAsignatura);
function getAsteriscos($n)
{
	$sAsteriscos = "";
	for ($i=0; $i < $n ; $i++) 
	{ 
		$sAsteriscos = $sAsteriscos ."*";
	}
	return $sAsteriscos;
}
if(isset($_POST['submitOfrezco']))
{	

	borrarOfrezcoMercado($dbh,$idAsignatura,$idAlumno);
	if ($_POST['sCromoOfrezco1']!="")
	{
		insertarOfrezcoMercado($dbh,$idAsignatura,$idAlumno,$_POST['sCromoOfrezco1']);
	}
	if ($_POST['sCromoOfrezco2']!="")
	{
		insertarOfrezcoMercado($dbh,$idAsignatura,$idAlumno,$_POST['sCromoOfrezco2']);
	}
	if ($_POST['sCromoOfrezco3']!="")
	{
		insertarOfrezcoMercado($dbh,$idAsignatura,$idAlumno,$_POST['sCromoOfrezco3']);
	}
	$msg=" Sección OFREZCO actualizada correctamente";	
}

if(isset($_POST['submitQuiero']))
{

	borrarQuieroMercado($dbh,$idAsignatura,$idAlumno);
	if (($_POST['sCromoQuiero11']!="")||($_POST['sCromoQuiero12']!="")||($_POST['sCromoQuiero13']!=""))
	{
		insertarQuieroMercado($dbh,$idAsignatura,$idAlumno,$_POST['sCromoQuiero11'],$_POST['sCromoQuiero12'],$_POST['sCromoQuiero13']);
	}
	if (($_POST['sCromoQuiero21']!="")||($_POST['sCromoQuiero22']!="")||($_POST['sCromoQuiero23']!=""))
	{
		insertarQuieroMercado($dbh,$idAsignatura,$idAlumno,$_POST['sCromoQuiero21'],$_POST['sCromoQuiero22'],$_POST['sCromoQuiero23']);
	}
	if (($_POST['sCromoQuiero31']!="")||($_POST['sCromoQuiero32']!="")||($_POST['sCromoQuiero33']!=""))
	{
		insertarQuieroMercado($dbh,$idAsignatura,$idAlumno,$_POST['sCromoQuiero31'],$_POST['sCromoQuiero32'],$_POST['sCromoQuiero33']);
	}



	$msg2=" Sección QUIERO actualizada correctamente";	
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
	
	<title>Editar Mi mercado</title>

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
if (!isset($_GET['idc']))
{
	?>
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">MI MERCADO: OFREZCO LOS SIGUIENTES CROMOS</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

<div class="panel-body">
<form id="form1" method="post" class="form-horizontal" enctype="multipart/form-data">

<?php

$aOfrezcoMercado = getOfrezcoMercado($dbh,$idAsignatura,$idAlumno);



?>



<div class="form-group">
	
	<label class="col-sm-2 control-label">CROMO1:</label>
	<div class="col-sm-4">
	  <select class="form-control col-md-2" ID="sCromoOfrezco1" name="sCromoOfrezco1">
	  	<option></option>
	<?php
		$aCromosAlumno1 = getCromosDeAlbum($dbh,$CORREO);
		$idCromoSeleccionado = ((isset($aOfrezcoMercado[0]))?$aOfrezcoMercado[0]['OFREZCO_ID_CROMO']:"-1");
	    foreach ($aCromosAlumno1 as $cromoI) 
	    {
	echo "<option ".(($cromoI['ID']==$idCromoSeleccionado)?" selected ":"")."value='".$cromoI['ID']."'>".$cromoI['name']." | ".$cromoI['power']." | ".getAsteriscos($cromoI['mana_w'])."</option>";
	    }

	?>
	  </select>
    </div>
	
	<label class="col-sm-2 control-label">CROMO2:</label>
	<div class="col-sm-4">
	  <select class="form-control col-md-2" ID="sCromoOfrezco2" name="sCromoOfrezco2">
	  	<option></option>
	<?php
		$aCromosAlumno1 = getCromosDeAlbum($dbh,$CORREO);
		$idCromoSeleccionado = ((isset($aOfrezcoMercado[1]))?$aOfrezcoMercado[1]['OFREZCO_ID_CROMO']:"-1");
	    foreach ($aCromosAlumno1 as $cromoI) 
	    {
	echo "<option ".(($cromoI['ID']==$idCromoSeleccionado)?" selected ":"")."value='".$cromoI['ID']."'>".$cromoI['name']." | ".$cromoI['power']." | ".getAsteriscos($cromoI['mana_w'])."</option>";
	    }

	?>
	  </select>
    </div>

</div>
<div class="form-group">
	
	<label class="col-sm-2 control-label">CROMO3:</label>
	<div class="col-sm-4">
	  <select class="form-control col-md-2" ID="sCromoOfrezco3" name="sCromoOfrezco3">
	  	<option></option>
	<?php
		$aCromosAlumno1 = getCromosDeAlbum($dbh,$CORREO);
		$idCromoSeleccionado = ((isset($aOfrezcoMercado[2]))?$aOfrezcoMercado[2]['OFREZCO_ID_CROMO']:"-1");
	    foreach ($aCromosAlumno1 as $cromoI) 
	    {
	echo "<option ".(($cromoI['ID']==$idCromoSeleccionado)?" selected ":"")."value='".$cromoI['ID']."'>".$cromoI['name']." | ".$cromoI['power']." | ".getAsteriscos($cromoI['mana_w'])."</option>";
	    }

	?>
	  </select>
    </div>
    <label class="col-sm-6 control-label">Signatura cromo: NOMBRE | REFERENCIA | Nº ESTRELLAS</label>
</div>
	<div class="col-sm-4 col-sm-offset-2">
		<button class="btn btn-primary" name="submitOfrezco" onclick="manageSubmitOfrezco()" >Guardar sección OFREZCO</button>

	</div>
</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">MI MERCADO: QUIERO CROMOS CON LAS SIGUIENTES CARACTERISTICAS</div>
<?php if($msg2){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg2); ?> </div><?php }?>

<div class="panel-body">
<form id="form2" method="post" class="form-horizontal" enctype="multipart/form-data">

<?php

$aQuieroMercado = getQuieroMercado($dbh,$idAsignatura,$idAlumno)

?>



<div class="form-group">
	
	<label class="col-sm-1 control-label" style="font-size: 20px">CROMO1:</label>
</div>
<div class="form-group">
	
	<label class="col-sm-2 control-label">Nombre:</label>
	<div class="col-sm-3">
	  <select class="form-control col-md-2" ID="sCromoQuiero11" name="sCromoQuiero11">
	  	<option></option>
	<?php
		$aCromosAlumno1 = getCromosDeAlbum($dbh,$CORREO);
		
		$idCromoSeleccionado = ((isset($aQuieroMercado[0]))?$aQuieroMercado[0]['QUIERO_NOMBRE_CROMO_ID']:"-1");
	    foreach ($aAlumnosAsig as $alumno) 
	    {
	    	$cromoI = getCromo($dbh,$alumno['CORREO']);
	    	if ($cromoI['name']!="")
	    	{
	echo "<option ".(($cromoI['ID']==$idCromoSeleccionado)?" selected ":"")."value='".$cromoI['ID']."'>".$cromoI['name']."</option>"; 		
	    	}
	    }

	?>
	  </select>
    </div>
	
	<label class="col-sm-1 control-label">Referencia:</label>
	<div class="col-sm-2">
	  <select class="form-control col-md-1" ID="sCromoQuiero12" name="sCromoQuiero12">
	  	<option></option>
	<?php

$maximaReferencia = getCromo($dbh,$CORREO)['toughness'];
$idSeleccionado = ((isset($aQuieroMercado[0]))?$aQuieroMercado[0]['QUIERO_REF_CROMO']:"-1");
	    
for ($i=1; $i < $maximaReferencia+1; $i++) 
{ 
	echo "<option ".(($i==$idSeleccionado)?" selected ":"")."value='".$i."'>".$i."</option>";
}

	?>
	  </select>
    </div>
	<label class="col-sm-1 control-label">Estrellas:</label>
	<div class="col-sm-2">
<?php
$idSeleccionado = ((isset($aQuieroMercado[0]))?$aQuieroMercado[0]['QUIERO_ESTRELLAS_CROMO']:"-1");
?>
	  <select class="form-control col-md-1" ID="sCromoQuiero13" name="sCromoQuiero13">
	  	<option></option>
	  	<option <?php echo (("1"==$idSeleccionado)?" selected ":"")?> value="1" >*</option>
	  	<option <?php echo (("2"==$idSeleccionado)?" selected ":"")?> value="2" >**</option>
	  	<option <?php echo (("3"==$idSeleccionado)?" selected ":"")?> value="3" >***</option>
	  	<option <?php echo (("4"==$idSeleccionado)?" selected ":"")?> value="4" >****</option>
	  	<option <?php echo (("5"==$idSeleccionado)?" selected ":"")?> value="5" >*****</option>
	  	<option <?php echo (("6"==$idSeleccionado)?" selected ":"")?> value="6" >******</option>	
	  </select>
    </div>
</div>
<div class="form-group">
	
	<label class="col-sm-1 control-label" style="font-size: 20px">CROMO2:</label>
</div>
<div class="form-group">
	
	<label class="col-sm-2 control-label">Nombre:</label>
	<div class="col-sm-3">
	  <select class="form-control col-md-2" ID="sCromoQuiero21" name="sCromoQuiero21">
	  	<option></option>
	<?php
		$aCromosAlumno1 = getCromosDeAlbum($dbh,$CORREO);
		$idCromoSeleccionado = ((isset($aQuieroMercado[1]))?$aQuieroMercado[1]['QUIERO_NOMBRE_CROMO_ID']:"-1");
	    foreach ($aAlumnosAsig as $alumno) 
	    {
	    	$cromoI = getCromo($dbh,$alumno['CORREO']);
	    	if ($cromoI['name']!="")
	    	{
	echo "<option ".(($cromoI['ID']==$idCromoSeleccionado)?" selected ":"")."value='".$cromoI['ID']."'>".$cromoI['name']."</option>"; 		
	    	}
	    }

	?>
	  </select>
    </div>
	
	<label class="col-sm-1 control-label">Referencia:</label>
	<div class="col-sm-2">
	  <select class="form-control col-md-1" ID="sCromoQuiero22" name="sCromoQuiero22">
	  	<option></option>
	<?php

$maximaReferencia = getCromo($dbh,$CORREO)['toughness'];
$idSeleccionado = ((isset($aQuieroMercado[1]))?$aQuieroMercado[1]['QUIERO_REF_CROMO']:"-1");
for ($i=1; $i < $maximaReferencia+1; $i++) 
{ 
	echo "<option ".(($i==$idSeleccionado)?" selected ":"")."value='".$i."'>".$i."</option>";
}

	?>
	  </select>
    </div>
	<label class="col-sm-1 control-label">Estrellas:</label>
	<div class="col-sm-2">
		<?php
$idSeleccionado = ((isset($aQuieroMercado[1]))?$aQuieroMercado[1]['QUIERO_ESTRELLAS_CROMO']:"-1");
?>

	  <select class="form-control col-md-1" ID="sCromoQuiero23" name="sCromoQuiero23">
	  	<option></option>
	  	<option <?php echo (("1"==$idSeleccionado)?" selected ":"")?> value="1" >*</option>
	  	<option <?php echo (("2"==$idSeleccionado)?" selected ":"")?> value="2" >**</option>
	  	<option <?php echo (("3"==$idSeleccionado)?" selected ":"")?> value="3" >***</option>
	  	<option <?php echo (("4"==$idSeleccionado)?" selected ":"")?> value="4" >****</option>
	  	<option <?php echo (("5"==$idSeleccionado)?" selected ":"")?> value="5" >*****</option>
	  	<option <?php echo (("6"==$idSeleccionado)?" selected ":"")?> value="6" >******</option>
	  </select>
    </div>
</div>

<div class="form-group">
	
	<label class="col-sm-1 control-label" style="font-size: 20px">CROMO3:</label>
</div>
<div class="form-group">
	
	<label class="col-sm-2 control-label">Nombre:</label>
	<div class="col-sm-3">
	  <select class="form-control col-md-2" ID="sCromoQuiero31" name="sCromoQuiero31">
	  	<option></option>
	<?php
		$aCromosAlumno1 = getCromosDeAlbum($dbh,$CORREO);
		$idCromoSeleccionado = ((isset($aQuieroMercado[2]))?$aQuieroMercado[2]['QUIERO_NOMBRE_CROMO_ID']:"-1");
	    foreach ($aAlumnosAsig as $alumno) 
	    {
	    	$cromoI = getCromo($dbh,$alumno['CORREO']);
	    	if ($cromoI['name']!="")
	    	{
	echo "<option ".(($cromoI['ID']==$idCromoSeleccionado)?" selected ":"")."value='".$cromoI['ID']."'>".$cromoI['name']."</option>"; 		
	    	}
	    }

	?>
	  </select>
    </div>
	
	<label class="col-sm-1 control-label">Referencia:</label>
	<div class="col-sm-2">
	  <select class="form-control col-md-1" ID="sCromoQuiero32" name="sCromoQuiero32">
	  	<option></option>
	<?php

$maximaReferencia = getCromo($dbh,$CORREO)['toughness'];
$idSeleccionado = ((isset($aQuieroMercado[2]))?$aQuieroMercado[2]['QUIERO_REF_CROMO']:"-1");
for ($i=1; $i < $maximaReferencia+1; $i++) 
{ 
	echo "<option ".(($i==$idSeleccionado)?" selected ":"")."value='".$i."'>".$i."</option>";
}

	?>
	  </select>
    </div>
	<label class="col-sm-1 control-label">Estrellas:</label>
	<div class="col-sm-2">
		<?php
$idSeleccionado = ((isset($aQuieroMercado[2]))?$aQuieroMercado[2]['QUIERO_ESTRELLAS_CROMO']:"-1");
?>

	  <select class="form-control col-md-1" ID="sCromoQuiero33" name="sCromoQuiero33">
	  	<option></option>
	  	<option <?php echo (("1"==$idSeleccionado)?" selected ":"")?> value="1" >*</option>
	  	<option <?php echo (("2"==$idSeleccionado)?" selected ":"")?> value="2" >**</option>
	  	<option <?php echo (("3"==$idSeleccionado)?" selected ":"")?> value="3" >***</option>
	  	<option <?php echo (("4"==$idSeleccionado)?" selected ":"")?> value="4" >****</option>
	  	<option <?php echo (("5"==$idSeleccionado)?" selected ":"")?> value="5" >*****</option>
	  	<option <?php echo (("6"==$idSeleccionado)?" selected ":"")?> value="6" >******</option>	
	  </select>
    </div>
</div>



	<div class="col-sm-4 col-sm-offset-2">
		<button class="btn btn-primary" name="submitQuiero" onclick="manageSubmitQuiero()" >Guardar sección QUIERO</button>

	</div>
</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

<?php } ?>
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">MERCADO GLOBAL</div>
<?php if($msg2){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg2); ?> </div><?php }?>

<div class="panel-body">



<div class="form-group">
	
<label class="col-sm-6 control-label" style="font-size: 20px">Ofrecen los siguientes cromos</label><br/>
</div>

<?php
$sTexto = "[mercado vacio]";
foreach ($aAlumnosAsig as $alumno) 
{
	$aOfrezcoMercado = getOfrezcoMercado($dbh,$idAsignatura,$alumno['ID']);

	$ofer1 = "";
	$ofer2 = "";
	$ofer3 = "";
	if (isset($aOfrezcoMercado[0]))
	{
		$cromoI = getCromoFromID($dbh,$aOfrezcoMercado[0]['OFREZCO_ID_CROMO']);
	$ofer1 ="CROMO1: ".$cromoI['name']." | ".$cromoI['power']." | ".getAsteriscos($cromoI['mana_w']);
	}
	if (isset($aOfrezcoMercado[1]))
	{
		$cromoI = getCromoFromID($dbh,$aOfrezcoMercado[1]['OFREZCO_ID_CROMO']);
	$ofer2 ="CROMO2: ".$cromoI['name']." | ".$cromoI['power']." | ".getAsteriscos($cromoI['mana_w']);
	}
	if (isset($aOfrezcoMercado[2]))
	{
		$cromoI = getCromoFromID($dbh,$aOfrezcoMercado[2]['OFREZCO_ID_CROMO']);
	$ofer3 ="CROMO3: ".$cromoI['name']." | ".$cromoI['power']." | ".getAsteriscos($cromoI['mana_w']);
	}
if (count($aOfrezcoMercado)>0)
{
	$sTexto = "";
?>

<div class="form-group">
	<label >
		<?php echo $alumno['NOMBRE']." ".$alumno['APELLIDO1']." ".$alumno['APELLIDO2']?></label>

<!--Table-->
<table class="table table-striped w-auto table-bordered">
  <!--Table head-->
  <thead>
    <tr>
      <th><?php echo $ofer1; ?></th>
      <?php if ($ofer2!="") {?>
      		<th><?php echo $ofer2; ?></th>
      		 <?php } if ($ofer3!="") {?>
      <th><?php echo $ofer3; ?></th>
       <?php } ?>
    </tr>
  </thead>
</table>
</div>

<?php
}
}
echo'<label >'.  $sTexto.'</label>';
?>



</div>
<div class="panel-body">



<div class="form-group">
	
<label class="col-sm-8 control-label" style="font-size: 20px">Quieren cromos que cumplan las siguientes características</label><br/>
</div>

<?php 
$sTexto = "[mercado vacio]";
foreach ($aAlumnosAsig as $alumno) 
{
	$aQuieroMercado = getQuieroMercado($dbh,$idAsignatura,$alumno['ID']);

	$demanda1 = "";
	$demanda2 = "";
	$demanda3 = "";
	if (isset($aQuieroMercado[0]))
	{		
		$nomb ="";
		$refe ="";
		$nEstre ="";
		$comma = "";
		if ($aQuieroMercado[0]['QUIERO_NOMBRE_CROMO_ID']!="")
		{
			$cromoI = getCromoFromID($dbh,$aQuieroMercado[0]['QUIERO_NOMBRE_CROMO_ID']);
			$nomb ="Nombre:<strong>".$cromoI['name']."</strong>";
			$comma = ",";
		}
		if ($aQuieroMercado[0]['QUIERO_REF_CROMO']!="")
		{
		$refe =$comma." Referencia:<strong>".$aQuieroMercado[0]['QUIERO_REF_CROMO']."</strong>";
			$comma = ",";
		}
		if ($aQuieroMercado[0]['QUIERO_ESTRELLAS_CROMO']!="")
		{
			$nEstre =$comma." NºEstrellas:<strong>".getAsteriscos($aQuieroMercado[0]['QUIERO_ESTRELLAS_CROMO'])."</strong>";
		}
		$demanda1 ="<strong>CROMO1:</strong> ".$nomb.$refe.$nEstre;
	}
	if (isset($aQuieroMercado[1]))
	{
		$nomb ="";
		$refe ="";
		$nEstre ="";
		$comma = "";
		if ($aQuieroMercado[1]['QUIERO_NOMBRE_CROMO_ID']!="")
		{
			$cromoI = getCromoFromID($dbh,$aQuieroMercado[1]['QUIERO_NOMBRE_CROMO_ID']);
			$nomb ="Nombre:<strong>".$cromoI['name']."</strong>";
			$comma = ",";
		}
		if ($aQuieroMercado[1]['QUIERO_REF_CROMO']!="")
		{
		$refe =$comma." Referencia:<strong>".$aQuieroMercado[1]['QUIERO_REF_CROMO']."</strong>";
		$comma = ",";
		}
		if ($aQuieroMercado[1]['QUIERO_ESTRELLAS_CROMO']!="")
		{
			$nEstre =$comma." NºEstrellas:<strong>".getAsteriscos($aQuieroMercado[1]['QUIERO_ESTRELLAS_CROMO'])."</strong>";
		}
		$demanda2 ="<strong>CROMO2:</strong> ".$nomb.$refe.$nEstre;
	}
	if (isset($aQuieroMercado[2]))
	{
		$nomb ="";
		$refe ="";
		$nEstre ="";
		$comma = "";
		if ($aQuieroMercado[2]['QUIERO_NOMBRE_CROMO_ID']!="")
		{
			$cromoI = getCromoFromID($dbh,$aQuieroMercado[2]['QUIERO_NOMBRE_CROMO_ID']);
			$nomb ="Nombre:<strong>".$cromoI['name']."</strong>";
			$comma = ",";
		}
		if ($aQuieroMercado[2]['QUIERO_REF_CROMO']!="")
		{
		$refe =$comma." Referencia:<strong>".$aQuieroMercado[2]['QUIERO_REF_CROMO']."</strong>";
		$comma = ",";
		}
		if ($aQuieroMercado[2]['QUIERO_ESTRELLAS_CROMO']!="")
		{
			$nEstre =$comma." NºEstrellas:<strong>".getAsteriscos($aQuieroMercado[2]['QUIERO_ESTRELLAS_CROMO'])."</strong>";
		}
		$demanda3 ="<strong>CROMO3:</strong> ".$nomb.$refe.$nEstre;
	}
if (count($aQuieroMercado)>0)
{
	$sTexto = "";
?>

<div class="form-group">
	<label >
		<?php echo $alumno['NOMBRE']." ".$alumno['APELLIDO1']." ".$alumno['APELLIDO2']?></label>

<!--Table-->
<table class="table table-striped w-auto table-bordered">
  <!--Table head-->
  <thead>
    <tr>
      <td><?php echo $demanda1; ?></td>
      </tr>
      <?php if ($demanda2!="") {?>
    <tr>
      <td><?php echo $demanda2; ?></td>
      </tr>
  <?php } if ($demanda3!="") {?>
    <tr>
      <td><?php echo $demanda3; ?></td>
      </tr>
  <?php } ?>
  </thead>
</table>
</div>

<?php
}
}
echo'<label >'.  $sTexto.'</label>';
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
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
	<script type="text/javascript">
	function manageSubmitOfrezco()
	{ 
		document.getElementById("form1").action="mimercado.php";
      	document.getElementById("form1").submit(); 

	}
	function manageSubmitQuiero()
	{ 
		document.getElementById("form2").action="mimercado.php";
      	document.getElementById("form2").submit(); 

	}



	$(document).ready(function () {          
		setTimeout(function() {
			$('.succWrap').slideUp("slow");
		}, 3000);
		});

	</script>
</body>
</html>
<?php } ?>