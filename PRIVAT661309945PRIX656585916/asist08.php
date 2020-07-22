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
//var_dump($_POST);


  
  
$tiposBotones = array( "btn btn-primary");
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
  $_SESSION["grado"]=$grado;
  $_SESSION["nivel"]=$nivel;
  
  $posGuion = strrpos($_POST["sel11"], "--");
  $idAs=substr($_POST["sel11"],$posGuion+2,strlen($_POST["sel11"]));
  $_SESSION["idAsignatura"]=$idAs;
  $_SESSION["nSesiones"]=$_POST["ssesiones"];
  $_SESSION["dia"]=$_POST["datepicker"];
  
if (!hayEstrellasAsigDia($db, $_SESSION["idAsignatura"], $_SESSION["dia"]))  
{
  insertMasivoEstrellasAsigDia($db,2, $_SESSION["idAsignatura"], $_SESSION["dia"],$grado,$nivel);
}
  


       $listaAlumnos = getAlumnosGradoNivel($db,$grado,$nivel);
    $auxLista = array();  
    foreach ($listaAlumnos as $alumno)
    {
      $auxLista[] = $alumno['NOMBRE']." ". $alumno['APELLIDO1']."--".$alumno['ID'];
    }
    $_SESSION['vAlumnos'] = $auxLista ;
    sort($_SESSION['vAlumnos']);
    $alumnos1=$_SESSION['vAlumnos'] ;
    $_SESSION['vAlumnos2'] = array ();
    $_SESSION['vElegidos'] = array ();
    //var_dump($_SESSION['vElegidos']);
  
}
else
{
  if (isset($_POST["mmasiva"]) && ($_POST["mmasiva"]==1))
  {
    modificarMasivoEstrellasAsigDia($db,$_POST["numeroSS"],$_SESSION["idAsignatura"], $_SESSION["dia"],$_SESSION["grado"],$_SESSION["nivel"]);
  }
  else
  {
    modificarEstrella($db,$_POST["alumnoSel"],$_SESSION["idAsignatura"],$_POST["numeroSS"],$_SESSION["dia"]);
  }
  

    // desde asis01
  // Se inserta el registro fantasma

  $alumnos1 = $_SESSION['vAlumnos'] ;
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
    

function managebuttonBack()
{
    document.getElementById("form1").action="asist00.php";
    document.getElementById("form1").submit();
}
    
function managebuttonMasiva() 
{      
       const { value: formValues } =  Swal.fire({
  title: '¿Resetear las estrellas de todos a '+document.getElementById('numeroSS').value+'?',
         showConfirmButton: false,
  html:
         <?php
        
         $Vnum=range(0,9);
         shuffle($Vnum);
         $nClave = abs($Vnum[0]-$Vnum[2]);
         $fila ="<center><table>";
         $cont=0;
         for($i=0;$i<3;$i++){
           $fila.= '<tr>';
              for($j=0;$j<3;$j++){
                  $fila.= '<td><a onclick="'.(($Vnum[$cont]==$nClave)?'managebuttonOK()':'swal.close()').'" class="btn btn-danger" >'.$Vnum[$cont].'</a></td>'; 
                  $cont++;
              }
          $fila.= '</tr>';
        }

$fila.= '<tr><td></td><td><a onclick="'.(($Vnum[$cont]==$nClave)?'managebuttonOK()':'swal.close()').'" class="btn btn-danger" >'.$Vnum[$cont].'</a></td></tr>';
         
         $fila.= '</table></center>';
         
         echo "'".$fila."'";
         ?>
      
        ,
  focusConfirm: false,
  
})
} 
    
function managebuttonOK()
{
    document.getElementById("form1").action="asist08.php";
    document.getElementById('mmasiva').value = 1;
    document.getElementById("form1").submit();
}
function managebuttonNumeros(num)
{
  document.getElementById('numeroSS').value = num;
  if (document.getElementById('idNumeros'+num).className == "btn btn-success")
  {
    // dado de segundas
    managebuttonMasiva();
      
  }
  else
  {
    document.getElementById('idNumeros0').className = "btn btn-danger";
    document.getElementById('idNumeros1').className = "btn btn-danger";
    document.getElementById('idNumeros2').className = "btn btn-danger";
    document.getElementById('idNumeros3').className = "btn btn-danger";
    document.getElementById('idNumeros4').className = "btn btn-danger";
    document.getElementById('idNumeros'+num).className = "btn btn-success";
  }
  
}

    
    

    
function cargarTemas()
{
  document.getElementById('idNumeros0').className = "btn btn-danger";
  document.getElementById('idNumeros1').className = "btn btn-danger";
  document.getElementById('idNumeros2').className = "btn btn-danger";
  document.getElementById('idNumeros3').className = "btn btn-danger";
  document.getElementById('idNumeros4').className = "btn btn-danger";
 
  
  <?php
    if (isset($_POST["numeroSS"]))
    {
      echo 'document.getElementById("idNumeros'.$_POST["numeroSS"].'").className = "btn btn-success";';
      echo 'document.getElementById("numeroSS").value='.$_POST["numeroSS"].';';
    }
    else
    {
      echo 'document.getElementById("idNumeros0").className = "btn btn-success";';  
      echo 'document.getElementById("numeroSS").value=0;';
    }
  ?>
  
}
function managebutton(alSel)
{

var height = window.innerHeight
      || document.documentElement.clientHeight
      || document.body.clientHeight;
        document.getElementById('altura').value=height;
      document.getElementById('alumnoSel').value=alSel;
      document.getElementById("form1").submit();
  
  //alert ("insertamos "+document.getElementById('numeroSS').value);
  /*
  
  var text = document.getElementById('idMas').innerHTML;
    if (text=="+/-")
    {
      faltas++;
    }
  else
    {
      faltas--;
    }
      if (faltas>-1)
      {
        
      
      document.getElementById('nFaltasInsertar').value=faltas;
      document.getElementById('tipoMas').value=text;
      document.getElementById("form1").submit(); 
  }
  
  */}


  </script>
</head>
<body onload="cargarTemas()">
<form id="form1" method="post" action="<?php test_input($_SERVER['PHP_SELF'])?>">
  <input type='hidden' name='altura' id='altura' value='<?php echo $_POST["altura"]?>'/>
  <input type='hidden' name='alumnoSel' id='alumnoSel'/>
  <input type='hidden' name='nFaltasInsertar' id='nFaltasInsertar'/>
  <input type='hidden' name='tipoMas' id='tipoMas'/>
  <input type='hidden' name='atras' id='atras'/>  
  <input type='hidden' name='numeroSS' id='numeroSS'/>  
  <input type='hidden' name='mmasiva' id='mmasiva' value = '0'/>  
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
  if (in_array($_SESSION['contador'], $_SESSION['vElegidos']))
  {
    $bele="true";
  }
  if ($i==$arrlength)
  {
    echo '<a id="idNumeros0" btn-outline btn-wrap-text style="font-size:'.($sizeT/2.5).'px; height: '.$sizeT.'px" onclick="managebuttonNumeros(0)" class="btn btn-danger">0*</a>';      
    echo '<a id="idNumeros1" btn-outline btn-wrap-text style="font-size:'.($sizeT/2.5).'px; height: '.$sizeT.'px" onclick="managebuttonNumeros(1)" class="btn btn-danger">1*</a>';      
    echo '<a id="idNumeros2" btn-outline btn-wrap-text style="font-size:'.($sizeT/2.5).'px; height: '.$sizeT.'px" onclick="managebuttonNumeros(2)" class="btn btn-danger">2*</a>';      
    echo '<a id="idNumeros3" btn-outline btn-wrap-text style="font-size:'.($sizeT/2.5).'px; height: '.$sizeT.'px" onclick="managebuttonNumeros(3)" class="btn btn-danger">3*</a>';      
    echo '<a id="idNumeros4" btn-outline btn-wrap-text style="font-size:'.($sizeT/2.5).'px; height: '.$sizeT.'px" onclick="managebuttonNumeros(4)" class="btn btn-success">4*</a>';      
  }
  else if ($i==$arrlength+1)
  {
    $pos = strrpos($_SESSION["sel11"], "--");
    $idAs=substr($_SESSION["sel11"],$pos+2,strlen($_SESSION["sel11"]));
    $nombre = substr($_SESSION["sel11"],0,$pos);
    $posAs= strrpos($nombre,"*");
    $clase = substr($nombre,$posAs+1,strlen($nombre));
    echo '<a id="bb_'.$i.'" btn-outline btn-wrap-text style="font-size:'.($sizeT/2.5).'px; height: '.$sizeT.'px" onclick="managebuttonBack()" class="btn btn-dark"> '.substr($_SESSION["dia"],5,strlen($_SESSION["dia"])).",".$clase.' </a>';      
  }
  else
  { 
    $pos = strrpos($alumnos1[$i], "--");
    $alumnoaux=substr($alumnos1[$i],0,$pos);
    $idAlumnoo=substr($alumnos1[$i],$pos+2,strlen($alumnos1[$i]));
    $nEstrellas = getEstrellasFromAlumnoDiaAsignatura($db, $idAlumnoo, $_SESSION["dia"], $_SESSION["idAsignatura"]);
    
    echo '<a id="bb_'.$i.'" style="font-size:'.($sizeT/2.5).'px; height: '.(floor($_POST["altura"]/$filas)).'px" onclick="managebutton('.$idAlumnoo.')" class="'.$tiposBotones[rand(0, sizeof($tiposBotones)-1)].'">'.$nEstrellas.'*'.$alumnoaux.'</a>';
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



