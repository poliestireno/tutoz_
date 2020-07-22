<!DOCTYPE html>
<html lang="es">
<?php
 session_start();
 include('../includes/config.php');
 require_once("../UTILS/dbutils.php");
  $sql = "SELECT username from admin;";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $result=$query->fetch(PDO::FETCH_OBJ);
if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))
{ 
  header('location:../admin/index.php');
}
$db=conectarDB();
//  var_dump($_POST);

  
$tiposBotones = array( "btn btn-primary", "btn btn-success", "btn btn-warning", "btn btn-info");
$alumnos1 = array();
if (isset($_POST['alumnoSel']) && ($_POST['alumnoSel']==-1))
{
  if ($_SESSION['contador']>1)
  {
    $_SESSION['contador']=$_SESSION['contador']-1;
  }
}
else
{
  $_SESSION['contador']=$_SESSION['contador']+1;
}

function test_input($data){
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function getAlumnosPorLinea()
{
  global $alumnos1;
  $limite = 0;
  $tam = sizeof($alumnos1);
  if ($tam>29)
  {
    $limite=$tam/6;
  }
  else if ($tam>19)
  {
    $limite=$tam/5;
  }
  else if ($tam>9)
  {
    $limite=$tam/4;
  }
  else
  {
    $limite=3;
  }
  return floor($limite);
}
if (isset($_POST["sel11"]))
{ 
  // desde asis00
  $_SESSION["sel11"]=$_POST["sel11"];
  $pos = strrpos($_POST["sel11"], "*");
  $grado=substr($_POST["sel11"],0,$pos-1);
  $nivel=substr($_POST["sel11"],$pos-1,1);
  $posGuion = strrpos($_POST["sel11"], "--");
  $idAs=substr($_POST["sel11"],$posGuion+2,strlen($_POST["sel11"]));
  $_SESSION["idAsignatura"]=$idAs;
  $_SESSION["nSesiones"]=$_POST["ssesiones"];
  $_SESSION["dia"]=$_POST["datepicker"];
  
  
  


       $listaAlumnos = getAlumnosGradoNivel($db,$grado,$nivel);
    $auxLista = array();  
    foreach ($listaAlumnos as $alumno)
    {
      $auxLista[] = $alumno['NOMBRE']." ". $alumno['APELLIDO1']."--".$alumno['ID'];
    }
    $_SESSION['vAlumnos'] = $auxLista ;
    sort($_SESSION['vAlumnos']);
    $alumnos1=$_SESSION['vAlumnos'] ;
  
}
else if (isset($_POST["asis02"]))
{
    // desde asis02 
  $alumnos1 = $_SESSION['vAlumnos'];
}
else
{
    // desde asis01
  $alumnos1 = $_SESSION['vAlumnos'] ;
  if ($_POST['alumnoSel']==-1)
  {
    // viene de dar atrás
    $ulti= count($_SESSION['vAlumnos2'])-1;
    if ($ulti>=0)
    {
      $ultimoSel = $_SESSION['vAlumnos2'][$ulti];      
      $alumnos1 []= $ultimoSel;
      unset($_SESSION['vAlumnos2'][$ulti]);
      $_SESSION['vAlumnos2'] = array_values( $_SESSION['vAlumnos2'] );
    }
  }
  else
  {
    $_SESSION['vAlumnos2'] []= $alumnos1[$_POST['alumnoSel']];
    unset($alumnos1[$_POST['alumnoSel']]);
    $alumnos1 = array_values( $alumnos1 );      
  }
  sort($alumnos1);
  $_SESSION['vAlumnos']=$alumnos1;
  
  // para mantener estado ver las variables $_SESSION['contador'], $_SESSION['vElegidos'],$_SESSION['vAlumnos1'],$_SESSION['vAlumnos2']
  
}
?>


<head>

  <title>Asis-tencia</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../UTILS/mi.css">
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <!-- SWAL -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
  <script>
<?php
    echo "var alumnos = new Array("; 
  $comma="";
  foreach ($_SESSION['vAlumnos'] as $ase)
        {
    $pos = strrpos($ase, "-");
  $ase=substr($ase,0,$pos-1);
  
            echo $comma."'".$ase."'";   
            $comma=",";
        } 
    echo ");";
   ?> 
function managebuttonMenu() 
{
    document.getElementById("form1").action="asist00.php";
    document.getElementById("form1").submit();
}   

function managebuttonOK()
{
    document.getElementById("form1").action="asist02.php";
    document.getElementById("form1").submit();
}

   
function managebuttonInsert2()
{         
  Swal.fire({
  title: 'INSERTAR FALTAS',
  text: "Se insertarán <?php echo count($_SESSION['vAlumnos'])?>  faltas",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Continuar'
  }).then((result) => {
  if (result.value) {
    document.getElementById("form1").action="asist02.php";
    document.getElementById("form1").submit();
  }
  });
} 
function decirAleatorio() {
  var tamAlumnos = Math.floor(Math.random()*alumnos.length);
  //alert(alumnos.length);
var texto = alumnos[tamAlumnos];

  //alert(tamAlumnos);
 if (typeof texto !== 'undefined')
 {
            $("*").each(function() { 
                if (this.id) { 
                  //alert(this.id);==
                  //alert(alumnos[tamAlumnos]);
                  if (alumnos[tamAlumnos]==document.getElementById(this.id).text)
                  {
                    document.getElementById(this.id).className = 'btn btn-danger';
                    //alert(document.getElementById(this.id).text);
                 }
                  //ID.push(this.id); 
                } 
            }); 
                      const index = alumnos.indexOf(alumnos[tamAlumnos]);
                    if (index > -1) 
                    {
                      alumnos.splice(index, 1);
                    }
 
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
		//alert('Speech has finished');    
	}
	// speak
	window.speechSynthesis.speak(utter); 
}
 }
}    
       
function toggleAlumno(texto,alSel,elegido,tam_alumnos)
{
    if (alumnos.includes(texto))
    {
       $("*").each(function() { 
          if (this.id) { 
                  if (texto==document.getElementById(this.id).text)
                  {
                    document.getElementById(this.id).className = 'btn btn-danger';
                 }
                  //ID.push(this.id); 
                } 
            }); 
            const index = alumnos.indexOf(texto);
            if (index > -1) 
            {
              alumnos.splice(index, 1);
            }
        
    }
  else
  {
       $("*").each(function() { 
          if (this.id) { 
                  if (texto==document.getElementById(this.id).text)
                  {
                    document.getElementById(this.id).className = 'btn btn-success';
                 }
                  //ID
                } 
            }); 
     alumnos.push(texto); 
  }
  
}
  </script>
</head>
<body>
<form id="form1" method="post" action="<?php test_input($_SERVER['PHP_SELF'])?>">
  <input type='hidden' name='altura' id='altura' value='<?php echo $_POST["altura"]?>'/>
  <input type='hidden' name='alumnoSel' id='alumnoSel'/>
  <input type='hidden' name='atras' id='atras'/>  
  <div id="cargador" align="center" style="display: none; position:fixed; height: 100%; width: 100%; top:0;left 0;">
      <img src='car.gif' style="height: 100%" />
  </div>
   <?php 
$limite = getAlumnosPorLinea();
$cont=0;
$filas=ceil((count($alumnos1)+1)/($limite));
if ($filas>4)
{
  $sizeT=floor($_POST["altura"]/$filas);
}
else
{
   $sizeT=floor($_POST["altura"]/4);
}
  
echo '<div id="aa_0" class="btn-group btn-group-justified" >';    
$arrlength = count($alumnos1);
$contFilas=0;
for($i = 0; $i < $arrlength+2; $i++) 
{
  $bele="false";
  if ($i==$arrlength)
  {
    echo '<a id="bb_'.$i.'" btn-outline btn-wrap-text style="font-size:'.($sizeT/2.5).'px; height: '.$sizeT.'px" onclick="decirAleatorio()" class="btn btn-primary">Random</a>';      
  }
  else if ($i==$arrlength+1)
  {
    $pos = strrpos($_SESSION["sel11"], "--");
    $idAs=substr($_SESSION["sel11"],$pos+2,strlen($_SESSION["sel11"]));
    $nombre = substr($_SESSION["sel11"],0,$pos);
    $posAs= strrpos($nombre,"*");
    $clase = substr($nombre,$posAs+1,strlen($nombre));
    echo '<a id="bb_'.$i.'" btn-outline btn-wrap-text style="font-size:'.($sizeT/2.5).'px; height: '.$sizeT.'px" onclick="managebuttonMenu()" class="btn btn-dark"> Menu principal </a>';      
  }
  else
  {
    $pos = strrpos($alumnos1[$i], "--");
    $alumnoaux=substr($alumnos1[$i],0,$pos);
    echo '<a id="bb_'.$i.'" style="font-size:'.($sizeT/2.5).'px; height: '.(floor($_POST["altura"]/$filas)).'px" onclick="toggleAlumno(\''.$alumnoaux.'\','.$i.','.$bele.','.$arrlength.')" class="btn btn-success">'.$alumnoaux.'</a>';
  }
  $cont++;
  if ($cont % $limite == 0)
  {
    $contFilas++;
    echo '</div><div  id="aa_'.$contFilas.'" class="btn-group btn-group-justified">';   
  } 
}
echo '</div>'; 
?>
    
  </form>
</body>
</html>



