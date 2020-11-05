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
//var_export($_POST);
if((isset($_POST['sCromo1']))&&($_POST['sCromo1']!='')&&($_POST['sCromo2']!=''))
  {	

  	$alumno1 = getAlumnoFromId($dbh,$_POST['sAlumno1']);
  	$aOrdenAlbum1 = explode(",", $alumno1['ORDEN_ALBUM']);
  	$aOrdenCreadores1 = explode(",", $alumno1['ORDEN_CREADORES']);
  	$aOrdenReferencias1 = explode(",", $alumno1['ORDEN_REFERENCIAS_TOTAL']);
  	$posicionCambio1 = -1;
  	for ($i=0; $i < Count($aOrdenAlbum1); $i++) 
  	{ 
  		if ($aOrdenAlbum1[$i]==$_POST['sCromo1'])
  		{			
			$posicionCambio1 = $i;
			break;
  		}
  	}
  	$alumno2 = getAlumnoFromId($dbh,$_POST['sAlumno2']);
  	$aOrdenAlbum2 = explode(",", $alumno2['ORDEN_ALBUM']);
  	$aOrdenCreadores2 = explode(",", $alumno2['ORDEN_CREADORES']);
  	$aOrdenReferencias2 = explode(",", $alumno2['ORDEN_REFERENCIAS_TOTAL']);
  	$posicionCambio2 = -1;
  	for ($i=0; $i < Count($aOrdenAlbum2); $i++) 
  	{ 
  		if ($aOrdenAlbum2[$i]==$_POST['sCromo2'])
  		{			
			$posicionCambio2 = $i;
			break;
  		}
  	}

  	modificarPoseedorCromo($dbh,$_POST['sAlumno1'], $_POST['sCromo2']);
  	modificarPoseedorCromo($dbh,$_POST['sAlumno2'], $_POST['sCromo1']);

  	$auxOrdenAlbum = $aOrdenAlbum1[$posicionCambio1];
	$auxOrdenCreadores = $aOrdenCreadores1[$posicionCambio1];
  	$auxOrdenReferencias = $aOrdenReferencias1[$posicionCambio1];

  	$aOrdenAlbum1[$posicionCambio1]=$aOrdenAlbum2[$posicionCambio2];
$aOrdenCreadores1[$posicionCambio1]=$aOrdenCreadores2[$posicionCambio2];
$aOrdenReferencias1[$posicionCambio1]=$aOrdenReferencias2[$posicionCambio2];

  	$aOrdenAlbum2[$posicionCambio2]=$auxOrdenAlbum;
$aOrdenCreadores2[$posicionCambio2]=$auxOrdenCreadores;
$aOrdenReferencias2[$posicionCambio2]=$auxOrdenReferencias;

	
	$sOrdenAlbum1 = implode(",", $aOrdenAlbum1);
	$sOrdenCreadores1 = implode(",", $aOrdenCreadores1);
	$sOrdenReferencias1 = implode(",", $aOrdenReferencias1);

	$sOrdenAlbum2 = implode(",", $aOrdenAlbum2);
	$sOrdenCreadores2 = implode(",", $aOrdenCreadores2);
	$sOrdenReferencias2 = implode(",", $aOrdenReferencias2);

modificarOrdenAlbum($dbh,$alumno1['CORREO'], $sOrdenAlbum1);
modificarOrdenCreadores($dbh,$alumno1['CORREO'], $sOrdenCreadores1);
modificarOrdenReferenciasTotal($dbh,$alumno1['CORREO'], $sOrdenReferencias1);

modificarOrdenAlbum($dbh,$alumno2['CORREO'], $sOrdenAlbum2);
modificarOrdenCreadores($dbh,$alumno2['CORREO'], $sOrdenCreadores2);
modificarOrdenReferenciasTotal($dbh,$alumno2['CORREO'], $sOrdenReferencias2);

	$msg="Cambio procesado correctamente!";
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
	
	<title>Cambiar Cromos</title>

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
		$sql = "SELECT * from admin;";
		$query = $dbh -> prepare($sql);
		$query->execute();
		$result=$query->fetch(PDO::FETCH_OBJ);
		$cnt=1;	
?>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h3 class="page-title">Cambiar Cromos</h3>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Cromos</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">

<form action="admin_cambio_cromos.php" id="form3" method="post">
 <input type='hidden' name='sAlumno1' id='sAlumno1' value='<?php echo $_POST['sAlumno1']?>'/>
 <input type='hidden' name='sAlumno2' id='sAlumno2' value='<?php echo $_POST['sAlumno2']?>'/>
                            <div class="form-group">
                            
<label class="col-sm-1 control-label"><?php $ali1 = getAlumnoFromId($dbh,$_POST['sAlumno1']); echo $ali1['NOMBRE'].' '.$ali1['APELLIDO1'];
 ?><span style="color:red">*</span></label>
                            <div class="col-sm-5">
  <select class="form-control col-md-2" ID="sCromo1" name="sCromo1">
  	<option></option>
<?php
	$aCromosAlumno1 = getCromosDeAlbum($dbh,$ali1['CORREO']);
    foreach ($aCromosAlumno1 as $cromoI) 
    {
echo "<option value='".$cromoI['ID']."'>".$cromoI['name']."-".$cromoI['power']."</option>";
    }

?>
  </select>
                            </div>
                           
                             

                            <label class="col-sm-1 control-label"><?php $ali2 = getAlumnoFromId($dbh,$_POST['sAlumno2']); echo $ali2['NOMBRE'].' '.$ali2['APELLIDO1'];
 ?><span style="color:red">*</span></label>
                            <div class="col-sm-5">
  <select class="form-control col-md-2" ID="sCromo2	" name="sCromo2">
  	<option></option>	
<?php
	$aCromosAlumno2 = getCromosDeAlbum($dbh,$ali2['CORREO']);
    foreach ($aCromosAlumno2 as $cromoI) 
    {
echo "<option value='".$cromoI['ID']."'>".$cromoI['name']."-".$cromoI['power']."</option>";
    }

?>  
  </select>
                            </div>
                            </div>



      <div class="form-group col-md-2">
      <a onclick="document.getElementById('form3').submit();"  class="btn btn-warning btn-outline btn-wrap-text">Cambiar</a>
    </div>
</form>									</div>
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