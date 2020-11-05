<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
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
  $idCur=$_POST['idCursoHid'];
}

if (isset($_POST['texto']))
{
  $clase = getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE'];        
        mandarNotificacion($dbh,'Admin',$clase,$_POST['texto']);
  $msg="Notificación enviada correctamente correctamente";
  
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
<script type="text/javascript">
  function managebuttonB()
  {
      document.getElementById("form2").action="admin_ranking.php";
      document.getElementById("form2").submit(); 
  }

</script>

</head>

<body>


                <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading">Enviar Notificación a todo <?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE']?></div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>
  <form id="form2" method="post" action="admin_cromos.php">
      <input type='hidden' name='idCursoHid' id='idCursoHid' value='<?php echo $idCur?>'/>
    <br/>

<div class="form-group">
<div class="col-sm-12">
<input type="text" name="texto" maxlength = "120" class="form-control" required value="">
</div>
</div>

<div class="form-group">
<div class="col-sm-12">
</div>
<div class="col-sm-12">
</div>
<div class="col-sm-12">
</div>
</div>
    <div class="form-group col-md-4">
      <a onclick="managebuttonB()"  class="btn btn-danger btn-outline btn-wrap-text">Enviar Notificación</a>
    </div>
    <br/><br/><br/>

  </form>
               </div>
                </div>



<h3>Ranking de curso/clase (<?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE']?>)</h3>

<table class="table table-striped w-auto table-bordered">

  <!--Table head-->
  <thead>
    <tr>
      <th>#</th>
      <th>Nombre</th>
      <th>Nivel</th>
      <th>Total Estrellas</th>
<?php
$aAlumnosCurso = getAlumnosFromCursoID($dbh,$idCur);

$aToConcursos = getTareasTotalesFromAlumno($dbh,$aAlumnosCurso[0]['CORREO'],1);

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
      <th>¿Clan?</th>
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
    <?php
     
     //var_dump($aAlumnosCurso);

     $aClanes = array();
     $aClanesIntroducidos = array ();
     $aTotalAlumnos = array();
     foreach ($aAlumnosCurso as $alumno) 
     {
      $arrayAlumno=array();
      $clanId = getClanIdFromAlumnoId($dbh,$alumno['ID']);
      if ($clanId!="")
      {
        $arrayAlumno['TIENE_CLAN'] = "SI";
      }
      else
      {
        $arrayAlumno['TIENE_CLAN'] = "NO";
      }
      if (($clanId!="")&&(!in_array($clanId, $aClanesIntroducidos)))
      {
        $aAlumnosIdClan = getAlumnosIdFromClanId($dbh,$clanId);
        $clan = getClanFromClanId($dbh,$clanId);
        $aClanesIntroducidos[] = $clanId;
        $aClanAlumno = array();
        $aClanAlumno['ID_CLAN']= $clan['ID'];
        $aClanAlumno['NOMBRE']= $clan['NOMBRE'];
        $aClanAlumno['IMAGEN']= $clan['IMAGEN'];
        $aClanAlumno['DESCRIPCION']= $clan['DESCRIPCION'];
        $aClanAlumno['ALUMNOS_CLAN']= $aAlumnosIdClan;
        $aClanes[] = $aClanAlumno;
        
      }
      
      
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
$totalConcursos = getEstrellasRetos($dbh,$alumno['CORREO'],1);
  // calculo de ganas


$totalComportamiento =getEstrellasGanas($dbh,$alumno['CORREO']);


      
      $totalCromos = $estrellasCromos+$estrellasCombinaciones;
      $totalSuerte = getEstrellasBonos($dbh,$alumno['CORREO']);
      $totalTotal = $totalRetos+$totalConcursos+$totalComportamiento+$totalCromos+$totalSuerte;
      

    $arrayAlumno['Nombre']=$alumno['NOMBRE'].' '.$alumno['APELLIDO1'].' '.$alumno['APELLIDO2'];
    $arrayAlumno['Id']=$alumno['ID'];
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
      $textoSiguienteNivel = " [Siguiente nivel a ".$estrellasFaltanSiguienteNivel  ." estrellas]";
    }
    
    $arrayAlumno['NivelSinSiguiente']=$alumno['NUMERO_NIVEL']." (".getNivelFromNumeroNivel($dbh,$alumno['CORREO'],$alumno['NUMERO_NIVEL'])['NOMBRE'].")";
    $arrayAlumno['NivelConSiguiente']=$alumno['NUMERO_NIVEL']." (".getNivelFromNumeroNivel($dbh,$alumno['CORREO'],$alumno['NUMERO_NIVEL'])['NOMBRE'].")".$textoSiguienteNivel;
    $arrayAlumno['CORREO']=$alumno['CORREO'];
    $aTotalAlumnos[]=$arrayAlumno;  


      
     }
     //var_export($aClanes);
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





        echo '<td><a  data-toggle="tooltip" title="Ver detalle del reto en otra ventana" href="../ranking.php?l='. $alum['CORREO'].'" target=”_blank”>'.$alum['Nombre'].'</a></td>';
        echo '<td>'.$alum['NivelSinSiguiente'].'</td>';
        echo '<td>'.$alum['Total'].'</td>';

if (Count($aToConcursos)>0)
{
        echo '<td>'.$alum['Concursos'].'</td>';
}       
        echo '<td>'.$alum['Retos'].'</td>';
        echo '<td>'.$alum['Comportamiento'].'</td>';
        echo '<td>'.$alum['Cromos'].'</td>';
        echo '<td>'.$alum['Suerte'].'</td>';
        echo '<td>'.$alum['TIENE_CLAN'].'</td>';
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
      <th>Nivel</th>
      <th>Total Estrellas</th>
<?php
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
      <th>¿Clan?</th>
    </tr>
  </thead>

</table>



<h3>Concursos</h3>
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

$aToRetos = getTareasTotalesFromCurso($dbh,$idCur);
//var_dump($aToRetos);
foreach ($aToRetos as $reto) 
{
    if ($reto['EXAMEN']==1)
    {
      echo '<tr class="table-info">';
      ?>
      <td><a  data-toggle="tooltip" title="Ver detalle del reto en otra ventana" href="admin_detalle_reto.php?idr=<?php echo $reto['ID']?>" target=”_blank”><?php echo $reto['NOMBRE'];?></a></td>
       <?php
        echo '<td>'.$reto['TOTAL_ESTRELLAS'].'</td>';
        echo '<td>'.(($reto['FECHA_LIMITE']==NULL)?'-':$reto['FECHA_LIMITE']).'</td>';
        echo '<td>'.$reto['DESCRIPCION'].'</td>';
        echo '<td>'.(($reto['LINK_DOCUMENTO']==NULL)?'-':$reto['LINK_DOCUMENTO']).'</td>';
      echo '</tr>';
    }
}

   ?>
    
  </tbody>
  <!--Table body-->


</table>
<h3>Retos</h3>
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
//var_dump($aToRetos);
foreach ($aToRetos as $reto) 
{
    if ($reto['EXAMEN']!=1)
    {
      echo '<tr class="table-info">';

?>
<td><a  data-toggle="tooltip" title="Ver detalle del reto en otra ventana" href="admin_detalle_reto.php?idr=<?php echo $reto['ID']?>" target=”_blank”><?php echo $reto['NOMBRE'];?></a></td>
<?php
        echo '<td>'.$reto['TOTAL_ESTRELLAS'].'</td>';
        echo '<td>'.(($reto['FECHA_LIMITE']==NULL)?'-':$reto['FECHA_LIMITE']).'</td>';
        echo '<td>'.$reto['DESCRIPCION'].'</td>';
        echo '<td>'.(($reto['LINK_DOCUMENTO']==NULL)?'-':$reto['LINK_DOCUMENTO']).'</td>';
      echo '</tr>';
    }
}

   ?>
    
  </tbody>
  <!--Table body-->


</table>


<h3>Clanes</h3>

<?php
echo "<ol>";
foreach ($aClanes as $clan) 
{
  echo "<li><b>".$clan['NOMBRE']."</b>(".$clan['DESCRIPCION'].")</li>";
  $aac = $clan['ALUMNOS_CLAN'];
  echo "<ol>";
  for ($i=0; $i < Count($aac); $i++) 
  { 
    //var_export($aac[$i]);
    $ali = getAlumnoFromId($dbh,$aac[$i]['ID_ALUMNO']);
    echo "<li>".$ali['NOMBRE'].' '.$ali['APELLIDO1'].' '.$ali['APELLIDO2']."</li>";
  }
  echo "</ol>";
}
echo "</ol>";
?>
 <h3>Cambio cromos</h3>

<form action="admin_cambio_cromos.php" id="form3" method="post">

                            <div class="form-group">
                            
                            <label class="col-sm-1 control-label">ALUMNO1<span style="color:red">*</span></label>
                            <div class="col-sm-5">
  <select class="form-control col-md-2" ID="sAlumno1" name="sAlumno1">
  <?php
    foreach ($aTotalAlumnos as $alum) 
    {
      echo "<option value='".$alum['Id']."'>".$alum['Nombre']."</option>";
    }
  ?>
  </select>
                            </div>
                           
                             

                            <label class="col-sm-1 control-label">ALUMNO2<span style="color:red">*</span></label>
                            <div class="col-sm-5">
  <select class="form-control col-md-2" ID="sAlumno2" name="sAlumno2">
  <?php
    foreach ($aTotalAlumnos as $alum) 
    {
      echo "<option value='".$alum['Id']."'>".$alum['Nombre']."</option>";
    }
  ?>
  </select>
                            </div>
                            </div>



      <div class="form-group col-md-2">
      <a onclick="document.getElementById('form3').submit();"  class="btn btn-warning btn-outline btn-wrap-text">Cambiar</a>
    </div>
</form>
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