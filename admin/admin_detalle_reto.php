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

if (isset($_GET['idc']))
{
  $_POST['idc']=$_GET['idc'];
}



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
	if (isset($_POST['cbPropio']))
	{
		modificarParesIncognitoAPropio($dbh,$idReto,$aNull);
	}
	else
	{
		modificarParesIncognito($dbh,$idReto,$aNull);
	}
}
$aAlumRetos = getAlumnosTareasFromTarea($dbh,$idReto);
//var_export($aAlumRetos);
if (count($aAlumRetos)==0)
{
	$incognitoNoActivado=true;
}
else
{
	$incognitoNoActivado=(count($aAlumRetos)>1)?(($aAlumRetos[0]['ID_ALUMNO_A_CORREGIR']==NULL)&&($aAlumRetos[1]['ID_ALUMNO_A_CORREGIR']==NULL)):($aAlumRetos[0]['ID_ALUMNO_A_CORREGIR']==NULL);
}


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
<?php 
$reto = getTareaFromID($dbh,$idReto);
//$folder="../retos/".getAsignaturaFromAsignaturaID($dbh,$reto['ID_ASIGNATURA'])['NOMBRE']."/".$reto['NOMBRE'];
?>
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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.2/css/bootstrap2/bootstrap-switch.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css"/>
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
				var inEva = document.getElementById('verEva');
				var inComent = document.getElementById('verComen');
				var aCo = document.getElementById('aCorre');
				var coPo = document.getElementById('correPor');
			
				$(".toggle-vis").bootstrapSwitch();
				var table = $('#zctb').DataTable();

        event.preventDefault();
        var column = table.column($(inEva).attr('data-column'));
        column.visible( false);
        var column = table.column($(inComent).attr('data-column'));
        column.visible( false);
        var column = table.column($(aCo).attr('data-column'));
        column.visible( false);
        var column = table.column($(coPo).attr('data-column'));
        column.visible( false);

}

function cambioNota(a) {
	var maxEstrellas = <?php echo $reto['TOTAL_ESTRELLAS']?>;
	var n = parseFloat(document.getElementById('eccN'+a).value);
	var sVal = "";
	if (isNaN(n))
	{
		document.getElementById('eccc'+a).value = "";
	}
	else
	{
		if (n>10)
		{
			alert('nota entre 0 y 10');
			document.getElementById('eccN'+a).value = Math.trunc(n / 10);
			n = parseFloat(document.getElementById('eccN'+a).value);
		}
		document.getElementById('eccc'+a).value = Math.round((n*maxEstrellas)/10);
	}
	

}


</script>
</head>

<body onload="init()">
<form id="form1" action="admin_detalle_reto.php" method="post">


<input type='hidden' name='actualizarEs' id='actualizarEs' value='0'/>
<input type='hidden' name='incognito' id='incognito' value='0'/>
  		<input type="hidden" name="idr" value="<?php echo $idReto;?>"/>
 		<input type="hidden" name="idc" value="<?php echo $_POST['idc'];?>"/>
  		  		<input type="hidden" id="idAlumn" name="idAlumn"/>

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
<h3>Corrección incognito: por pares <input type="checkbox" id="cbPares" name="cbPares" onchange="manageCbPares();" <?php if(isset($_POST['cbPares'])) { echo 'checked="checked"'; } ?>/> al propio <input type="checkbox" id="cbPropio" name="cbPropio" /></h3>

</div>
 <div class="form-group">
  <a onclick="actualizarEstrellas();" class="btn btn-success btn-outline btn-wrap-text">Actualizar estrellas conseguidas</a>
</div>

     <input id="verEva" name="verEva"
      type="checkbox" 
      data-column="6" 
      class="toggle-vis" 
      data-label-text="Evaluación" />
<input id="verComen" name="verComen"
      type="checkbox" 
      data-column="7" 
      class="toggle-vis" 
      data-label-text="Comentario" />
      <?php
      if (!$incognitoNoActivado)
  	{
  		?>
<input id="aCorre" name="aCorre"
      type="checkbox" 
      data-column="8" 
      class="toggle-vis" 
      data-label-text="Acorregir" />
<input id="correPor" name="CorrePor"
      type="checkbox" 
      data-column="9" 
      class="toggle-vis" 
      data-label-text="CoPor" />
      <?php
      }



$aIdsClan = getIdsClanFromIdCurso($dbh,$_POST['idc']);

$aIdClanMedia = array ();
foreach ($aIdsClan as $idClan) 
{
	$med = getMediaFromRetoIdClanId($dbh,$idReto,$idClan['ID_CLAN']);
	$aIdClanMedia[$idClan['ID_CLAN']]=$med;
}


arsort($aIdClanMedia);


$cont =1;
foreach ($aIdClanMedia as $idClan => $media) 
{
	echo $cont."º.-<b>".getClanFromClanId($dbh,$idClan)['NOMBRE']."</b>(".$media.") ";
	$cont++;
}

  		?>

<h3>Alumnos</h3>
<table ID="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
  <!--Table head-->
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Apellidos</th>
      <th>Clan</th>
      <th>Estado</th>
      <th>Nota</th>
      <th>Estrellas</th>   
      <th>Evaluación</th>
      <th>Comentario</th>
      <?php echo ($incognitoNoActivado)?"":"<th data-toggle='tooltip' title='a corregir de incognito' >A corregir</th>"?>
      <?php echo ($incognitoNoActivado)?"":"<th data-toggle='tooltip' title='corregido por' >Corregido por</th>"?>
      <th>Entregado en fecha</th>
      <!--th>Descripción</th-->
    </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
    <?php

function calcularNota($estrellas,$reto)
{
	$maxEstrellas = $reto['TOTAL_ESTRELLAS'];
	return ($estrellas * 10)/(($maxEstrellas>0)?$maxEstrellas:1);
}

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
         echo '<td><a data-toggle="tooltip" title="Calificar reto al alumno" href="admin_cromos.php?ida='.$alumno['CORREO'].'&idr='.$idReto.'" target="_blank">'.$alumno['APELLIDO1'].' '.$alumno['APELLIDO2'].', '.$alumno['NOMBRE'].'</a></td>';
         $clanCero = getMediaFromRetoIdClanId($dbh,$idReto,$clan['ID']);
         if ($clanCero!=0)
         {
         	echo '<td>'.$nombreClan.'(M='.$clanCero.')</td>';
         }
         else
         {
         	echo '<td>'.$nombreClan.'</td>';
         }

         $bgColor = "white";
         if ($datosAlumnoTarea['ESTADO']=='corregido')
         {
         		$bgColor = "#b9fbc0";
         }
         else if ($datosAlumnoTarea['ESTADO']=='entregado')
         {
						$bgColor = "#f4a261";
         }
         
       echo '<td style="background-color: '.$bgColor.';" >'.$datosAlumnoTarea['ESTADO'].'</td>';
       echo '<td><input type="number" min="0" max="10" placeholder="[0..10]" name="eccN'.$alumno['ID'].'" id="eccN'.$alumno['ID'].'" class="form-control" value="'.calcularNota(($datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']==NULL)?'0':$datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS'],$reto).'" onKeyUp="cambioNota('.$alumno['ID'].')" step=".01"></td>';
echo '<td><input style="font-weight: bold;" class="form-control" type="text" name="eccc'.$alumno['ID'].'" id="eccc'.$alumno['ID'].'" readonly="readonly" value="'.(($datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']==NULL)?'-':$datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']).'"/></td>';
        echo '<td><div class="pull-left">'.$textoEval.'</div>
  <div class="pull-right"><button onclick="borrarEva('.$alumno['ID'].')" class="btn btn-warning btn-xs" name="bBorrar">borrar eva</button></div></td>';
  echo '<td>'.$datosAlumnoTarea['COMENTARIO'].'</td>';
  	$nombreACorregir="";
  	if (!$incognitoNoActivado)
  	{
  		$alToCo = getAlumnoFromID($dbh,$datosAlumnoTarea['ID_ALUMNO_A_CORREGIR']);
  		$nombreACorregir = $alToCo['NOMBRE']." ".$alToCo['APELLIDO1']." ".$alToCo['APELLIDO2'];
  		//coger datos de alumno tarea de alToCo y coger la nota corregida y el comentario

  		$alumnoCorrector = getAlumnoFromID($dbh,getCorrectorAlumnoTarea($dbh,$alumno['ID'],$idReto)['ID_ALUMNO']);
  		$nombreCorrector = $alumnoCorrector['NOMBRE']." ".$alumnoCorrector['APELLIDO1']." ".$alumnoCorrector['APELLIDO2'];
  		$datosAlumnoTareaToCo = getDatosAlumnoTarea($dbh,$alumnoCorrector['CORREO'],$idReto);

  		$nota = $datosAlumnoTareaToCo['NOTA_CORREGIDA'];
  		$comentCo = $datosAlumnoTareaToCo['COMENT_CORRECCION'];
  		$diferencia =" ";
  		if (($datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS']!=NULL)&&($nota != NULL))
  		{
  			$diferencia =calcularNota($datosAlumnoTarea['ESTRELLAS_CONSEGUIDAS'],$reto)-$nota;
  			if ($diferencia==0)
  			{
  				$diferencia =number_format($diferencia, 2) .'&nbsp;<i class="fa fa-thumbs-up" style="color:green;"></i>';
  			}
  			else if ($diferencia>0)
  			{
  				$diferencia =number_format($diferencia, 2) .'&nbsp;<i class="fa fa-arrow-down"</i>';
  			}
  			else
  			{
					$diferencia =(-1*number_format($diferencia, 2)) .'&nbsp;<i class="fa fa-arrow-up" style="color:red;"</i>';
  			}
  		}
  		 
  	}
   echo ($incognitoNoActivado)?"":"<td data-toggle='tooltip' title='".$nombreACorregir."' >".$nombreACorregir."</td>";
   echo ($incognitoNoActivado)?"":"<td data-toggle='tooltip' title='".$comentCo."' >".(($comentCo=='')?"<span style='color:#FF0000';>FALTA</span>":$nota)." (".$nombreCorrector.")".$diferencia."</td>";
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
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-switch/3.3.4/js/bootstrap-switch.js" data-turbolinks-track="true"></script>

	<script type="text/javascript">

$(function(){

    $(".toggle-vis").bootstrapSwitch();
		var table = $('#zctb').DataTable();
    $('.toggle-vis').on('switchChange.bootstrapSwitch', function(event, state) {
        event.preventDefault();
        var column = table.column($(this).attr('data-column'));
        column.visible( ! column.visible() );
    });
});



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
 if (document.getElementById('cbPropio').checked)
 {
 	alert("Cada alumno se corregirá a sí mismo")
 }
 
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