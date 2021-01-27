<!DOCTYPE html>
<html lang="es">
<?php
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
session_start();
$db=conectarDB();
//var_dump($_POST);
if (isset($_GET["cod"]))
{
  $_POST["cod"]=$_GET["cod"];
}
$textoError="";
$decrypted = openssl_decrypt($_POST["cod"], "AES-128-ECB", "kgYYBOihH8/(ggG/)gKGB8/biLJLDJOIUD/(%&/UG(DF(/F%&(IGDF%(F)HFG=FD:_V:F_VBLVP?F=F)FKIF)))");
if ($decrypted)  
{
  $myString = $decrypted;
  $Datos = explode(',', $myString);
  
   $_POST["sel11"]=$Datos[0];
   $_POST["ssesiones"]=$Datos[1];
   $_POST["datepicker"]=$Datos[2];  
  
}
else
{
  $textoError= '<div class="form-group"><span class="label label-danger">CODIGO DE CLASE NO VALIDO</span></div><div class="form-group">
        <a onclick="managebuttonVolver()" class="btn btn-danger btn-outline btn-wrap-text">Vuelve a intentarlo</a>
      </div>';
}
//var_dump($decrypted);

   
  
$tiposBotones = array( "btn btn-primary");
$alumnos1 = array();

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
  $_SESSION['vAlumnos'] = $auxLista;
  sort($_SESSION['vAlumnos']);
  $alumnos1=$_SESSION['vAlumnos'] ;
  $_SESSION['vAlumnos2'] = array ();
  $_SESSION['vElegidos'] = array ();
  //var_dump($_SESSION['vElegidos']);
  
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
    
function managebuttonVolver()
{
  document.getElementById("form1").action="codigoOnline.php";
  document.getElementById("form1").submit(); 
} 
function prepararPantalla()
{
    var heightTotal = window.innerHeight
  || document.documentElement.clientHeight
  || document.body.clientHeight;
}
function managebutton(alSel,correoI)
{
  document.getElementById('alumnoSel').value=alSel;
  document.getElementById('nuevoCorreo').value=0;
  correoDos="";
  if (correoI=='')
  {
        const { value: email2 } = Swal.fire({
        title: 'Correo electrónico',
        input: 'email',
        inputPlaceholder: 'Por favor, introduce tu correo electrónico'
      }).then(function(result) { 
          correoDos=result.value;
        if (typeof correoDos !== 'undefined')
        {
                  Swal.fire({
        title: '¿Está Ok?',
        text: '¿Tu correo electrónico es '+correoDos+'?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ok'
      }).then((result) => {
        if (result.value) {
          document.getElementById('nuevoCorreo').value=1;
          document.getElementById('correoTo').value=correoDos;
          document.getElementById("form1").submit();
        }
      })
        }
      })
  } 
  else
  {
      document.getElementById('correoTo').value=correoI;
      document.getElementById("form1").submit();      
   }
 }


  </script>
</head>
<body onload="prepararPantalla()">
  
<form id="form1" method="post" action="enviarCorreo.php">
  <input type='hidden' name='alumnoSel' id='alumnoSel'/>
  <input type='hidden' name='sel11' id='sel11' value='<?php echo $_POST["sel11"]?>'/>
  <input type='hidden' name='nSesiones' id='nSesiones' value='<?php echo $_POST["ssesiones"]?>'/>
  <input type='hidden' name='datepicker' id='datepicker' value='<?php echo $_POST["datepicker"]?>'/>
  <input type='hidden' name='correoTo' id='correoTo' value='poliestireno@gmail.com'/>
  <input type='hidden' name='nuevoCorreo' id='nuevoCorreo'/>
  
  <!--div id="cargador" align="center" style="display: none; position:fixed; height: 100%; width: 100%; top:0;left 0;">
      <img src='car.gif' style="height: 100%" />
  </div!-->
   <?php 
$limite = getAlumnosPorLinea();
$cont=0;
$filas=ceil((count($alumnos1)+1)/($limite));
/*if ($filas>4)
{
  $sizeT=floor($_POST["altura"]/$filas);
}
else
{
   $sizeT=floor($_POST["altura"]/4);
}*/
  
echo '<div id="aa_0" class="btn-group btn-group-justified" >';    
$arrlength = count($alumnos1);
$contFilas=0;
for($i = 0; $i < $arrlength; $i++) 
{

    $pos = strrpos($alumnos1[$i], "--");
    $alumnoaux=substr($alumnos1[$i],0,$pos);
    $idAlumnoo=substr($alumnos1[$i],$pos+2,strlen($alumnos1[$i]));
    $correoAlumno = getAlumnoFromId($db,$idAlumnoo)["CORREO"];
  echo '<a id="bb_'.$i.'" style="font-size: ((heightTotal/'.$filas.')/2.5) px; height: (heightTotal/'.$filas.') px" onclick="managebutton('.$idAlumnoo.',\''.$correoAlumno.'\')" class="btn btn-success">'.$alumnoaux.'</a>';
  $cont++;
  if ($cont % $limite == 0)
  {
    $contFilas++;
    echo '</div><div  id="aa_'.$contFilas.'" class="btn-group btn-group-justified">';   
  } 
}
echo '</div>'; 
  echo $textoError;
?>
    
  </form>
</body>
</html>



