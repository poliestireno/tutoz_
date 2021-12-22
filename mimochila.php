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

	$CORREO = $_SESSION['alogin'];	
	$idAsignatura = getAsignaturasFromCurso($dbh,getAlumnoFromCorreo($dbh,$CORREO)['ID_CURSO'])[0]['ID'];
	$aPreguntas = getPreguntasFromAsignaturaID($dbh,$idAsignatura);
	if (Count($aPreguntas)==0)
	{
		$aPreguntas = getPreguntasTotal($dbh); 
	}
	$apreguntaa = $aPreguntas[rand(0,Count($aPreguntas)-1)];

	
function esDiferenteTipo($resDefinitiva,$isRespNum)
{
	$isRespNumNueva = is_numeric($resDefinitiva);
	return ($isRespNumNueva xor $isRespNum);
}


$numOpcionesPreguntas = getConfGeneral($dbh, "NUMERO_OPCIONES_PREGUNTAS");
$aRespuestas2 = array();
$aRespDB2 = explode(",", $apreguntaa['RESPUESTA']);
$aRespuestas2[] = $aRespDB2[rand(0,Count($aRespDB2)-1)];

//$aRespuestasOk = array();

$isRespNum = is_numeric($aRespuestas2[0]);
//$aRespuestasOk[] = $aRespuestas2[0];
for ($i=1; $i < $numOpcionesPreguntas; $i++) 
{ 
	$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
	while ($apreguntaa2['ID']==$apreguntaa['ID'])
	{
		$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
	}
	$aRespDB22 = explode(",", $apreguntaa2['RESPUESTA']);
	 $resDefinitiva= $aRespDB22[rand(0,Count($aRespDB22)-1)];
	$contt=0;
while (((in_array($resDefinitiva, $aRespuestas2)||(esDiferenteTipo($resDefinitiva,$isRespNum)))&&($contt<1000)))
	{
		$apreguntaa2 = $aPreguntas[rand(0,Count($aPreguntas)-1)];
		while ($apreguntaa2['ID']==$apreguntaa['ID'])
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
	
	<title>Mi Mochila</title>

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


<style type="text/css">


.dice {
  align-items: center;
  display: grid;
  grid-gap: 2rem;
  grid-template-columns: repeat(auto-fit, minmax(8rem, 1fr));
  grid-template-rows: auto;
  justify-items: center;
  padding: 2rem;
  perspective: 600px;
}
.die-list {
  display: grid;
  grid-template-columns: 1fr;
  grid-template-rows: 1fr;
  height: 6rem;
  list-style-type: none;
  transform-style: preserve-3d;
  width: 6rem;
}
.even-roll {
  transition: transform 1.5s ease-out;
}
.odd-roll {
  transition: transform 1.25s ease-out;
}
.die-item {
  background-color: #fefefe;
  box-shadow: inset -0.35rem 0.35rem 0.75rem rgba(0, 0, 0, 0.3),
    inset 0.5rem -0.25rem 0.5rem rgba(0, 0, 0, 0.15);
  display: grid;
  grid-column: 1;
  grid-row: 1;
  grid-template-areas:
    "one two three"
    "four five six"
    "seven eight nine";
  grid-template-columns: repeat(3, 1fr);
  grid-template-rows: repeat(3, 1fr);
  height: 100%;
  padding: 2rem;
  width: 100%;
}
.dot {
  align-self: center;
  background-color: #676767;
  border-radius: 50%;
  box-shadow: inset -0.15rem 0.15rem 0.25rem rgba(0, 0, 0, 0.5);
  display: block;
  height: 1.25rem;
  justify-self: center;
  width: 1.25rem;
}
.even-roll[data-roll="1"] {
  transform: rotateX(360deg) rotateY(720deg) rotateZ(360deg);
}
.even-roll[data-roll="2"] {
  transform: rotateX(450deg) rotateY(720deg) rotateZ(360deg);
}
.even-roll[data-roll="3"] {
  transform: rotateX(360deg) rotateY(630deg) rotateZ(360deg);
}
.even-roll[data-roll="4"] {
  transform: rotateX(360deg) rotateY(810deg) rotateZ(360deg);
}
.even-roll[data-roll="5"] {
  transform: rotateX(270deg) rotateY(720deg) rotateZ(360deg);
}
.even-roll[data-roll="6"] {
  transform: rotateX(360deg) rotateY(900deg) rotateZ(360deg);
}
.odd-roll[data-roll="1"] {
  transform: rotateX(-360deg) rotateY(-720deg) rotateZ(-360deg);
}
.odd-roll[data-roll="2"] {
  transform: rotateX(-270deg) rotateY(-720deg) rotateZ(-360deg);
}
.odd-roll[data-roll="3"] {
  transform: rotateX(-360deg) rotateY(-810deg) rotateZ(-360deg);
}
.odd-roll[data-roll="4"] {
  transform: rotateX(-360deg) rotateY(-630deg) rotateZ(-360deg);
}
.odd-roll[data-roll="5"] {
  transform: rotateX(-450deg) rotateY(-720deg) rotateZ(-360deg);
}
.odd-roll[data-roll="6"] {
  transform: rotateX(-360deg) rotateY(-900deg) rotateZ(-360deg);
}
[data-side="1"] {
  transform: rotate3d(0, 0, 0, 90deg) translateZ(4rem);
}
[data-side="2"] {
  transform: rotate3d(-1, 0, 0, 90deg) translateZ(4rem);
}
[data-side="3"] {
  transform: rotate3d(0, 1, 0, 90deg) translateZ(4rem);
}
[data-side="4"] {
  transform: rotate3d(0, -1, 0, 90deg) translateZ(4rem);
}
[data-side="5"] {
  transform: rotate3d(1, 0, 0, 90deg) translateZ(4rem);
}
[data-side="6"] {
  transform: rotate3d(1, 0, 0, 180deg) translateZ(4rem);
}
[data-side="1"] .dot:nth-of-type(1) {
  grid-area: five;
}
[data-side="2"] .dot:nth-of-type(1) {
  grid-area: one;
}
[data-side="2"] .dot:nth-of-type(2) {
  grid-area: nine;
}
[data-side="3"] .dot:nth-of-type(1) {
  grid-area: one;
}
[data-side="3"] .dot:nth-of-type(2) {
  grid-area: five;
}
[data-side="3"] .dot:nth-of-type(3) {
  grid-area: nine;
}
[data-side="4"] .dot:nth-of-type(1) {
  grid-area: one;
}
[data-side="4"] .dot:nth-of-type(2) {
  grid-area: three;
}
[data-side="4"] .dot:nth-of-type(3) {
  grid-area: seven;
}
[data-side="4"] .dot:nth-of-type(4) {
  grid-area: nine;
}
[data-side="5"] .dot:nth-of-type(1) {
  grid-area: one;
}
[data-side="5"] .dot:nth-of-type(2) {
  grid-area: three;
}
[data-side="5"] .dot:nth-of-type(3) {
  grid-area: five;
}
[data-side="5"] .dot:nth-of-type(4) {
  grid-area: seven;
}
[data-side="5"] .dot:nth-of-type(5) {
  grid-area: nine;
}
[data-side="6"] .dot:nth-of-type(1) {
  grid-area: one;
}
[data-side="6"] .dot:nth-of-type(2) {
  grid-area: three;
}
[data-side="6"] .dot:nth-of-type(3) {
  grid-area: four;
}
[data-side="6"] .dot:nth-of-type(4) {
  grid-area: six;
}
[data-side="6"] .dot:nth-of-type(5) {
  grid-area: seven;
}
[data-side="6"] .dot:nth-of-type(6) {
  grid-area: nine;
}


@media (min-width: 900px) {
  .dice {
    perspective: 1300px;
  }
}
.botonAnchoTotal {
  display: block;
  width: 100%;
  border: none;
  padding: 14px 28px;
  font-size: 16px;
  cursor: pointer;
  text-align: center;
}


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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script type="text/javascript">
	function testRapido()
	{       	
      	document.getElementById("idPreguntaNext").value=<?php echo $apreguntaa['ID']?>;
      	document.getElementById("ids_preguntas").value=<?php echo $apreguntaa['ID']?>;
      	document.getElementById("respuestaUsu").value='SABESQUESI';
     document.getElementById("form1").action="fastest.php";
    
    document.getElementById("form1").submit(); 
	}
	function mandarMo()
	{       	
	document.getElementById("form1").action="mandar_monotermino.php";
          	document.getElementById("form1").submit(); 
	}
	function mandarJus()
	{       	
	document.getElementById("form1").action="mandar_justificante.php";
          	document.getElementById("form1").submit(); 
	}

	function testLento()
	{ 
	}
function init()
	{ 
	}

function rollDice() {
  const dice = [...document.querySelectorAll(".die-list")];
  dice.forEach(die => {
    toggleClasses(die);
    die.dataset.roll = getRandomNumber(1, 6);
  });
}

function toggleClasses(die) {
  die.classList.toggle("odd-roll");
  die.classList.toggle("even-roll");
}

function getRandomNumber(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min + 1)) + min;
}


		function mostratDado() 
		{
			Swal.fire({
	  html:
	    '<div class="dice"> <ol class="die-list even-roll" data-roll="1" id="die-1"> <li class="die-item" data-side="1"> <span class="dot"></span> </li> <li class="die-item" data-side="2"> <span class="dot"></span> <span class="dot"></span> </li> <li class="die-item" data-side="3"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li> <li class="die-item" data-side="4"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li> <li class="die-item" data-side="5"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li> <li class="die-item" data-side="6"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li> </ol> </div> <br/><br/><br/><button type="button" id="roll-button" class="botonAnchoTotal" onclick="rollDice()" >Lánzalooo</button>'
	    	,
				  showConfirmButton: false,
				  showCloseButton: false,
				  showCancelButton: false,
				  focusConfirm: false,
				  
				})

							


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
						<div class="row">
							<div class="col-md-12">



								<div class="panel panel-default">
									<div class="panel-heading">Mi Mochila</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data" id="form1" >
<input type="hidden" name="respuestaUsu" id="respuestaUsu"/>
<input type="hidden" name="ids_preguntas" id="ids_preguntas" value=""/>
<input type="hidden" name="idPreguntaNext" id="idPreguntaNext" value=""/>
<input type="hidden" name="ids_respuestas" id="ids_respuestas" value="<?php echo implode(",", $aRespuestas2); ?>"/>

<div class="panel-body">
		<div class="panel panel-default col-sm-2">
		<div class="panel-heading">Test Rápido</div>
		<div class="panel-body">

		<div class="form-group">
		<div class="col-sm-10">
		<label class="control-label">Test Rápido</label>
		</div>
		</div>
		<div class="form-group">

		<div class="col-md-4">
		<a onclick="testRapido();" class="btn btn-info btn-outline btn-wrap-text">Hacer test</a>
		</div> 

		</div>

		</div>


		</div>

		<div class="panel panel-default col-sm-2 col-sm-offset-1">
		<div class="panel-heading">Test Lento</div>
		<div class="panel-body">

		<div class="form-group">
		<div class="col-sm-10">
		<label class="control-label">Test Lento</label>
		</div>
		</div>
		<div class="form-group">

		<div class="col-md-4">
		<a onclick="testLento();" class="btn btn-info btn-outline btn-wrap-text">Hacer test</a>
		</div> 

		</div>

		</div>


		</div>

		<div class="panel panel-default col-sm-2 col-sm-offset-1">
		<div class="panel-heading">Lanzar Dado</div>
		<div class="panel-body">

		<div class="form-group">
		<div class="col-sm-10">
		<label class="control-label">Lanzar Dado</label>
		</div>
		</div>
		<div class="form-group">

		<div class="col-md-4">
		<a onclick="mostratDado();" class="btn btn-danger btn-outline btn-wrap-text">Suerte!!!</a>
		</div> 

		</div>

		</div>


		</div>
</div>
<div class="panel-body">
		<div class="panel panel-default col-sm-2">
		<div class="panel-heading">Monotérmino</div>
	<div class="panel-body">

		<div class="form-group">
		<div class="col-sm-10">
		<label class="control-label">Monotérmino</label>
		</div>
		</div>
		<div class="form-group">

		<div class="col-md-4">
		<a  target="_blank" onclick="mandarMo();" class="btn btn-warning btn-outline btn-wrap-text">Mandar</a>
		</div> 

		</div>

		</div>


		</div>

		<div class="panel panel-default col-sm-2 col-sm-offset-1">
		<div class="panel-heading">JUSTIFICANTES</div>
		<div class="panel-body">

		<div class="form-group">
		<div class="col-sm-10">
		<label class="control-label">Justificantes</label>
		</div>
		</div>
		<div class="form-group">

		<div class="col-md-4">
		<a  target="_blank" onclick="mandarJus();" class="btn btn-warning btn-outline btn-wrap-text">Mandar</a>
		</div> 

		</div>

		</div>


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
					}, 3000);
					});

	</script>
</body>
</html>
<?php } ?>