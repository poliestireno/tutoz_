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
		<img src="https://www.mtgcardmaker.com/mcmaker/createcard.php?name=<?php echo $cromo['name'];?>&color=<?php echo $cromo['color'];?>&mana_w=<?php echo $cromo['mana_w'];?>&picture=<?php echo htmlentities(substr(url(),0,strrpos(url(), '/')).'/imagesCromos/'.$cromo['picture'])?>&cardtype=<?php echo 
		(($cromo['cardtype']!='')?(
		((getValorAtributo($dbh,$_GET['l'])>=0)?'%2B':'').getValorAtributo($dbh,$_GET['l']).'  '
		):'').$cromo['cardtype'];?>&rarity=<?php echo $cromo['rarity'];?>&cardtext=<?php echo $cromo['cardtext'];?>&power=&toughness=<?php echo $cromo['toughness'];?>&artist=<?php echo $cromo['artist'];?>&bottom=<?php echo $cromo['bottom'];?>" style="width:250px; border-radius:5%; margin:10px;">
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

       <?php
       $fechaInicioCampActual = getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$loginAux)['ID_CURSO'])[0]['FECHA_INICIO_CAMP_ACTUAL'];
       if ($fechaInicioCampActual!=NULL)
       {
       	echo '<th>Estrellas Campaña Actual</th>';
       }      
       
       if ($fechaInicioCampActual==NULL)
       {
       	echo '<th>Estrellas Total</th>';
       }
       ?>
      
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
      <th>Retos</th><th>Ganas</th><th>Bonus</th><th>Estrellas Total</th>
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
      <th><?php echo (($fechaInicioCampActual==NULL)?"Concursos":"Concursos Actual")?></th>
 <?php
}
 ?>
      <th><?php echo (($fechaInicioCampActual==NULL)?"Retos":"Retos Actual")?></th>

      <th><?php echo (($fechaInicioCampActual==NULL)?"Ganas":"Ganas Actual")?></th>
      <th><?php echo (($fechaInicioCampActual==NULL)?"Bonus":"Bonus Actual")?></th>
      <th><?php echo (($fechaInicioCampActual==NULL)?"Cromos":"Cromos Total")?></th>

<?php
if ($fechaInicioCampActual!=NULL)
       {
       	echo '<th>Estrellas Total</th>';
       }
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

		$fechaInicioCampActual = getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$loginAux)['ID_CURSO'])[0]['FECHA_INICIO_CAMP_ACTUAL'];

		$totalRetosActual = getEstrellasRetosCampActual($dbh,$alumno['CORREO'],0,$fechaInicioCampActual);
		$totalConcursosActual = getEstrellasRetosCampActual($dbh,$alumno['CORREO'],1,$fechaInicioCampActual);

		$totalGanasActual = getEstrellasGanasCampActual($dbh,$alumno['CORREO'],$fechaInicioCampActual);
		$totalBonusActual = getEstrellasBonosCampActual($dbh,$alumno['CORREO'],$fechaInicioCampActual);

		$arrayAlumno['totalRetosActual']=$totalRetosActual;
		$arrayAlumno['totalConcursosActual']=$totalConcursosActual;
		$arrayAlumno['totalGanasActual']=$totalGanasActual;
		$arrayAlumno['totalBonusActual']=$totalBonusActual;

		$totalTotalActual = $totalConcursosActual + $totalRetosActual + $totalGanasActual + $totalBonusActual;

		$arrayAlumno['TotalActual']=$totalTotalActual;//." [".$totalConcursosActual.",".$totalRetosActual.",".$totalGanasActual.",".$totalBonusActual."]";

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
function cmpCampActual($a, $b)
{
    return $b['TotalActual'] - $a['TotalActual'];
}
if ($fechaInicioCampActual!=NULL)
{
	usort($aTotalAlumnos, "cmpCampActual");
}
else
{
	usort($aTotalAlumnos, "cmp");
}


  	 $contador=1;
foreach ($aTotalAlumnos as $alum) 
 {	
		if ($loginAux==$alum['CORREO'])
		{
			echo '<tr>
      <th></th>
      <th>Nombre</th>';
      if ($fechaInicioCampActual!=NULL)
       {
       		echo '<th>Estrellas Campaña Actual</th>';
   		}
   	if ($fechaInicioCampActual==NULL)
       {	
      echo '<th>Estrellas Total</th>';
     }
if ($confAsig=='MENU_SIMPLON_RETOS')
{
echo ''.((Count($aToConcursos)>0)?'<th>Concursos</th>':'').'<th>Retos</th><th>Ganas</th><th>Bonus</th><th>Estrellas Total</th>';
}  
else if ($confAsig!='MENU_BASICA')
{      
     

      echo '<th>Nivel</th>'.((Count($aToConcursos)>0)?'<th>'.(($fechaInicioCampActual==NULL)?"Concursos":"Concursos Actual").'</th>':'').'<th>'.(($fechaInicioCampActual==NULL)?"Retos":"Retos Actual").'</th>
      <th>'.(($fechaInicioCampActual==NULL)?"Ganas":"Ganas Actual").'</th>
      <th>'.(($fechaInicioCampActual==NULL)?"Bonus":"Bonus Actual").'</th>
      <th>'.(($fechaInicioCampActual==NULL)?"Cromos":"Cromos Total").'</th>';
      if ($fechaInicioCampActual!=NULL)
       {
       	echo '<th>Estrellas Total</th>';
       }
}
 echo   '</tr>';
		}


	  	echo '<tr class="table-info">';
	      echo '<th scope="row">'.$contador.'</th>';
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).$alum['Nombre'].finNegrita($loginAux, $alum['CORREO']).'</td>';
	      if ($fechaInicioCampActual!=NULL)
       {
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['TotalActual']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
	    }
	      if ($fechaInicioCampActual==NULL)
       {
       	echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Total']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
       }
	      

if ($confAsig=='MENU_SIMPLON_RETOS')
{
if (Count($aToConcursos)>0)
{
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Concursos']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
}


	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Retos']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Comportamiento']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
		$valorBonus = ($fechaInicioCampActual!=NULL)?$alum['totalBonusActual']:$alum['Suerte'];
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$valorBonus:'').finNegrita($loginAux, $alum['CORREO']).'</td>';

	    if ($fechaInicioCampActual!=NULL)
       	{
       	echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Total']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
       	}


}
else if ($confAsig!='MENU_BASICA')
{      

	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['NivelConSiguiente']:$alum['NivelSinSiguiente']).finNegrita($loginAux, $alum['CORREO']).'</td>';

if (Count($aToConcursos)>0)
{
	      
//totalConcursosActual.",".$totalRetosActual.",".$totalGanasActual.",".$totalBonusActual
			
	      $valorConcursos = ($fechaInicioCampActual!=NULL)?$alum['totalConcursosActual']:$alum['Concursos'];
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$valorConcursos:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
}

		$valorRetos = ($fechaInicioCampActual!=NULL)?$alum['totalRetosActual']:$alum['Retos'];	
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$valorRetos:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
		$valorGanas = ($fechaInicioCampActual!=NULL)?$alum['totalGanasActual']:$alum['Comportamiento'];
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$valorGanas:'').finNegrita($loginAux, $alum['CORREO']).'</td>';

		$valorBonus = ($fechaInicioCampActual!=NULL)?$alum['totalBonusActual']:$alum['Suerte'];
	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$valorBonus:'').finNegrita($loginAux, $alum['CORREO']).'</td>';

	      echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Cromos']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
	    if ($fechaInicioCampActual!=NULL)
       	{
       	echo '<td>'.iniNegrita($loginAux, $alum['CORREO']).(($loginAux==$alum['CORREO'])?$alum['Total']:'').finNegrita($loginAux, $alum['CORREO']).'</td>';
       	}
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
       <?php
       $fechaInicioCampActual = getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$loginAux)['ID_CURSO'])[0]['FECHA_INICIO_CAMP_ACTUAL'];
       if ($fechaInicioCampActual!=NULL)
       {
       	echo '<th>Campaña desde '.(($fechaInicioCampActual==NULL)?'':''.date('Y-m-d', strtotime($fechaInicioCampActual)).'').'</th>';
   		}
      
       if ($fechaInicioCampActual==NULL)
       {
      		echo '<th>Estrellas Total</th>';
  		}
       ?>
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
      <th>Retos</th><th>Ganas</th><th>Bonus</th><th>Estrellas Total</th>
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

<th><?php echo (($fechaInicioCampActual==NULL)?"Concursos":"Concursos Actual")?></th>
 <?php
}
 ?>
      <th><?php echo (($fechaInicioCampActual==NULL)?"Retos":"Retos Actual")?></th>

      <th><?php echo (($fechaInicioCampActual==NULL)?"Ganas":"Ganas Actual")?></th>
      <th><?php echo (($fechaInicioCampActual==NULL)?"Bonus":"Bonus Actual")?></th>
      <th><?php echo (($fechaInicioCampActual==NULL)?"Cromos":"Cromos Total")?></th>


<?php 
if ($fechaInicioCampActual!=NULL)
       {
       	echo '<th>Estrellas Total</th>';
       }	
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
      <th>Día Creación</th>
      <th>Fecha límite</th>
      <!--th>Descripción</th-->
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
  	<?php
//var_dump($aToRetos);
$insertadoTituloCampanaActual=0;
foreach ($aToConcursos as $reto) 
{
		$fechaInicioCampActual = getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$loginAux)['ID_CURSO'])[0]['FECHA_INICIO_CAMP_ACTUAL'];
		$fechaCreacionReto = (($reto['FECHA_CREACION']==NULL)?'-':date('Y-m-d', strtotime($reto['FECHA_CREACION'])));
		if (($fechaInicioCampActual!=NULL)&&($fechaCreacionReto!='-')&&($fechaCreacionReto>$fechaInicioCampActual)&&($insertadoTituloCampanaActual==0))
		{
		echo '<tr class="table-info"><td><strong>Campaña Actual desde ('.date('Y-m-d', strtotime($fechaInicioCampActual)).')</strong></td></tr>';
		$insertadoTituloCampanaActual=1;
		}
		$datosAlumnoTarea = getDatosAlumnoTarea($dbh,$loginAux,$reto['ID']);
		//var_export($datosAlumnoTarea);
	  	echo '<tr class="table-info">';
	      echo '<td title="'.$reto['DESCRIPCION'].'">'.$reto['NOMBRE'].'</td>';
	      echo '<td>'.$datosAlumnoTarea['ESTADO'].'</td>';
echo '<td>'.(($datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']==NULL)?'-':'<b>'.$datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS'].'</b>').'</td>';
	      echo '<td>'.$reto['TOTAL_ESTRELLAS'].'</td>';
	      echo '<td>'.(($datosAlumnoTarea['FECHA']==NULL)?'-':$datosAlumnoTarea['FECHA']).'</td>';
	      echo '<td>'.(($reto['FECHA_CREACION']==NULL)?'-':date('Y-m-d', strtotime($reto['FECHA_CREACION']))).'</td>';
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
<h3>Mis Retos & <span style="color:red;	">Materiales</span></h3>
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>Reto/<span style="color:red;	">Material</span></th>
      <th>Estado</th>
      <th>Estrellas conseguidas</th>
      <th>Máximo estrellas</th>
      <th>Entregado en fecha</th>
      <th>Día Creación</th>
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
$insertadoTituloCampanaActual=0;
foreach ($aToRetos as $reto) 
{
		
	if ($reto['VISIBLE_WEB']==1)
	{
		$fechaInicioCampActual = getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$loginAux)['ID_CURSO'])[0]['FECHA_INICIO_CAMP_ACTUAL'];
		$fechaCreacionReto = (($reto['FECHA_CREACION']==NULL)?'-':date('Y-m-d', strtotime($reto['FECHA_CREACION'])));
		if (($fechaInicioCampActual!=NULL)&&($fechaCreacionReto!='-')&&($fechaCreacionReto>$fechaInicioCampActual)&&($insertadoTituloCampanaActual==0))
		{
		echo '<tr class="table-info"><td><strong>Campaña Actual desde ('.date('Y-m-d', strtotime($fechaInicioCampActual)).')</strong></td></tr>';
		$insertadoTituloCampanaActual=1;
		}
		$retoId = $reto['ID'];
		$datosAlumnoTarea = getDatosAlumnoTarea($dbh,$loginAux,$retoId);
		//var_export($datosAlumnoTarea);

		$paramShow = '\''.$reto['NOMBRE'].'\',\''.$reto['DESCRIPCION'].'\'';
		if ($reto['LINK_DOCUMENTO']=='')
		{

			$linkDocumentos= 'javascript:" onclick="showDesc('.$paramShow .');return false;';
		}
		else
		{
			$linkDocumentos= $reto['LINK_DOCUMENTO'].'" onclick="showDesc('.$paramShow .');';
		}

	  	echo '<tr class="table-info">';
	      
	  	  // no sale si tiene activado el bot, lo tienen que hacer por SalleZ
	      if (!opcionMenuOk($dbh,$_SESSION['alogin'],"Mi bot"))
	      {
	      

	      if (strpos($reto['NOMBRE'], 'MATERIAL'))
	      {
			echo '<td title="'.$reto['DESCRIPCION'].'"><a href="'.$linkDocumentos.'" target="_blank" rel="noopener">[INFO]</a><span style="color:red;	"> '.$reto['NOMBRE'].'</span></td>';
	      }
	      else
	      {

	      	$alumnoTareaYo = getDatosAlumnoTarea($dbh,$loginAux,$retoId);
	$alumnoAcorregir = getAlumnoFromID($dbh,$alumnoTareaYo['ID_ALUMNO_A_CORREGIR']);
	      	$tieneQueCorregir = ($alumnoAcorregir=='')?"":'<a href="corregir_reto.php?idt='.$retoId.'&ida='.getAlumnoFromCorreo($dbh,$loginAux)['ID'].'" target="_blank" rel="noopener"> [CORREGIR]</a>';

	      	echo '<td title="'.$reto['DESCRIPCION'].'"><a href="'.$linkDocumentos.'" target="_blank" rel="noopener">[INFO]</a><a href="entregar_reto.php?act=act&idt='.$retoId.'" target="_blank" rel="noopener"> [ENTREGAS]</a>'.'<a href="evaluar_reto.php?idt='.$retoId.'&ida='.getAlumnoFromCorreo($dbh,$loginAux)['ID'].'" target="_blank" rel="noopener"> [EVALUAR]</a>'.$tieneQueCorregir.' [RETO] '.$reto['NOMBRE'].'</td>';
	      }

	  		}
	  		else
	  		{
	  		echo '<td>'.$reto['NOMBRE'].'</td>';	
	  		}

	      echo '<td>'.(strpos($reto['NOMBRE'], 'MATERIAL')?"-":$datosAlumnoTarea['ESTADO']).'</td>';
echo '<td>'.(($datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']==NULL)?'-':'<b>'.$datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS'].'</b>').'</td>';
	      echo '<td>'.(strpos($reto['NOMBRE'], 'MATERIAL')?"-":$reto['TOTAL_ESTRELLAS']).'</td>';
	      echo '<td>'.(($datosAlumnoTarea['FECHA']==NULL)?'-':$datosAlumnoTarea['FECHA']).'</td>';
	      echo '<td>'.$fechaCreacionReto.'</td>';
	      echo '<td>'.(strpos($reto['NOMBRE'], 'MATERIAL')?"-":(($reto['FECHA_LIMITE']==NULL)?'-':$reto['FECHA_LIMITE'])).'</td>';
	      //echo '<td>'.$reto['DESCRIPCION'].'</td>';
	    echo '</tr>';
	}
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
      <th>Estrellas</th>
      <th>Día</th>
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
  	<?php
$aToCompor = getEstrellasComportamientoFromCorreo($dbh,$loginAux);
$insertadoTituloCampanaActual=0;	
foreach ($aToCompor as $compor) 
{
		$fechaInicioCampActual = getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$loginAux)['ID_CURSO'])[0]['FECHA_INICIO_CAMP_ACTUAL'];
		if (($fechaInicioCampActual!=NULL)&&($compor['DIA']>$fechaInicioCampActual)&&($insertadoTituloCampanaActual==0))
		{
		echo '<tr class="table-info"><td><strong>Campaña Actual desde ('.date('Y-m-d', strtotime($fechaInicioCampActual)).')</strong></td></tr>';
		$insertadoTituloCampanaActual=1;
		}
	  	echo '<tr class="table-info">';
	      echo '<td><b>'.$compor['ESTRELLAS'].'</b></td>';
	      echo '<td>'.$compor['DIA'].'</td>';
	    echo '</tr>';
}


	 ?>
    
  </tbody>
  <!--Table body-->


</table>
<?php
if (($confAsig!='MENU_BASICA'))
{
?>
<h3>Mis Bonus</h3>
<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Estrellas</th>
      <th>Día creación</th>
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
  	<?php
$aToCompor = getBonusFromCorreo($dbh,$loginAux);	
$insertadoTituloCampanaActual=0;
foreach ($aToCompor as $compor) 
{
		$fechaInicioCampActual = getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$loginAux)['ID_CURSO'])[0]['FECHA_INICIO_CAMP_ACTUAL'];
		$fechaCreacionReto = (($compor['FECHA_CREACION']==NULL)?'-':date('Y-m-d', strtotime($compor['FECHA_CREACION'])));
		if (($fechaInicioCampActual!=NULL)&&($fechaCreacionReto!='-')&&($fechaCreacionReto>$fechaInicioCampActual)&&($insertadoTituloCampanaActual==0))
		{
		echo '<tr class="table-info"><td><strong>Campaña Actual desde ('.date('Y-m-d', strtotime($fechaInicioCampActual)).')</strong></td></tr>';
		$insertadoTituloCampanaActual=1;
		}

	  	echo '<tr class="table-info">';
	  	  echo '<td>'.$compor['NOMBRE'].'</td>';
	      echo '<td><b>'.$compor['NUM_ESTRELLAS'].'</b></td>';
	      echo '<td>'.$fechaCreacionReto.'</td>';
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
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
	<script type="text/javascript">
				 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 3000);
					});

	function showDesc(nombreReto,descReto)
	{
		Swal.fire(nombreReto,descReto ,'info');
	}
				 
	</script>
	

</body>
</html>
<?php } ?>