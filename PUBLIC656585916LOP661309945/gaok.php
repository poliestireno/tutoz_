<?php
session_start();
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$db=conectarDB();
//var_dump($_POST);
//var_dump($_GET);

$textoMostrar="Error, asistencia no completada, pongase en contacto con el administrador, osea Gilbert";
$decrypted = openssl_decrypt($_GET["cod"], "AES-128-ECB", "kgYYBOihH8/(ggG/)gKGB8/biLJLDJOIUD/(%&/UG(DF(/F%&(IGDF%(F)HFG=FD:_V:F_VBLVP?F=F)FKIF)))");
//echo $decrypted;
$esElegido=false;
if ($decrypted)  
{
  $myString = $decrypted;
  $Datos = explode(',', $myString);
  
   $idAlumno=$Datos[0];
   $sell1=$Datos[1];
   $ssesiones=$Datos[2];
   $datepicker=$Datos[3];
  
  $pos = strrpos($sell1, "*");
  $posGuion = strrpos($sell1, "--");
  $clase=substr($sell1,0,$posGuion);
  $idAsignatura=substr($sell1,$posGuion+2,strlen($sell1)); 
  $esElegido=false;

  if (getFaltasFromAlumnoDiaAsignatura($db, $idAlumno, $datepicker, $idAsignatura)>0)
{
  borrarFaltasAsignaturaDiaAlumno($db,$idAsignatura,$datepicker,$idAlumno);
  $textoMostrar="Asistencia correcta!!! ".getAlumnoFromId($db,$idAlumno)["NOMBRE"]." ".getAlumnoFromId($db,$idAlumno)["APELLIDO1"]
  . " en ".$clase." el dia ".$datepicker;
  $fantasma = getFantasma($db,$idAsignatura,$datepicker);
  $contElegTotal= $fantasma["CONT_ELEG"];
  $datos = explode(',', $contElegTotal);
  
  $contador=$datos[0];
  $contador=$contador+1;

  for ($i=1;$i<Count($datos);$i++)
  {
    if ($contador==$datos[$i])
    {
      $esElegido=true;
    }
  }
  if ($esElegido)
  {
    $lElegidos= $fantasma["ELEGIDOS"];
    $sEsPrimero="";
    if ($lElegidos!="")
    {
      $sEsPrimero=",";
    }
    $confAsig = getConfAsignaturaFromID($db,getAsignaturasFromCurso($db,getAlumnoFromId($db,$idAlumno)['ID_CURSO'])[0]['ID_CONF_ASIGNATURAS'])['NOMBRE'];

    if ($confAsig!='MENU_SIMPLON')
    {    
          $idCursoII = getAlumnoFromId($db,$idAlumno)['ID_CURSO'];
          $diaHoy = DateTime::createFromFormat('Y-m-d',date('Y-m-d'))->format('Y-m-d');
if (!getBonoLike($db,$idAlumno,$idCursoII,$diaHoy,"Enhorabuena de la buena"))
          {
            insertarBono($db,$idAlumno,$idCursoII,getConfGeneral($dbh, "NUM_ESTRELLAS_ENHORABUENA"),"Enhorabuena de la buena");
          }
    }
    $lElegidos=$lElegidos.$sEsPrimero.$idAlumno;
    insertarElegidosEnFantasma($db,$idAsignatura,$datepicker,$lElegidos);
  }
  //echo "contElegTotal:".$contElegTotal;
  $posPrimeraComa = strpos($contElegTotal, ",");
  //echo 'posPrimeraComa:'.$posPrimeraComa;
  $contElegTotal=substr($contElegTotal,$posPrimeraComa,strlen($contElegTotal));
  $contElegTotal=$contador.$contElegTotal;
  
  //echo "contElegTotal:".$contElegTotal;
  modificarContEleg($db,$idAsignatura,$datepicker,$contElegTotal);
}
else
{
  $textoMostrar="Asistencia correcta!!! por enesima vez, ".getAlumnoFromId($db,$idAlumno)["NOMBRE"]." ".getAlumnoFromId($db,$idAlumno)["APELLIDO1"]
  . " en ".$clase." el dia ".$datepicker;
  
}
}


?>
  <!DOCTYPE html>
  <html lang="es">

  <head>
    <title>Asis-tencia</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../UTILS/mi.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script>
function piarTexto(texto)
{
  //vibrar
  navigator.vibrate([500, 250, 500, 250, 500, 250, 500, 250, 500, 250, 500, 250, 500]);
  // list of languages is probably not loaded, wait for it
  if(window.speechSynthesis.getVoices().length == 0) {
	  window.speechSynthesis.addEventListener('voiceschanged', function() {
		  textToSpeech();
	  });
  }
  else {
	// languages list available, no need to wait
	textToSpeech();
  }

function textToSpeech() {
	// get all voices that browser offers
	var available_voices = window.speechSynthesis.getVoices();
	// this will hold an english voice
	var english_voice = '';
	// find voice by language locale "en-US"
	// if not then select the first voice
	for(var i=0; i<available_voices.length; i++) {
		if(available_voices[i].lang === 'es') {
			english_voice = available_voices[i];
			break;
		}
	}
	if(english_voice === '')
		english_voice = available_voices[0];
	// new SpeechSynthesisUtterance object
	var utter = new SpeechSynthesisUtterance();
	utter.rate = 1;
	utter.pitch = 0.5;
	utter.text = texto;
	utter.voice = english_voice;
	// event after text has been spoken
	utter.onend = function() {
	}
	// speak
	window.speechSynthesis.speak(utter); 
}
   }
</script>

  </head>

  <body <?php if ($esElegido) echo 'onload="piarTexto(\' enhorabuena,enhorabuena,enhorabuena,enhorabuena de la buena. \')"'?> >

      <span class="label label-primary"><?php echo $textoMostrar?></span>
      <?php if ($esElegido) echo '<h1><span class="label label-success">¡ENHORABUENA DE LA BUENA!,'.getAlumnoFromId($db,$idAlumno)["NOMBRE"].' HAS SIDO ELEGID@</span></h1>'?>

  </body>
  </html>