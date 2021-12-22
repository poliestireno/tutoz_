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

	//var_export($_POST);	
		if (isset($_GET['idc']))
		{
		  $idCur=$_GET['idc'];
		}
 		else
		{
		  $idCur=$_POST['idc3'];
		}

if(isset($_POST['esDelete'])&&($_POST['esDelete']!=0))
{
	
$justiAux = getJustificanteFromId($dbh,$_POST['esDelete']);

	if (file_exists("../".$justiAux['ENLACE']))
	{
    unlink("../".$justiAux['ENLACE']);  
	}
	borrarJustificanteFromId($dbh,$_POST['esDelete']);
	$msg="justificante borrado correctamente...";
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
	
	<title>Justificantes</title>

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
						<h3 class="page-title">Justificantes de clase <?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE']?></h3>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Justificantes</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">

<form action="admin_justificantes.php" id="form3" method="post">
<input type="hidden" name="idc3" id="idc3" value="<?php echo $_POST['idc3']?>"/>
<input type="hidden" name="esDelete" id="esDelete" value="0">
<table ID="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
  <!--Table head-->
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Fecha desde</th>
      <th>Fecha hasta</th>
      <th>Comentario</th>
      <th>Archivo</th>         
      <th>Borrar</th>         
   </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
<?php

function diferenciaDias($date1,$date2)
{
	$startTimeStamp = strtotime($date1);
$endTimeStamp = strtotime($date2);

$timeDiff = abs($endTimeStamp - $startTimeStamp);

$numberDays = $timeDiff/86400;  // 86400 seconds in one day

// and you might want to convert to integer
$diaN = intval($numberDays)+1;

return "(".$diaN.(($diaN==1)?" día":" días").")";

}



$alumnosCurso = getAlumnosFromCursoID($dbh,$idCur);
foreach ($alumnosCurso as $alumno) 
{
	$justifcantesAlumno = getJustificantesFromAlumno($dbh,$alumno['ID']);
	foreach ($justifcantesAlumno as $justifI) 
	{
		echo '<tr class="table-info">';

	        echo '<td>'.$alumno['APELLIDO1'].' '.$alumno['APELLIDO2'].', '.$alumno['NOMBRE'].'</td>';
	        echo '<td>'.$justifI['DIA_DESDE'].'</td>';
	        echo '<td>'.$justifI['DIA_HASTA'].' '.diferenciaDias($justifI['DIA_DESDE'],$justifI['DIA_HASTA'])
.'</td>';
	        echo '<td align="center" data-toggle="tooltip" title="'.$justifI['COMENTARIO'].'">&nbsp; '.((strlen($justifI['COMENTARIO'])<50)?$justifI['COMENTARIO']:'<i class="fa fa-comment"></i>').'</td>';
	        echo '<td align="center" >'.
	'<a target="_blank" href="../'.$justifI['ENLACE'].'">&nbsp; <i class="fa fa-file"></i></a>'
	        .'</td>';
	        echo '<td onclick="manageDelete('.$justifI['ID'].')" align="center" >&nbsp; <i class="fa fa-trash"></i></td>';

	  echo '</tr>';
	}
}
?>
  </tbody>
  <!--Table body-->


</table>


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
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>						
	<script type="text/javascript">
				 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 5000);
					});
	$('#zctb').DataTable( {
    "order": [[ 0, "asc" ],[ 1, "desc" ]]
} );
	function manageDelete(delId)
	{
				Swal.fire({
          title: '¿Seguro que quieres borrar el justificante?',
          text: "¿Seguro, Seguro, Seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, lo borro para siempre!'
        }).then((result) => {
          if (result.value) {
    document.getElementById("esDelete").value=delId;
    document.getElementById("form3").action="admin_justificantes.php";
    document.getElementById("form3").submit(); 
          }
        });
	}

	</script>
</body>
</html>
<?php } ?>