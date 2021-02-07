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

$idReto="";
if (isset($_GET['idr']))
{
  $idReto=$_GET['idr'];
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
	
	<title>Detalle reto</title>

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
$reto = getTareaFromID($dbh,$idReto);




//$folder="../retos/".getAsignaturaFromAsignaturaID($dbh,$reto['ID_ASIGNATURA'])['NOMBRE']."/".$reto['NOMBRE'];
?>
<h3><?php echo $reto['NOMBRE']?><a  data-toggle="tooltip" title="Corregir reto en otra ventana" href="admin_corregir_reto.php?idr=<?php echo $idReto?>" target="_blank"> [Corregir]</a></h3>
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>Reto</th>
      <th>Máximo estrellas</th>
      <th>Fecha límite</th>
      <th>Descripción</th>
      <th>Documento</th>
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
    <?php
      echo '<tr class="table-info">';
        echo '<td>'.$reto['NOMBRE'].'</td>';
        echo '<td>'.$reto['TOTAL_ESTRELLAS'].'</td>';
        echo '<td>'.(($reto['FECHA_LIMITE']==NULL)?'-':$reto['FECHA_LIMITE']).'</td>';
        echo '<td>'.$reto['DESCRIPCION'].'</td>';
        echo '<td>'.(($reto['LINK_DOCUMENTO']==NULL)?'-':'<a href="'.$reto['LINK_DOCUMENTO'].'" target="_blank" rel="noopener">[IR AL DOCUMENTO]</a>').'</td>';
      echo '</tr>';
    


   ?>
    
  </tbody>
  <!--Table body-->


</table>





<h3>Alumnos</h3>
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Estado</th>
      <th>Estrellas conseguidas</th>
      <th>Entregado en fecha</th>
      <!--th>Descripción</th-->
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
    <?php
 $alumnos = getAlumnosFromAsignaturaID($dbh,$reto['ID_ASIGNATURA']);
//var_dump($aToRetos);
foreach ($alumnos as $alumno) 
{

$datosAlumnoTarea = getDatosAlumnoTarea($dbh,$alumno['CORREO'],$idReto);
    //var_export($datosAlumnoTarea);


      echo '<tr class="table-info">';
        echo '<td><a data-toggle="tooltip" title="Calificar reto al alumno" href="admin_cromos.php?ida='.$alumno['CORREO'].'&idr='.$idReto.'" target="_blank">'.$alumno['NOMBRE'].' '.$alumno['APELLIDO1'].' '.$alumno['APELLIDO2'].'</a></td>';
        echo '<td>'.$datosAlumnoTarea['ESTADO'].'</td>';
echo '<td>'.(($datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']==NULL)?'-':'<b>'.$datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS'].'</b>').'</td>';
        echo '<td>'.(($datosAlumnoTarea['FECHA']==NULL)?'-':$datosAlumnoTarea['FECHA']).'</td>';
        //echo '<td>'.$reto['DESCRIPCION'].'</td>';
      echo '</tr>';
}

   ?>
    
  </tbody>
  <!--Table body-->


</table>




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
<?php 

} 
?>