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
if (isset($_POST['idr']))
{
  $idReto=$_POST['idr'];
}


//var_export($_POST);
 
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
	
	<title>Corregir reto</title>

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
function getDiferenciaDates($date2,$date1)
{
    // Formulate the Difference between two dates
    $date1 = strtotime($date1);  
    $date2 = strtotime($date2);  
    $diff = abs($date2 - $date1);  
// To get the year divide the resultant date into 
// total seconds in a year (365*60*60*24) 
$years = floor($diff / (365*60*60*24));  
// To get the month, subtract it with years and 
// divide the resultant date into 
// total seconds in a month (30*60*60*24) 
$months = floor(($diff - $years * 365*60*60*24) 
                               / (30*60*60*24));  
// To get the day, subtract it with years and  
// months and divide the resultant date into 
// total seconds in a days (60*60*24) 
$days = floor(($diff - $years * 365*60*60*24 -  
             $months*30*60*60*24)/ (60*60*24)); 
// To get the hour, subtract it with years,  
// months & seconds and divide the resultant 
// date into total seconds in a hours (60*60) 
$hours = floor(($diff - $years * 365*60*60*24  
       - $months*30*60*60*24 - $days*60*60*24) 
                                   / (60*60));  
// To get the minutes, subtract it with years, 
// months, seconds and hours and divide the  
// resultant date into total seconds i.e. 60 
$minutes = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24  
                          - $hours*60*60)/ 60);  
// To get the minutes, subtract it with years, 
// months, seconds, hours and minutes  
$seconds = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24 
                - $hours*60*60 - $minutes*60));   
// Print the result 
//printf("%d years, %d months, %d days, %d hours, "
//     . "%d minutes, %d seconds", $years, $months, 
//             $days, $hours, $minutes, $seconds);
$sDife = "";
if ($years>0)
{
   $sDife .= $years." años ";
}
if ($months>0)
{
   $sDife .= $months." meses ";
}
if ($days>0)
{
   $sDife .= $days." días ";
}
if ($hours>0)
{
   $sDife .= $hours." horas ";
}
if ($minutes>0)
{
   $sDife .= $minutes." minutos ";
}
if ($seconds>0)
{
   $sDife .= $seconds." segundos";
}

return $sDife;
}

$reto = getTareaFromID($dbh,$idReto);




//$folder="../retos/".getAsignaturaFromAsignaturaID($dbh,$reto['ID_ASIGNATURA'])['NOMBRE']."/".$reto['NOMBRE'];
?>
<h3><?php echo $reto['NOMBRE'];?></h3>

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


<form method="post" action="admin_corregir_reto.php" class="form-horizontal" enctype="multipart/form-data" name="form1" id="form1">
<input type="hidden" name="idr" value="<?php echo $idReto;?>">

<h2><b>Alumnos</b></h2>
<h3>Ver entregas anteriores <input type="checkbox" id="cbAnteriores" name="cbAnteriores" onchange="manageCbAnteriores();" <?php if(isset($_POST['cbAnteriores'])) { echo 'checked="checked"'; } ?>/></h3>


    <?php



 $alumnos = getAlumnosFromAsignaturaID($dbh,$reto['ID_ASIGNATURA']);
 $filaTarea = getTareaFromID($dbh,$reto['ID']);
//var_dump($aToRetos);
foreach ($alumnos as $alumno) 
{
$datosAT = getDatosAlumnoTarea($dbh,$alumno['CORREO'],$reto['ID']);
$numeroEntregas=$datosAT['NUMERO_ENTREGAS']+1;

$patronPreFix= $alumno['NOMBRE'].'_'.$alumno['APELLIDO1'].'_'.$alumno['APELLIDO2'];

$folder="../retos/".getAsignaturasFromCurso($dbh,$alumno['ID_CURSO'])[0]['NOMBRE']."/".$reto['NOMBRE'];
$files = glob($folder."/".$patronPreFix."*");
rsort($files);
$numEntregasInterador = $numeroEntregas-1;
if ($numEntregasInterador>0)
{
    if (count($files)>0)
    {
    $postIniFecha = strpos($files[0], "__")+2;
    $time = strtotime(substr($files[0], $postIniFecha, 19));
        $newformat = date('Y-m-d H:i:s',$time);
        $entregaFueraTiempo2= ($newformat>$filaTarea['FECHA_LIMITE']);
        if ($filaTarea['FECHA_LIMITE']==NULL)
{
$entregaFueraTiempo2= false;
}
$sDiferenciaFueraTiempo2 = "";
if ($entregaFueraTiempo2)
{
    $sDiferenciaFueraTiempo2 = getDiferenciaDates($newformat,$filaTarea['FECHA_LIMITE']);
}
    
   

    $nombreAl = "<b style='font-size:2vw'>".$alumno['NOMBRE'].' '.$alumno['APELLIDO1'].' '.$alumno['APELLIDO2']."</b>".(($datosAT['ESTRELLAS_CONSEGUIDAS']==NULL)?'':'<b> (Ya calificado con '.$datosAT['ESTRELLAS_CONSEGUIDAS'].' estrellas)</b>')."<br/>";
    echo '<a data-toggle="tooltip" title="Calificar reto al alumno" href="admin_cromos.php?ida='.$alumno['CORREO'].'&idr='.$idReto.'" target="_blank">'.$nombreAl.'</a>';
    echo "Entrega ".$numEntregasInterador."ª (última)".(($entregaFueraTiempo2)?"<b style='color:red'> [entrega fuera de tiempo por ".$sDiferenciaFueraTiempo2."] </b>":"")." <br/>";
}



$entregasAnteriores=false;
if ($numEntregasInterador<10)
{
    $numEntregasInterador="0".$numEntregasInterador;
}
$patronEntregas="_".$numEntregasInterador."__";
foreach ($files as $ficheroI) 
{
    
    $contienePatronF = strpos($ficheroI, $patronEntregas);
    if (!$contienePatronF)
    {
        $numEntregasInterador--;
        $textoNumEntregasInterador = $numEntregasInterador;
        if ($numEntregasInterador<10)
        {
            $numEntregasInterador="0".$numEntregasInterador;
        }
        $patronEntregas="_".$numEntregasInterador."__";
        $postIniFecha = strpos($ficheroI, "__")+2;
       
        $time = strtotime(substr($ficheroI, $postIniFecha, 19));
        $newformat = date('Y-m-d H:i:s',$time);
        $entregaFueraTiempo2= ($newformat>$filaTarea['FECHA_LIMITE']);
        if ($filaTarea['FECHA_LIMITE']==NULL)
{
$entregaFueraTiempo2= false;
}
        $sDiferenciaFueraTiempo2 = "";
        if ($entregaFueraTiempo2)
        {
            $sDiferenciaFueraTiempo2 = getDiferenciaDates($newformat,$filaTarea['FECHA_LIMITE']);
        }
        if (!$entregasAnteriores)
        {
            if (!isset($_POST['cbAnteriores']))
            {
              break;
            }
            echo "<b style='font-size:1vw' >ENTREGAS ANTERIORES</b><br/>";
            $entregasAnteriores=true;
        }
        //var_export($newformat);
        echo "Entrega ".$textoNumEntregasInterador."ª".(($entregaFueraTiempo2)?"<b style='color:red'> [entrega fuera de tiempo por ".$sDiferenciaFueraTiempo2."] </b>":"")." <br/>";

    }

    echo '<a href="'.$ficheroI.'" target="_blank" rel="noopener">'.basename($ficheroI).'</a><br/>';
  
}
}
}

   ?>
      


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
  function manageCbAnteriores()
  {
    document.getElementById("form1").submit();
  }
	</script>
	


</body>
</html>
<?php 

} 
?>