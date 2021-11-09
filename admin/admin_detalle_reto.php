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

//var_export($_POST);

$idReto="";
if (isset($_GET['idr']))
{
  $idReto=$_GET['idr'];
}
else if (isset($_POST['idr']))
{
  $idReto=$_POST['idr'];
  if (isset($_POST['bBorrar']))
  {
  	$aex = getAutoevaluacion($dbh,$idReto,$_POST['idAlumn']);
  	$idAlu = $_POST['idAlumn'];
  	borrarAutoevaluacion($dbh,$idAlu,$idReto);
  	borrarAutoevaluacionAlumnos($dbh,$aex['ID']);
  }
}
if (isset($_POST['incognito'])&&($_POST['incognito']=='A'))
{
	$aNull=true;
	if (isset($_POST['cbPares']))
	{
		$aNull=false;
	}
	modificarParesIncognito($dbh,$idReto,$aNull);
}
$aAlumRetos = getAlumnosTareasFromTarea($dbh,$idReto);
$incognitoNoActivado=(count($aAlumRetos)>1)?(($aAlumRetos[0]['ID_ALUMNO_A_CORREGIR']==NULL)&&($aAlumRetos[1]['ID_ALUMNO_A_CORREGIR']==NULL)):($aAlumRetos[0]['ID_ALUMNO_A_CORREGIR']==NULL);

if (isset($_POST['actualizarEs'])&&($_POST['actualizarEs']=='A'))
{
	foreach($_POST as $key => $value)
	{
	    if ((substr( $key, 0, 4 ) === "eccc")&& is_numeric($value))
	    {
	    	$idAlu = substr($key, 4, strlen($key) - 4);
	    	$correoAlumno = getAlumnoFromID($dbh,$idAlu)['CORREO'];
modificarEstadoReto($dbh,$correoAlumno,$idReto,'corregido');
  modificarEstrellasConseguidasReto($dbh,$correoAlumno,$idReto,$value);

	    }
	    else if ((substr( $key, 0, 4 ) === "eccc")&& ($value=="-"))
	    {
	    	$idAlu = substr($key, 4, strlen($key) - 4);
	    	$correoAlumno = getAlumnoFromID($dbh,$idAlu)['CORREO'];
  			modificarEstrellasConseguidasReto($dbh,$correoAlumno,$idReto,'NULL');
	    }
	}
	

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
<script type="text/javascript">
	function borrarEva(idAlumn)
	{ 
		//document.getElementById("form1").action="mimercado.php";
      	document.getElementById("idAlumn").value=idAlumn;
      	document.getElementById("form1").submit(); 

	}

function actualizarEstrellas()
  {
    document.getElementById('actualizarEs').value = 'A';
    document.getElementById('form1').submit();  
  }
function init(argument) {
	<?php
	if ($incognitoNoActivado)
	{
		echo 'document.getElementById("cbPares").checked=false;';
	}
	else
	{
		echo 'document.getElementById("cbPares").checked=true;';
	}
	?>
}
</script>
</head>

<body onload="init()">
<form id="form1" action="admin_detalle_reto.php" method="post">


<input type='hidden' name='actualizarEs' id='actualizarEs' value='0'/>
<input type='hidden' name='incognito' id='incognito' value='0'/>
  		<input type="hidden" name="idr" value="<?php echo $idReto;?>"/>
  		  		<input type="hidden" id="idAlumn" name="idAlumn"/>
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
 <div class="form-group">
<h3>Correción incognito por pares <input type="checkbox" id="cbPares" name="cbPares" onchange="manageCbPares();" <?php if(isset($_POST['cbPares'])) { echo 'checked="checked"'; } ?>/></h3>

</div>
 <div class="form-group">
  <a onclick="actualizarEstrellas();" class="btn btn-success btn-outline btn-wrap-text">Actualizar estrellas conseguidas</a>
</div>
<h3>Alumnos</h3>
<table ID="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
  <!--Table head-->
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Apellidos</th>
      <th>Clan</th>
      <th>Estado</th>
      <th>Estrellas conseguidas</th>      
      <th>Evaluación</th>
      <th>Comentario</th>
      <?php echo ($incognitoNoActivado)?"":"<th data-toggle='tooltip' title='a corregir de incognito' >?</th>"?>
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

$autoEva = getAutoevaluacion($dbh,$idReto,$alumno["ID"]);

$alumnosEvaluados = getAutoEvaAlumnos($dbh,$autoEva["ID"]);
$textoEval = "";
$textoOtras = "";
$textoOtrasNombres = "";

foreach ($alumnosEvaluados as $alumnoEva) 
{
	$alumnoTo = getAlumnoFromID($dbh,$alumnoEva["ID_ALUMNO"]);
	if ($alumnoEva["ID_ALUMNO"]==$alumno["ID"])
	{
		$textoOtras = "<b>[[".$alumnoEva["NOTA"]."]]</b>	".$textoOtras;
	}
	else
	{
		$textoEval = $textoEval."[".$alumnoTo["NOMBRE"]." ".$alumnoTo["APELLIDO1"].":<b>".$alumnoEva["NOTA"]."</b>]";

		$autoEvaOtra = getAutoevaluacion($dbh,$idReto,$alumnoTo["ID"]);
		$alumnosEvaluadosOtra = getAutoEvaAlumnos($dbh,$autoEvaOtra["ID"]);
		foreach ($alumnosEvaluadosOtra as $alumnoEvaOtra) 
		{
			if ($alumnoEvaOtra["ID_ALUMNO"]==$alumno["ID"])
			{
				$alumnoToOtra = getAlumnoFromID($dbh,$alumnoEvaOtra["ID_ALUMNO"]);
				$textoOtras = $textoOtras."<b>[".$alumnoEvaOtra["NOTA"]."]</b>";
				$textoOtrasNombres = $textoOtrasNombres."(".$alumnoTo["NOMBRE"]." ".$alumnoTo["APELLIDO1"].")";

			}
		}

	}
}
$textoEval = $textoOtras .$textoOtrasNombres." <span style='color:#FF0000';>SU COEVA:</span> " .$textoEval ;
if ((count($alumnosEvaluados)==0)||($alumnoEva["NOTA"]==-1))
{
	$textoEval = "<span style='color:#FF0000';>FALTA</span>";
}
$datosAlumnoTarea = getDatosAlumnoTarea($dbh,$alumno['CORREO'],$idReto);
    //var_export($datosAlumnoTarea);

$clan = getClanFromCorreo($dbh,$alumno['CORREO']);
$nombreClan = ($clan==NULL)?"z(No Tiene)":$clan['NOMBRE'];
      echo '<tr class="table-info">';
        echo '<td><a data-toggle="tooltip" title="Calificar reto al alumno" href="admin_cromos.php?ida='.$alumno['CORREO'].'&idr='.$idReto.'" target="_blank">'.$alumno['NOMBRE'].'</a></td>';
         echo '<td><a data-toggle="tooltip" title="Calificar reto al alumno" href="admin_cromos.php?ida='.$alumno['CORREO'].'&idr='.$idReto.'" target="_blank">'.$alumno['APELLIDO1'].' '.$alumno['APELLIDO2'].'</a></td>';
         echo '<td>'.$nombreClan.'</td>';

       echo '<td>'.$datosAlumnoTarea['ESTADO'].'</td>';
echo '<td><input style="font-weight: bold;" class="form-control" type="text" name="eccc'.$alumno['ID'].'" value="'.(($datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']==NULL)?'-':$datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']).'"/></td>';
        echo '<td><div class="pull-left">'.$textoEval.'</div>
  <div class="pull-right"><button onclick="borrarEva('.$alumno['ID'].')" class="btn btn-warning btn-xs" name="bBorrar">borrar eva</button></div></td>';
  echo '<td>'.$datosAlumnoTarea['COMENTARIO'].'</td>';
  	$nombreACorregir="";
  	if (!$incognitoNoActivado)
  	{
  		$alToCo = getAlumnoFromID($dbh,$datosAlumnoTarea['ID_ALUMNO_A_CORREGIR']);
  		$nombreACorregir = $alToCo['NOMBRE']." ".$alToCo['APELLIDO1']." ".$alToCo['APELLIDO2'];
  	}
   echo ($incognitoNoActivado)?"":"<td data-toggle='tooltip' title='".$nombreACorregir."' >?</td>";
        echo '<td>'.(($datosAlumnoTarea['FECHA']==NULL)?'-':$datosAlumnoTarea['FECHA']).'</td>';        
        //echo '<td>'.$reto['DESCRIPCION'].'</td>';
      echo '</tr>';




}

   ?>
  </tbody>
  <!--Table body-->


</table>




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

$('#zctb').DataTable( {
    scrollY: 600,
    paging: false,
    scrollCollapse: true,
    "order": [[ 1, "asc" ]]
} );

				 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 3000);
					});
	 function manageCbPares()
  {
    
  	Swal.fire({
          title: '¿Seguro que quieres cambiarlo?',
          text: "Se modificarán todos los pares de corrección incognitos",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, lo modifico!'
        }).then((result) => {
          if (result.value) {
 document.getElementById('incognito').value = 'A';         	
document.getElementById("form1").submit();
          }
          else
          {

          	document.getElementById("cbPares").checked = !document.getElementById("cbPares").checked;
          }
        });
    
  }			 
	</script>
	

</form>
</body>
</html>
<?php 

} 
?>