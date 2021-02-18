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
	



if(isset($_POST['iPregunta']))
{	
	$alumno = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);
	insertMonotermino($dbh,$_POST['iPregunta'],$_POST['iRespuesta'],$alumno['ID_CURSO'],$alumno['ID']);
	$lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {
		$msg="Monotérmino mandado correctamente, ¡gracias!";
    }
    else
    {
		$msg="Error al mandar el monotérmino, intentalo más tarde...";
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
	
	<title>Mandar Monotérmino</title>

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
									<div class="panel-heading">MANDAR MONOTÉRMINO</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" id="form1" class="form-horizontal" enctype="multipart/form-data" >

<div class="form-group">
	<div class="col-sm-4">
	</div>
	<div class="col-sm-4 text-center">
		<img src="" style="width:200px; border-radius:50%; margin:10px;">
	</div>
	<div class="col-sm-4">
	</div>
</div>

<div class="form-group">
	<label class="col-sm-2 control-label">Pregunta:</label>
	<div class="col-sm-9">
	<input type="text" name="iPregunta" id="iPregunta" class="form-control" required value="">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-2 control-label">Respuesta (una palabra o palabras entre comas):</label>
	<div class="col-sm-4">
	<input type="text" name="iRespuesta" id="iRespuesta" class="form-control" required value="">
	</div>
</div>


<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2">
<a onclick="manageMandar()" class="btn btn-danger" >Mandar Monotérmino</a>
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

	function manageMandar()
	{
	
var pre = document.getElementById("iPregunta").value;
var res = document.getElementById("iRespuesta").value;

	if (pre == '' || res == '') 
	{
		Swal.fire('UFFF!',"Se deben rellenar todos los campos!" ,'error');
		return false;
	}
	else
	{

		Swal.fire({
          title: '¿Seguro que quieres mandar esta pregunta y respuesta?',
          text: "¿Seguro, Seguro, Seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, la mando!'
        }).then((result) => {
          if (result.value) {
    document.getElementById("form1").action="mandar_monotermino.php";
    document.getElementById("form1").submit(); 
          }
        });
	}
}
	
	</script>
</body>
</html>
<?php } ?>