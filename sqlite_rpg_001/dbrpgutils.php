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
function getAlumnoFromIdJS()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$IDAlumno = $_POST["param01"];
	$aluBD = getAlumnoFromID($dbh,$IDAlumno);
	$mibotBD = getMiBotFromAlumnoID($dbh,$IDAlumno);
	$aInfoAlumno = array_merge($aluBD, $mibotBD); 
	$result = json_encode($aInfoAlumno);
	return $result;
}
function getAlumnoFromCorreoJS()
{
	global $dbh;
	if(!isset($_POST["param01"])) die("No param01 found");
	$correo = $_POST["param01"];
	$aluBD = getAlumnoFromCorreo($dbh,$correo);
	$mibotBD = getMiBotFromAlumnoID($dbh,$aluBD['ID']);
	$aInfoAlumno = array_merge($aluBD, $mibotBD); 
	$result = json_encode($aInfoAlumno);
	return $result;
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

	