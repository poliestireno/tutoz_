<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
	{	
header('location:index.php');
}
else{
	
if(isset($_POST['submit']))
  {	
	$file = $_FILES['image']['name'];
	$file_loc = $_FILES['image']['tmp_name'];
	$folder="images/";
	$new_file_name = strtolower($file);
	$final_file=str_replace(' ','-',$new_file_name);
	
	$name=$_POST['name'];
	$CORREO=$_POST['CORREO'];
	$APELLIDO1no=$_POST['APELLIDO1'];
	$APELLIDO2=$_POST['APELLIDO2'];
	$IDedit=$_POST['editID'];
	$image=$_POST['image'];

	if(move_uploaded_file($file_loc,$folder.$final_file))
	{
		$image=$final_file;
	}

	$sql="UPDATE ALUMNOS SET NOMBRE=(:name), APELLIDO1=(:APELLIDO1no), APELLIDO2=(:APELLIDO2), Image=(:image) WHERE ID=(:IDedit)";
	$query = $dbh->prepare($sql);
	$query-> bindParam(':name', $name, PDO::PARAM_STR);
	$query-> bindParam(':APELLIDO1no', $APELLIDO1no, PDO::PARAM_STR);
	$query-> bindParam(':APELLIDO2', $APELLIDO2, PDO::PARAM_STR);
	$query-> bindParam(':image', $image, PDO::PARAM_STR);
	$query-> bindParam(':IDedit', $IDedit, PDO::PARAM_STR);
	$query->execute();
	$msg="Information Updated Successfully";
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
	
	<title>Ranking</title>

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
	
?>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
<h3>Ranking de mi clase (<?php 

	echo getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$_SESSION['alogin'])['ID_CURSO'])[0]['NOMBRE']?>)</h3>
<!--Table-->
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>#</th>
      <th>Nombre</th>
      <th>Nivel</th>
      <th>Total Estrellas</th>
      <th>Retos</th>
      <th>Ganas</th>
      <th>Cromos</th>
      <th>Suerte</th>
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
  	<?php
  	 $aAlumnosCurso = getAlumnosCompanerosCursoFromCorreo($dbh,$_SESSION['alogin']);
  	 //var_dump($aAlumnosCurso);

  	 $aTotalAlumnos = array();
  	 foreach ($aAlumnosCurso as $alumno) 
  	 {
	  	// estrellas cromos

		$estrellasCromos = getEstrellasCromos($dbh,$alumno['CORREO']);



		// estrellas combinaciones


// INI CODIGO IGUAL EN mialbum.php y ranking.php, si se modifica hay que copiarlo
$aRe = getEstrellasCombinaciones($dbh,$alumno['CORREO']);
$estrellasCombinaciones=$aRe [0];
$sEstrellas=$aRe [1];
// FIN CODIGO IGUAL EN mialbum.php y ranking.php, si se modifica hay que copiarlo





  // calculo retos

$totalRetos = getEstrellasRetos($dbh,$alumno['CORREO']);
  // calculo de ganas


$totalComportamiento =getEstrellasGanas($dbh,$alumno['CORREO']);


	  	
	  	$totalCromos = $estrellasCromos+$estrellasCombinaciones;
	  	$totalSuerte = 0;
	  	$totalTotal = $totalRetos+$totalComportamiento+$totalCromos+$totalSuerte;
	  	
		$arrayAlumno=array();
		$arrayAlumno['Nombre']=$alumno['NOMBRE'].' '.$alumno['APELLIDO1'].' '.$alumno['APELLIDO2'];
		$arrayAlumno['Retos']=$totalRetos;
		$arrayAlumno['Comportamiento']=$totalComportamiento;
		$arrayAlumno['Cromos']=$totalCromos;
		$arrayAlumno['Suerte']=$totalSuerte;
		$arrayAlumno['Total']=$totalTotal;
		$arrayAlumno['Nivel']=$alumno['NUMERO_NIVEL']." (".getNivelFromNumeroNivel($dbh,$alumno['CORREO'],$alumno['NUMERO_NIVEL'])['NOMBRE'].")";
		$arrayAlumno['CORREO']=$alumno['CORREO'];
		$aTotalAlumnos[]=$arrayAlumno;	


	    
  	 }
function iniNegrita($correologin, $correoalumno)
{
    return ($correologin== $correoalumno)?"<b>":"";
}
function finNegrita($correologin, $correoalumno)
{
    return ($correologin== $correoalumno)?"</b>":"";
}

function cmp($a, $b)
{
    return $b['Total'] - $a['Total'];
}
usort($aTotalAlumnos, "cmp");

  	 $contador=1;
foreach ($aTotalAlumnos as $alum) 
 {

	  	echo '<tr class="table-info">';
	      echo '<th scope="row">'.$contador.'</th>';
	      echo '<td>'.iniNegrita($_SESSION['alogin'], $alum['CORREO']).$alum['Nombre'].finNegrita($_SESSION['alogin'], $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($_SESSION['alogin'], $alum['CORREO']).$alum['Nivel'].finNegrita($_SESSION['alogin'], $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($_SESSION['alogin'], $alum['CORREO']).$alum['Total'].finNegrita($_SESSION['alogin'], $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($_SESSION['alogin'], $alum['CORREO']).$alum['Retos'].finNegrita($_SESSION['alogin'], $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($_SESSION['alogin'], $alum['CORREO']).$alum['Comportamiento'].finNegrita($_SESSION['alogin'], $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($_SESSION['alogin'], $alum['CORREO']).$alum['Cromos'].finNegrita($_SESSION['alogin'], $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($_SESSION['alogin'], $alum['CORREO']).$alum['Suerte'].finNegrita($_SESSION['alogin'], $alum['CORREO']).'</td>';
	    echo '</tr>';
	    $contador++;
}




	 ?>
    
  </tbody>
  <!--Table body-->


</table>
<h3>Mis Retos conseguidos</h3>
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>Reto</th>
      <th>Estrellas</th>
      <th>Fecha</th>
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
  	<?php
$aToRetos = getEstrellasRetosFromCorreo($dbh,$_SESSION['alogin']);
//var_dump($aToRetos);
foreach ($aToRetos as $reto) 
{

	  	echo '<tr class="table-info">';
	      echo '<td>'.$reto['NOMBRE_TAREA'].'</td>';
	      echo '<td><b>'.$reto['ESTRELLAS_CONSEGUIDAS'].'</b>/'.$reto['TOTAL_ESTRELLAS'].'</td>';
	      echo '<td>'.$reto['FECHA'].'</td>';
	    echo '</tr>';
}


	 ?>
    
  </tbody>
  <!--Table body-->


</table>
<h3>Mis Ganas</h3>
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>Asignatura</th>
      <th>Estrellas</th>
      <th>Día</th>
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
  	<?php
$aToCompor = getEstrellasComportamientoFromCorreo($dbh,$_SESSION['alogin']);	
foreach ($aToCompor as $compor) 
{

	  	echo '<tr class="table-info">';
	  	  echo '<td>'.$compor['NOMBRE_ASIGNATURA'].'</td>';
	      echo '<td><b>'.$compor['ESTRELLAS'].'</b></td>';
	      echo '<td>'.$compor['DIA'].'</td>';
	    echo '</tr>';
}


	 ?>
    
  </tbody>
  <!--Table body-->


</table>
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