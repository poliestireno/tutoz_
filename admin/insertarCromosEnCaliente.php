<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";
//var_export($_POST);
try
  {
$sql = "SELECT username from admin;";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $result=$query->fetch(PDO::FETCH_OBJ);

if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))
  { 
header('location:index.php');
}
else{

$curso=9999;
if (isset($_GET['curso']))
{
    $curso=$_GET['curso'];
}
echo 1;
$aAlumnosCurso = getAlumnosFromCursoID($dbh,$curso);
foreach ($aAlumnosCurso as $alumno) 
{
$CORREO=$alumno['CORREO'];
$lastInsertId=$alumno['ID'];
echo "el id es ".$lastInsertId;
echo 2;
if (utilizaCromosCurso($dbh,$curso))
{
  echo 3;
    // insertamos cromos del alumno y le damos los cromos iniciales

    $nCromosIni = getAdminCromos($dbh)['N_CROMOS_INI'];
    $setId = getSetCromosIdFromAlumno($dbh,$CORREO);
    $nCromosPropios = getAdminCromos($dbh)['N_CROMOS_PROPIOS'];
    $contPropios=0;

    $aRandomPropios = array();
    for ($i=0; $i < $nCromosIni; $i++) { 
        $aRandomPropios[$i]=$i;
    }
    shuffle($aRandomPropios);
    $aRandomPropios2 = array();
    for ($i=0; $i < $nCromosPropios; $i++) { 
        $aRandomPropios2[$i]=$aRandomPropios[$i];
    }
    
    for ($i=0; $i < $nCromosIni; $i++) 
    { 
       echo 4;
        if (in_array($i, $aRandomPropios2)) 
        {
            $ID_POSEEDOR=$lastInsertId;
            $GENERADO=1;
        }
        else
        {
            $ID_POSEEDOR=NULL;
            $GENERADO=0;
        }

        $ID_CREADOR=$lastInsertId;        
        $name="REEMPLAZAR_NOMBRE";
        $color="White";
        $mana_w=1;
        $picture="";
        $cardtype="";
        $rarity="Common";
        $cardtext="";
        $power=$i+1;
        $toughness=$nCromosIni;
        $artist="REEMPLAZAR_ARTISTA";
        $bottom=$alumno['NOMBRE']. " " . $alumno['APELLIDO1'] . " " .$alumno['APELLIDO2'];
        insertarCromo($dbh,$ID_CREADOR,$ID_POSEEDOR,$GENERADO,$setId, $name, $color, $mana_w, $picture, $cardtype, $rarity, $cardtext, $power, $toughness, $artist, $bottom);
        echo 5;
    }
    
echo 6;
    // notificamos los cromos otorgados de inicio al alumno

    $notitype='Empiezas tu álbum con '.$nCromosPropios.' cromos tuyos';
    mandarNotificacion($dbh,'Admin',$CORREO,$notitype);

    // notificamos números de cromos al alumno

    $notitype='Creados '.$nCromosIni.' cromos tuyos que salen a mercado';
    mandarNotificacion($dbh,'Admin',$CORREO,$notitype);
  echo 7;  
}


}
} 


}
  catch (Exception $ex)
  {
      echo "Error:".$ex->getMessage();
  }  

  ?>

  111111111111