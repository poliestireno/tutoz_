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
$loginAux = $_SESSION['alogin'];
if (isset($_GET['l']))
{
	$loginAux =$_GET['l'];
}

$confAsig = getConfAsignaturaFromID($dbh,getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$loginAux)['ID_CURSO'])[0]['ID_CONF_ASIGNATURAS'])['NOMBRE'];
//echo $confAsig;
	
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
if (isset($_GET['l']))
{
	$cromo = getCromo($dbh,$_GET['l']);
function url(){
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}

?>
<div class="form-group">
	<div class="col-sm-3">
	</div>
<div class="col-sm-3 text-center">
		<img src="https://www.mtgcardmaker.com/mcmaker/createcard.php?name=<?php echo $cromo['name'];?>&color=<?php echo $cromo['color'];?>&mana_w=<?php echo $cromo['mana_w'];?>&picture=<?php echo htmlentities(substr(url(),0,strrpos(url(), '/')).'/imagesCromos/'.$cromo['picture'])?>&cardtype=<?php echo $cromo['cardtype'];?>&rarity=<?php echo $cromo['rarity'];?>&cardtext=<?php echo $cromo['cardtext'];?>&power=&toughness=<?php echo $cromo['toughness'];?>&artist=<?php echo $cromo['artist'];?>&bottom=<?php echo $cromo['bottom'];?>" style="width:250px; border-radius:5%; margin:10px;">
	</div>
	<div class="col-sm-3">
	</div>
</div>

<?php
}
?>
	<?php if (!isset($_GET['l'])) include('includes/header.php');?>
	<div class="ts-main-content">
	<?php if (!isset($_GET['l'])) include('includes/leftbar.php');?>

		<div class="content-wrapper">
<?php
if (!isset($_GET['l']))
{
?>
<h3>Ranking de mi clase (<?php 
	echo getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$loginAux)['ID_CURSO'])[0]['NOMBRE']?>)</h3>
<?php
}
else
{
	?>
	<h3><?php 
	$alu =  getAlumnoFromCorreo($dbh,$loginAux);
	echo $alu['NOMBRE']." ".$alu['APELLIDO1']." ".$alu['APELLIDO2']?></h3>
<?php
}
?>

<!--Table-->
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>#</th>
      <th>Nombre</th>
      <th>Total Estrellas</th>
 <?php
if ($confAsig=='MENU_SIMPLON_RETOS')
{

$aToConcursos = getTareasTotalesFromAlumno($dbh,$loginAux,1);

if (Count($aToConcursos)>0)
{
?>
      <th>Concursos</th>
 <?php
}
?>
      <th>Retos</th><th>Ganas</th>
<?php
}

else if ($confAsig!='MENU_BASICA')
{?>
      <th>Nivel</th>
      <?php

$aToConcursos = getTareasTotalesFromAlumno($dbh,$loginAux,1);

if (Count($aToConcursos)>0)
{
?>
      <th>Concursos</th>
 <?php
}
 ?>
      <th>Retos</th>

      <th>Ganas</th>
      <th>Cromos</th>
      <th>Bonus</th>
<?php 
}
?>


    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
  	<?php
  	 $aAlumnosCurso = getAlumnosCompanerosCursoFromCorreo($dbh,$loginAux);
  	 //var_dump($aAlumnosCurso);

  	 $aTotalAlumnos = array();
  	 foreach ($aAlumnosCurso as $alumno) 
  	 {
	  	// estrellas cromos

		$estrellasCromos = getEstrellasCromos($dbh,$alumno['CORREO']);



		// estrellas combinaciones


// INI CODIGO IGUAL EN mialbum.php y ranking.php, si se modifica hay que copiarlo
$aRe = getEstrellasCombinaciones($dbh,$alumno['CORREO']);
$estrellasCombinaciones=$aRe[0];
$sEstrellas=$aRe[1];
// FIN CODIGO IGUAL EN mialbum.php y ranking.php, si se modifica hay que copiarlo





  // calculo retos

$totalRetos = getEstrellasRetos($dbh,$alumno['CORREO'],0);
//var_export($totalRetos);
$totalConcursos = getEstrellasRetos($dbh,$alumno['CORREO'],1);
//var_export($totalConcursos);
  // calculo de ganas


$totalComportamiento =getEstrellasGanas($dbh,$alumno['CORREO']);


	  	
	  	$totalCromos = $estrellasCromos+$estrellasCombinaciones;
	  	$totalSuerte = getEstrellasBonos($dbh,$alumno['CORREO']);
	  	$totalTotal = $totalRetos+$totalConcursos+$totalComportamiento+$totalCromos+$totalSuerte;
	  	
		$arrayAlumno=array();
		$arrayAlumno['Nombre']=$alumno['NOMBRE'].' '.$alumno['APELLIDO1'].' '.$alumno['APELLIDO2'];
		$arrayAlumno['Retos']=$totalRetos;
		$arrayAlumno['Concursos']=$totalConcursos;
		$arrayAlumno['Comportamiento']=$totalComportamiento;
		$arrayAlumno['Cromos']=$totalCromos;
		$arrayAlumno['Suerte']=$totalSuerte;
		$arrayAlumno['Total']=$totalTotal;

		$siguienteNivel = getNivelFromNumeroNivel($dbh,$alumno['CORREO'],$alumno['NUMERO_NIVEL']+1);
		
		$estrellasFaltanSiguienteNivel = $siguienteNivel['ESTRELLAS_DESBLOQUEO']-$totalTotal;
		$textoSiguienteNivel = "";
		if ($estrellasFaltanSiguienteNivel>0)
		{	
			$textoSiguienteNivel = " [Siguiente nivel a ".$estrellasFaltanSiguienteNivel	." estrellas]";
		}
		
		$arrayAlumno['NivelSinSiguiente']=$alumno['NUMERO_NIVEL']." (".getNivelFromNumeroNivel($dbh,$alumno['CORREO'],$alumno['NUMERO_NIVEL'])['NOMBRE'].")";
		$arrayAlumno['NivelConSiguiente']=$alumno['NUMERO_NIVEL']." (".getNivelFromNumeroNivel($dbh,$alumno['CORREO'],$alumno['NUMERO_NIVEL'])['NOMBRE'].")".$textoSiguienteNivel;
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
		if ($loginAux==$alum['CORREO'])
		{
			echo '<tr>
      <th></th>
      <th>Nombre</th>
      
      <th>Total Estrellas</th>';
if ($confAsig=='MENU_SIMPLON_RETOS')
{
echo ''.((Count($aToConcursos)>0)?'<th>Concursos</th>':'').'<th>Retos</th><th>Ganas</th>';
}  
else if ($confAsig!='MENU_BASICA')
{      
      echo '<th>Nivel</th>'.((Count($aToConcursos)>0)?'<th>Concursos</th>':'').'<th>Retos</th>
      <th>Ganas</th>
      <th>Cromos</th>
      <th>Bonus</th>';
}
 echo   '</tr>';
		}


	  	echo '<tr class="table-info">';
	      echo '<th scope="row">'.$contador.'</th>';
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).$alum['Nombre'].finNegrita($loginAux, $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Total']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
if ($confAsig=='MENU_SIMPLON_RETOS')
{
if (Count($aToConcursos)>0)
{
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Concursos']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
}


	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Retos']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Comportamiento']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
}
else if ($confAsig!='MENU_BASICA')
{      

	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['NivelConSiguiente']:$alum['NivelSinSiguiente']).finNegrita($loginAux, $alum['CORREO']).'</td>';

if (Count($aToConcursos)>0)
{
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Concursos']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
}


	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Retos']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Comportamiento']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Cromos']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Suerte']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
}

	    echo '</tr>';
	    $contador++;
}




	 ?>
    
  </tbody>
  <!--Table body-->
  <thead>
    <tr>
      <th>#</th>
      <th>Nombre</th>
      <th>Total Estrellas</th>
 <?php
if ($confAsig=='MENU_SIMPLON_RETOS')
{

$aToConcursos = getTareasTotalesFromAlumno($dbh,$loginAux,1);

if (Count($aToConcursos)>0)
{
?>
      <th>Concursos</th>
 <?php
}
?>
      <th>Retos</th><th>Ganas</th>
<?php
}
else if ($confAsig!='MENU_BASICA')
{?>
      <th>Nivel</th>
      <?php

$aToConcursos = getTareasTotalesFromAlumno($dbh,$loginAux,1);

if (Count($aToConcursos)>0)
{
?>
      <th>Concursos</th>
 <?php
}
 ?>
      <th>Retos</th>

      <th>Ganas</th>
      <th>Cromos</th>
      <th>Bonus</th>
<?php 
}
?>



    </tr>
  </thead>

</table>

<?php

if ($confAsig!='MENU_BASICA')
{
if (Count($aToConcursos)>0)
{
?>

<h3>Mis Concursos</h3>
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>Concurso</th>
      <th>Estado</th>
      <th>Estrellas conseguidas</th>
      <th>Máximo estrellas</th>
      <th>Entregado en fecha</th>
      <th>Fecha límite</th>
      <!--th>Descripción</th-->
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
  	<?php
//var_dump($aToRetos);
foreach ($aToConcursos as $reto) 
{

		$datosAlumnoTarea = getDatosAlumnoTarea($dbh,$loginAux,$reto['ID']);
		//var_export($datosAlumnoTarea);
	  	echo '<tr class="table-info">';
	      echo '<td>'.$reto['NOMBRE'].'</td>';
	      echo '<td>'.$datosAlumnoTarea['ESTADO'].'</td>';
echo '<td>'.(($datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']==NULL)?'-':'<b>'.$datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS'].'</b>').'</td>';
	      echo '<td>'.$reto['TOTAL_ESTRELLAS'].'</td>';
	      echo '<td>'.(($datosAlumnoTarea['FECHA']==NULL)?'-':$datosAlumnoTarea['FECHA']).'</td>';
	      echo '<td>'.(($reto['FECHA_LIMITE']==NULL)?'-':$reto['FECHA_LIMITE']).'</td>';
	      //echo '<td>'.$reto['DESCRIPCION'].'</td>';
	    echo '</tr>';
}

	 ?>
    
  </tbody>
  <!--Table body-->


</table>
<?php
}
}
if ($confAsig!='MENU_BASICA')
{
?>
<h3>Mis Retos</h3>
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>Reto</th>
      <th>Estado</th>
      <th>Estrellas conseguidas</th>
      <th>Máximo estrellas</th>
      <th>Entregado en fecha</th>
      <th>Fecha límite</th>
      <!--th>Descripción</th-->
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
  	<?php

$aToRetos = getTareasTotalesFromAlumno($dbh,$loginAux,0);
//var_dump($aToRetos);
foreach ($aToRetos as $reto) 
{

		$datosAlumnoTarea = getDatosAlumnoTarea($dbh,$loginAux,$reto['ID']);
		//var_export($datosAlumnoTarea);
	  	echo '<tr class="table-info">';
	      echo '<td>'.$reto['NOMBRE'].'</td>';
	      echo '<td>'.$datosAlumnoTarea['ESTADO'].'</td>';
echo '<td>'.(($datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']==NULL)?'-':'<b>'.$datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS'].'</b>').'</td>';
	      echo '<td>'.$reto['TOTAL_ESTRELLAS'].'</td>';
	      echo '<td>'.(($datosAlumnoTarea['FECHA']==NULL)?'-':$datosAlumnoTarea['FECHA']).'</td>';
	      echo '<td>'.(($reto['FECHA_LIMITE']==NULL)?'-':$reto['FECHA_LIMITE']).'</td>';
	      //echo '<td>'.$reto['DESCRIPCION'].'</td>';
	    echo '</tr>';
}

	 ?>
    
  </tbody>
  <!--Table body-->


</table>
<h3>Mis Ganas (0..4)</h3>
<?php
}
?>
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
$aToCompor = getEstrellasComportamientoFromCorreo($dbh,$loginAux);	
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
<?php
if (($confAsig!='MENU_BASICA')&&($confAsig!='MENU_SIMPLON_RETOS'))
{
?>
<h3>Mis Bonus</h3>
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Estrellas</th>
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
  	<?php
$aToCompor = getBonusFromCorreo($dbh,$loginAux);	
foreach ($aToCompor as $compor) 
{

	  	echo '<tr class="table-info">';
	  	  echo '<td>'.$compor['NOMBRE'].'</td>';
	      echo '<td><b>'.$compor['NUM_ESTRELLAS'].'</b></td>';
	    echo '</tr>';
}


	 ?>
    
  </tbody>
  <!--Table body-->


</table>
<?php
}
?>

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