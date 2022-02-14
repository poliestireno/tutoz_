<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
$msg="";

//guardar los ids de las respuestas correctas para que no sume correcta al hacer f5

if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{	
	header('location:index.php');
}
else{

//var_export($_POST);	
if (isset($_POST['ids_preguntas']))
{
$aIdsPreguntas = explode(",", $_POST['ids_preguntas']);	


$aColorB4 = array("primary","secondary","success","danger","warning","info","dark");

$CORREO = $_SESSION['alogin'];	
$alumnoDB = getAlumnoFromCorreo($dbh,$CORREO);
$fastTestActivo = getFastestActivo($dbh,getAsignaturasFromCurso($dbh,$alumnoDB['ID_CURSO'])[0]['ID']);

//var_export($fastTestActivo);

function respuestaOk($respDB,$resUsu)
{  
  $aRespDB = explode(",", $respDB);
  foreach ($aRespDB as $respuestaI) 
  {
    if (strcasecmp($respuestaI,$resUsu) == 0)
    {
      return true;
    }
  }
  return false;
}

	$idAsignatura = getAsignaturasFromCurso($dbh,$alumnoDB['ID_CURSO'])[0]['ID'];
	$aPreguntas = getPreguntasFromAsignaturaID($dbh,$idAsignatura);
	if (Count($aPreguntas)==0)
	{
		$aPreguntas = getPreguntasTotal($dbh); 
	}
$aTestAlumno = getTestAlumno($dbh,$fastTestActivo['ID'],$alumnoDB['ID']);
$respuestaAnteriorOk=false;
$bPuntuar = true;
if ($fastTestActivo)
{
	$idPreguntaContestada = $aIdsPreguntas[Count($aIdsPreguntas)-1];
	$aPre33 = explode(",",$aTestAlumno['PREGUNTAS']);
	$idUltimaPregunta = $aPre33[count($aPre33)-1];
	if ($idPreguntaContestada==$idUltimaPregunta)
	{
		$bPuntuar = false;
	}
}

if (($_POST['respuestaUsu']!="SABESQUESI")&&($fastTestActivo)&&$bPuntuar)
{


	$idPreguntaContestada = $aIdsPreguntas[Count($aIdsPreguntas)-1];
	$preContestada=getPreguntaFromID($dbh,$idPreguntaContestada);
	if (respuestaOk($preContestada['RESPUESTA'],$_POST['respuestaUsu']))
	{
		$respuestaAnteriorOk=true;
		$msg="DELUXE!";
	}
	else
	{
		$msg="REGULAR ;(";
	}

	if ($aTestAlumno)
	{
modificarAlumnoFastest($dbh,$aTestAlumno,$preContestada['ID'],$respuestaAnteriorOk,$fastTestActivo);
	}
	else
	{
	insertarAlumnoTest($dbh,$fastTestActivo['ID'],$alumnoDB['ID'],$preContestada['ID'],$respuestaAnteriorOk,$fastTestActivo);
	}
}


$apreguntaa = getPreguntaFromID($dbh,$_POST['idPreguntaNext']);

$aIdsPreguntas[]=$apreguntaa['ID'];

	$apreguntaaNext = $aPreguntas[rand(0,Count($aPreguntas)-1)];
	$contt=0;
	while ((in_array($apreguntaaNext['ID'], $aIdsPreguntas))&&($contt<1000))
	{
		$apreguntaaNext = $aPreguntas[rand(0,Count($aPreguntas)-1)];
		$contt++;
	}
	if ($contt>=1000)
	{
		$apreguntaaNext = $aPreguntas[rand(0,Count($aPreguntas)-1)];
		while ($apreguntaa['ID']==$apreguntaaNext['ID'])
		{
		$apreguntaaNext = $aPreguntas[rand(0,Count($aPreguntas)-1)];	
		}
		
	}	

function esDiferenteTipo($resDefinitiva,$isRespNum)
{
	$isRespNumNueva = is_numeric($resDefinitiva);
	return ($isRespNumNueva xor $isRespNum);
}
function transformacionRespuesta($respu)
{
	
	if (is_numeric($respu))
	{
		if (($respu & ($respu - 1)) == 0)
		{			
			if (rand(0,1)==0)
			{
				return $respu*2;
			}
			else
			{
				return $respu/2;
			}
		}
		else
		{
			$nR = rand(1,5);
			if (rand(0,1)==0)
			{
				return $respu-$nR;
			}
			else
			{
				return $respu+$nR;
			}

		}
	}
	else
	{
		if (rand(0,1)==0)
		{
			$vowel_arr=array('a','e','i','o','u');
			$len=strlen($respu);
			$aPosVocales= array();
			for($i=0;$i<$len;$i++)
			{
				if(in_array($respu[$i],$vowel_arr))
				{
					$aPosVocales[]=$i;
				}
			}
			if (count($aPosVocales)>0)
			{
				$indexVocal = $aPosVocales[rand(0,Count($aPosVocales)-1)];
				$respu[$indexVocal]=$vowel_arr[rand(0,Count($vowel_arr)-1)];				
			}
		}
		return $respu;
	}
}



$numOpcionesPreguntas = getConfGeneral($dbh, "NUMERO_OPCIONES_PREGUNTAS");
$aRespuestas2 = array();
$aRespDB2 = explode(",", $apreguntaaNext['RESPUESTA']);
$aRespuestas2[] = $aRespDB2[rand(0,Count($aRespDB2)-1)];

//$aRespuestasOk = array();

$isRespNum = is_numeric($aRespuestas2[0]);
//$aRespuestasOk[] = $aRespuestas2[0];
for ($i=1; $i < $numOpcionesPreguntas; $i++) 
{ 
	$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
	while ($apreguntaa2['ID']==$apreguntaaNext['ID'])
	{
		$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
	}
	$aRespDB22 = explode(",", $apreguntaa2['RESPUESTA']);
	 $resDefinitiva= $aRespDB22[rand(0,Count($aRespDB22)-1)];
	$contt=0;
while (((in_array($resDefinitiva, $aRespuestas2)||(esDiferenteTipo($resDefinitiva,$isRespNum)))&&($contt<1000)))
	{
		$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
		while ($apreguntaa2['ID']==$apreguntaaNext['ID'])
		{
			$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
		}
		$aRespDB22 = explode(",", $apreguntaa2['RESPUESTA']);
	 	$resDefinitiva= $aRespDB22[rand(0,Count($aRespDB22)-1)];	
		$contt++;
	}
	if ($contt>=1000)
	{
		$resDefinitiva= $aRespDB22[rand(0,Count($aRespDB22)-1)];
	}
	//$aRespuestasOk[]=$resDefinitiva;

	if (rand(0,3)==0)
	{
		$resDefinitiva2=transformacionRespuesta($aRespuestas2[0]);
		if ($aRespuestas2[0]!=$resDefinitiva2)
		{
			if (!(in_array($resDefinitiva2, $aRespuestas2)))
			{
				$resDefinitiva=$resDefinitiva2;
			}
			else
			{
			$resDefinitiva2=transformacionRespuesta($resDefinitiva);
			if (!(in_array($resDefinitiva2, $aRespuestas2)))
			{
				$resDefinitiva=$resDefinitiva2;
			}
			}
			
		}
	}
	
	$aRespuestas2[]=$resDefinitiva;
	//var_export($aRespuestas2);
}

shuffle($aRespuestas2);



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
	
	<title>Test Rápido</title>

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
.badWrap{
    padding: 10px;
    margin: 0 0 20px 0;
	background: #d64d4d;
	color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}

		</style>
<script type="text/javascript">
	function manageR(resUsu)
	{ 
		//document.getElementById("form1").action="mimercado.php";
      	document.getElementById("respuestaUsu").value=resUsu;
      	document.getElementById("form1").submit(); 

	}
function init()
	{ 
	<?php 
/*		if (($_POST['respuestaUsu']!="SABESQUESI")&&($fastTestActivo))
		{
			if ($respuestaAnteriorOk)
			{
				echo "alert ('CORRECTA');";
			}
			else
			{
				echo "alert ('ERROR');";
			}
		}
	*/?>
	}
</script>

</head>

<body onload="init()">


	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>

		<div class="content-wrapper">

			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">
						<h2><?php echo ($fastTestActivo)?$fastTestActivo['NOMBRE']:"NO HAY TEST ACTIVO" ?></h2>
						<div class="row">
							<div class="col-md-12">

								<div class="panel panel-default">
									<div class="panel-heading"> 
<?php 

$aTestAlumno2 = getTestAlumno($dbh,$fastTestActivo['ID'],$alumnoDB['ID']);
if ($fastTestActivo)
{

	$nMonoDeLuxe = 0;
	$aRespuestas_ = explode(",",$aTestAlumno2['RESPUESTAS']);
	foreach ($aRespuestas_ as $resII) {
		$nMonoDeLuxe += (($resII>0)?$resII:0);
	}


 echo ((isset($aTestAlumno2['RESULTADO']))?"Nº Monotérminos Deluxe: ".$nMonoDeLuxe:"Monotérminos Deluxe: 0").((isset($aTestAlumno2['PREGUNTAS']))?"/".count(explode(",",$aTestAlumno2['PREGUNTAS'])):"/0");
}
?>	
</div>
<?php if($msg){?><div class="<?php echo ($respuestaAnteriorOk)?'succWrap':'badWrap'?>"><strong><?php echo htmlentities($msg); ?> </strong></div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data" id="form1" action="fastest.php">
<input type="hidden" name="ids_preguntas" value="<?php echo implode(",", $aIdsPreguntas); ?>"/>
<input type="hidden" name="respuestaUsu" id="respuestaUsu"/>
<input type="hidden" name="idPreguntaNext" id="idPreguntaNext" value="<?php echo $apreguntaaNext['ID']; ?>"/>
<input type="hidden" name="ids_respuestas" id="ids_respuestas" value="<?php echo implode(",", $aRespuestas2); ?>"/>

<div class="form-group">
	<div class="col-sm-10">
		<label class="control-label"><?php echo ($fastTestActivo)?$apreguntaa['PREGUNTA']:"";  ?></label>
	</div>
</div>
<div class="form-group">
<div class="col-sm-8 btn-group-vertical" role="group" >
<?php
if ($fastTestActivo)
{


$aRespuestas3 = explode(",", $_POST["ids_respuestas"]);

for ($i=0; $i < count($aRespuestas3); $i++) 
{ 


?>
    <button type="button" onclick="manageR('<?php echo $aRespuestas3[$i]?>')" style="font-size: 16px;"class="btn btn-<?php echo $aColorB4[rand(0,6)]?>"><?php echo $aRespuestas3[$i]?></button>
<?php
}
}
?>   
</button>
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
					}, 1500);
					});
				 $(document).ready(function () {          
					setTimeout(function() {
						$('.badWrap').slideUp("slow");
					}, 1500);
					});

	</script>
</body>
</html>
<?php  } } ?>