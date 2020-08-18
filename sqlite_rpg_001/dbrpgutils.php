<?php

include('../includes/config.php');
require_once("../UTILS/dbutils.php");

if(!isset($_POST["funcion"])) die("No funcion found");
$funcion = $_POST["funcion"];
try {
switch ($funcion) {
	case 'getData':
		echo getData();
		break;
	case 'getCompaneros':
		echo getCompaneros();
		break;
	case 'getIDCompaneros':
		echo getIDCompanerosMasPersonajes();
		break;
	case 'getAlumnoFromIdJS':
		echo getAlumnoFromIdJS();
		break;
	case 'getAlumnoFromCorreoJS':
		echo getAlumnoFromCorreoJS();
		break;
	case 'getCromoAleatorioFromCorreoJS':
		echo getCromoAleatorioFromCorreoJS();
		break;
	case 'mandarNotificacion':
		echo mandarNotificacionJS();
		break;
	case 'iniciarPartidaPPTJS':
		echo iniciarPartidaPPTJS();
		break;
	case 'modificarCalas':
		echo modificarCalasJS();
		break;
	case 'getEventosRetosJugador':
		echo getEventosRetosJugadorJS();
		break;
	case 'modificarEstadoReto':
		echo modificarEstadoRetoJS();
		break;
	
	default:
		# code...
		break;
}


} catch(PDOException $e) {

	echo "Exception: ".$e->getMessage();

}

function getCompaneros()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$correo = $_POST["param01"];
	$aAlumnosCurso = getAlumnosCompanerosCursoFromCorreo($dbh,$correo);
	$result="";		
	$comma ="";
	foreach ($aAlumnosCurso as $alumno) 
  	{		
		if (strcasecmp($alumno['CORREO'], $correo) != 0)
		{
			$result.=$comma.$alumno['NOMBRE'];
			$comma ="|";			
		}
	}
	return $result;
}
function getIDCompanerosMasPersonajes()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$correo = $_POST["param01"];
	$aAlumnosCurso = getAlumnosCompanerosCursoFromCorreo($dbh,$correo);
	$result=array();		
	foreach ($aAlumnosCurso as $alumno) 
  	{		
		if (strcasecmp($alumno['CORREO'], $correo) != 0)
		{
			$result[]=$alumno['ID'];			
		}
	}
	$idCurso = getAlumnoFromCorreo($dbh,$correo)['ID_CURSO'];
	$aCursosPersonajes = getCursosPersonajesFromCursoID($dbh,$idCurso);
	
	foreach ($aCursosPersonajes as $curso_personajes) 
	{
		$aPersonajes = getAlumnosFromCursoID($dbh,$curso_personajes['ID_CURSO_PERSONAJES']);
		foreach ($aPersonajes as $personaje) 
		{
			$result[]=$personaje['ID'];	
		}
	}
	$result = json_encode($result);
	return $result;
}
function getEventosRetosJugadorJS()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$correo = $_POST["param01"];
	$result=array();
	$listaTareas = getTareasFromAlumno($dbh,$correo);
	foreach ($listaTareas as $tarea) 
  	{
  		if ($tarea['ID_SITIO']!=NULL)
  		{
  			$aux = array();
  			$aux[]=getSitioFromID($dbh,$tarea['ID_SITIO'])['ID_MAP'];
  			$aux[]=$tarea['POS_X'];
  			$aux[]=$tarea['POS_Y'];
  			$aux[]=$tarea['NOMBRE'];
  			$aux[]=$tarea['LINK_DOCUMENTO'];
  			$aux[]=$tarea['DESCRIPCION'];
			$aux[]=$tarea['ID'];

  			$result[]=$aux;
  		}
  	}
	$result = json_encode($result);
	return $result;
}
	 
function iniciarPartidaPPTJS()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$correoJugador = $_POST["param01"];
	if(!isset($_POST["param02"])) die("No param02 found");
	$correoPNJ = $_POST["param02"];	
	return iniciarPartidaPPT($dbh,$correoJugador,$correoPNJ);
}
function getAlumnoFromIdJS()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$IDAlumno = $_POST["param01"];
	$aluBD = getAlumnoFromID($dbh,$IDAlumno);
	$mibotBD = getMiBotFromAlumnoID($dbh,$IDAlumno);
	$miactorBD = getMiActorFromAlumnoID($dbh,$IDAlumno);
	$aInfoAlumno = array_merge($aluBD, $mibotBD); 
	$aInfoAlumno = array_merge($aInfoAlumno, $miactorBD); 
	$result = json_encode($aInfoAlumno);
	return $result;
}

	

function mandarNotificacionJS()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$remitente = $_POST["param01"];
	if(!isset($_POST["param02"])) die("No param02 found");
	$receptor = $_POST["param02"];
	if(!isset($_POST["param03"])) die("No param03 found");
	$mensaje = $_POST["param03"];
	mandarNotificacion($dbh,$remitente,$receptor,$mensaje);
	return "";
}

function modificarEstadoRetoJS()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$correo = $_POST["param01"];
	if(!isset($_POST["param02"])) die("No param02 found");
	$idTarea = $_POST["param02"];
	if(!isset($_POST["param03"])) die("No param03 found");
	$estado = $_POST["param03"];
	modificarEstadoReto($dbh,$correo,$idTarea,$estado);
	return "";
}
function modificarCalasJS()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$correo = $_POST["param01"];
	if(!isset($_POST["param02"])) die("No param02 found");
	$cantidad = $_POST["param02"];
	modificarCalas($dbh,$correo,$cantidad);
	return "";
}

function getAlumnoFromCorreoJS()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$correo = $_POST["param01"];
	$aluBD = getAlumnoFromCorreo($dbh,$correo);
	$mibotBD = getMiBotFromAlumnoID($dbh,$aluBD['ID']);
	$aInfoAlumno = array_merge($aluBD, $mibotBD);
	$miactorBD = getMiActorFromAlumnoID($dbh,getAlumnoFromCorreo($dbh,$correo)['ID']);
	$aInfoAlumno = array_merge($aInfoAlumno, $miactorBD); 
	$result = json_encode($aInfoAlumno);
	return $result;
}
function url(){
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}
function getCromoAleatorioFromCorreoJS()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$correo = $_POST["param01"];


	$vectorCromos = getCromosDeAlbum($dbh,$correo);

	$cromo = $vectorCromos[random_int (0, Count($vectorCromos)-1 )];

return "https://www.mtgcardmaker.com/mcmaker/createcard.php?name=". $cromo['name']."&color=". $cromo['color']."&mana_w=". $cromo['mana_w']."&picture=". substr(url(),0,strrpos(url(), '/')).'/imagesCromos/'.$cromo['picture']."&cardtype=". $cromo['cardtype']."&rarity=". $cromo['rarity']."&cardtext=". $cromo['cardtext']."&power=". $cromo['power']."&toughness=". $cromo['toughness']."&artist=". $cromo['artist']."&bottom=". $cromo['bottom'];
}

function getData()
{
	if(!isset($_POST["param01"])) die("No param01 found");
	$tipo      = $_POST["param01"];
	//$tipo =1;
	$array       = array();
	$result      = "";
			
	$db = new PDO('sqlite:rmmv.sqlite');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM datos WHERE tipo=".$tipo;
	$select = $db->query($sql);
	$rows = $select->fetchAll();
	$result="";		
	if (count($rows)>0) {
	$comma ="";
	foreach ($rows as $entry) {
		$result.=$comma.$entry['data'];
		$comma ="|";
	}
	}
	$db = NULL;

	return $result;	
}

?>

	