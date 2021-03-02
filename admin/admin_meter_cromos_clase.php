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

		if (isset($_GET['idc']))
		{
		  $idCur=$_GET['idc'];
		}
		else
		{
		  $idCur=$_POST['idc2'];
		}
	if (isset($_POST['ref_desde']))
	{

if (utilizaCromosCurso($dbh,$idCur))
{
		$aAlumnosCurso = getAlumnosFromCursoID($dbh,$idCur);
		$setId = NULL;
		foreach ($aAlumnosCurso as $alumno) 
     	{
     		//var_export($alumno);
     		$setId = getSetCromosIdFromAlumno($dbh,$alumno['CORREO']);
     		//var_export($setId);
     		for ($i=$_POST['ref_desde']; $i <= $_POST['ref_hasta']; $i++) 
     		{ 
	            
     			$cromoActual = getCromo($dbh,$alumno['CORREO']);

	            $ID_POSEEDOR=NULL;
	            $GENERADO=0;
		        $ID_CREADOR=$alumno['ID'];        
		        $name=$cromoActual['name'];
		        $color=$cromoActual['color'];
		        $mana_w=$cromoActual['mana_w'];
		        $picture=$cromoActual['picture'];
		        $cardtype=$cromoActual['cardtype'];
		        $rarity=$cromoActual['rarity'];
		        $cardtext=$cromoActual['cardtext'];
		        $power=$i;
		        $toughness=$_POST['ref_hasta'];
		        $artist=$cromoActual['artist'];
		        $bottom=$cromoActual['bottom'];
				insertarCromo($dbh,$ID_CREADOR,$ID_POSEEDOR,$GENERADO,$setId, $name, $color, $mana_w, $picture, $cardtype, $rarity, $cardtext, $power, $toughness, $artist, $bottom);
     		}
     	}
     	modificarMaxReferenciaCromoFromSetId($dbh,$_POST['ref_hasta'], $setId);
		$msg="Cromos con referencia [".$_POST['ref_desde']."..".$_POST['ref_hasta']."] insertados correctamente correctamente!"; 
	}
	else
	{
		$msg="La clase no está habilitada con cromos";
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
	
	<title>Meter cromos clase</title>

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
						<h3 class="page-title">Meter cromos clase <?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE']?></h3>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Máxima referencia: (<?php echo (utilizaCromosCurso($dbh,$idCur))?(getCromo($dbh,getAlumnosFromCursoID($dbh,$idCur)[0]['CORREO'])['toughness']):"-" ?>)</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">

<form action="admin_meter_cromos_clase.php" id="form3" method="post">
  <input type="hidden" name="idc2" id="idc2" value="<?php echo $_POST['idc2']?>">

	<div class="form-group">
 <label class="col-sm-2 control-label">Referencia desde (inclusive)</label>
<div class="col-sm-4">
<input type="text" name="ref_desde" class="form-control" required value=""/>

</div>

<label class="col-sm-2 control-label">Referencia hasta (inclusive)</label>
<div class="col-sm-4">
<input type="text" name="ref_hasta" class="form-control" required value=""/>
</div>                           

	</div>
	<div class="col-sm-8 col-sm-offset-2"></div>
	<div class="col-sm-8 col-sm-offset-2"></div>
    <div class="form-group">
  <div class="col-sm-2 col-sm-offset-2">
    <button class="btn btn-warning" name="submit" type="submit">Crear cromos clase</button>
  </div>
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
					}, 5000);
					});
	</script>
</body>
</html>
<?php } ?>