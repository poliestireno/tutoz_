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
	
$tiposBotones = array( "btn btn-primary", "btn btn-success", "btn btn-warning", "btn btn-info");
$alumnoDB = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);

$juicioActivo = getJuicioActivo($dbh,getAsignaturasFromCurso($dbh,$alumnoDB['ID_CURSO'])[0]['ID']);


if(isset($_POST['opcionSel']))
{	
	
	if (existeAlumnoJuicio($dbh,$juicioActivo['ID'],$alumnoDB['ID']))
	{
		$numRows = modificarAlumnoJuicio($dbh,$juicioActivo['ID'],$alumnoDB['ID'],$_POST['opcionSel']);
		if ($numRows>0)
		{

	        $msg="Voto actualizado correctamente a la opción: ".$_POST['opcionSel'];
	    }
	    else
	    {
	        $msg="Error al actualizar el voto";       
	    }
	}
	else
	{
		insertarAlumnoJuicio($dbh,$juicioActivo['ID'],$alumnoDB['ID'],$_POST['opcionSel']);
		$lastInsertId = $dbh->lastInsertId();
	    if($lastInsertId)
	    {

	        $msg="Votado correctamente con la opción: ".$_POST['opcionSel'];
	    }
	    else
	    {
	        $msg="Error al efectuar el voto";       
	    }
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
	
	<title>Vota Juicio</title>

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
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">VOTA JUICIO: <?php echo $juicioActivo['NOMBRE']; echo (($juicioActivo['OPCIONES']==NULL)?" NO HAY JUICIO ACTIVO":"")?></div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" id = "form1" class="form-horizontal" enctype="multipart/form-data">
<input type='hidden' name='opcionSel' id='opcionSel'/>
  






<?php
$tamDesc = strlen($juicioActivo['DESCRIPCION']);
echo '<div class="form-group"><label class="col-sm-8 control-label" style="text-align:left;font-family:Courier; font-size:'.(65-$tamDesc).'px;" > '.$juicioActivo['DESCRIPCION'].'</label></div>';

if ($juicioActivo['OPCIONES']!=NULL)
{
	$aOpciones = explode(",", $juicioActivo['OPCIONES']);	
	for ($i=0; $i < count($aOpciones); $i++) {


		$tamTexto = strlen($aOpciones[$i]);

	echo '<div id="aa_'.$i.'" class="btn-group btn-group-justified" >';
	echo '<a id="bb_'.$i.'" style="font-size:'.(60-$tamTexto).'px; height: 90px" onclick="managebutton(\''.$aOpciones[$i].'\')"  class="'.$tiposBotones[$i % sizeof($tiposBotones)	].'">'.$aOpciones[$i].'</a>';
	echo '</div>';

	}
}
?>

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
function managebutton(opcionSel)
{
	//alert(opcionSel);
	document.getElementById('opcionSel').value=opcionSel;
   	document.getElementById("form1").submit(); 
}
	</script>
</body>
</html>
<?php } ?>