<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
  { 
header('location:index.php');
}
else{
  $idCur=$_POST['idc'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Justas</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
  
  <style type="text/css">
@font-face {
  font-family: 'Oli2';
  src: 
    url('fonts/OlivettiType2.ttf') format('woff'), 
  url('fonts/OlivettiType2.ttf') format('truetype');
}

.Oli2  {
    font-family: 'Oli2';
    text-align: center;
}
  </style>
</head>
<body onload="validateFin();" style="background-color: black">
<form action="justa.php" id="form1" method="post">
<input type="hidden" name="idc" id="idc"  value="<?php echo $_POST['idc']?>">
<input type="hidden" name="p1" id="p1"  value="<?php echo $_POST['p1']?>">
<input type="hidden" name="contJusta" id="contJusta"  value="<?php echo ($_POST['contJusta']+1)?>">
<input type="hidden" name="totalJusta" id="totalJusta"  value="<?php echo ($_POST['totalJusta'])?>">
<input type="hidden" name="p2" id="p2"  value="<?php echo $_POST['p2']?>">
<input type="hidden" name="nRepescas" id="nRepescas"  value="<?php echo $_POST['nRepescas']?>">
<input type="hidden" name="textTotalAlumnos" id="textTotalAlumnos" value="<?php echo $_POST['textTotalAlumnos']?>">
<input type="hidden" name="indicePlayer" id="indicePlayer" value="<?php echo $_POST['indicePlayer']?>">
<input type="hidden" name="historial" id="historial">
<input type="hidden" name="numMaxGanadas" id="numMaxGanadas" value="<?php echo $_POST['numMaxGanadas']?>">
<input type="hidden" name="indiceUltimoGanador" id="indiceUltimoGanador">
<input type="hidden" name="indiceUltimoGanadorAcum" id="indiceUltimoGanadorAcum">

<input type="hidden" name="historialGanador" id="historialGanador">
<input type="hidden" name="historialNumMaxGanadas" id="historialNumMaxGanadas">
<input type="hidden" name="listaGanadores" id="listaGanadores" value="<?php echo $_POST['listaGanadores']?>">


<?php

$aTotalAlumnos = explode(",", $_POST['textTotalAlumnos']);

  $player1 = getAlumnoFromId($dbh, $aTotalAlumnos[$_POST['p1']]);
  $player2 = getAlumnoFromId($dbh, $aTotalAlumnos[$_POST['p2']]);
//var_export($_POST);
if (isset($_POST['historial']))
{

  //backup de todo por si atrás
$aListaGanadores = explode(",", $_POST['listaGanadores']);


  $backup_numMaxGanadas=$_POST['numMaxGanadas']-1;
  $backup_indiceUltimoGanador=$_POST['indiceUltimoGanador'];
  $backup_indiceUltimoGanadorAcum=$_POST['indiceUltimoGanadorAcum'];
  $backup_historialGanador=$_POST['historialGanador'];
  if ($aListaGanadores[count($aListaGanadores)-1]==$_POST['historialGanador'])
  {
    $backup_historialNumMaxGanadas=$_POST['historialNumMaxGanadas']-1;
  }
  else
  {
    $backup_historialNumMaxGanadas=$_POST['historialNumMaxGanadas'];
  }

  if ($_POST['indiceUltimoGanador']==$_POST['indiceUltimoGanadorAcum'])
  {
    $_POST['numMaxGanadas']=$_POST['numMaxGanadas']+1;
    if ($_POST['numMaxGanadas']>=$_POST['historialNumMaxGanadas'])
    {
      $_POST['historialGanador']=$_POST['indiceUltimoGanador'];
      $_POST['historialNumMaxGanadas']=$_POST['numMaxGanadas'];
    }
  }
  else
  {
    $_POST['numMaxGanadas']=1;
    $_POST['indiceUltimoGanadorAcum']=$_POST['indiceUltimoGanador'];
  }
}
else
{
  $aListaGanadores= array();

}
$historial = ((isset($_POST['historial']))?$_POST['historial']:"")."|".$_POST['p1'].",".$_POST['p2'].",".$_POST['indicePlayer'];
//var_export($_POST);
//var_export($player1);
//var_export($player2);

?>
<div class="text-center">
  <h1 class="Oli2" style="font-size: 70px; color: white;"><div title="Si alguien gana una justa (tirando un dado) tiene que contestar una pregunta monotérmino, si la acierta pasa a la siguiente justa y si la falla hay rebote al perdedor para que conteste, si este falla gana la justa el ganador, hay un tiempo para contestar. Se puede mantener la pregunta para la siguiente justa o no">Justas <?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE']?></div></h1>
  <!--p style="color: white">Suerte a los tres!</p--> 
</div>
  
<div class="row">

<div class="col-lg-5 col-md-5 col-xs-5 thumb">
  <h1 class="Oli2 text-center" style="color: white"><?php echo ($_POST['p1']+1)."º ".$player1['NOMBRE']." ".$player1['APELLIDO1']." ".((((getValorAtributo($dbh,$player1['CORREO'])!="")?'(':'').getValorAtributo($dbh,$player1['CORREO']))).((getValorAtributo($dbh,$player1['CORREO'])!="")?')':'')?></h1>



<?php

  $cromo = getCromo($dbh,$player1['CORREO']);
function url(){
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}
$urlIni = substr(url(),0,strrpos(url(), '/'));
$urlIni = substr($urlIni, 0, -6);
?>

    <img id="imgP1" class="center-block img-responsive" height="50%" width="50%" src="https://www.mtgcardmaker.com/mcmaker/createcard.php?name=<?php echo $cromo['name'];?>&color=<?php echo $cromo['color'];?>&mana_w=<?php echo $cromo['mana_w'];?>&picture=<?php echo htmlentities($urlIni.'/imagesCromos/'.$cromo['picture'])?>&cardtype=<?php echo 
    (($cromo['cardtype']!='')?(
    ((getValorAtributo($dbh,$player1['CORREO'])>=0)?'%2B':'').getValorAtributo($dbh,$player1['CORREO']).'  '
    ):'').$cromo['cardtype'];?>&rarity=<?php echo $cromo['rarity'];?>&cardtext=<?php echo $cromo['cardtext'];?>&power=&toughness=<?php echo $cromo['toughness'];?>&artist=<?php echo $cromo['artist'];?>&bottom=<?php echo $cromo['bottom'];?>" style="border-radius:5%;"/>

</div>
<div class="col-lg-2 col-md-2 col-xs-2 thumb">
  <table><tr><td><img id="idAtras" class="img-responsive" src="img/pp.png" alt=""></td></tr><tr><td><h1 class="Oli2 text-center" style="color: white"><?php echo $_POST['contJusta'] ."/". ($_POST['totalJusta'])?></h1></td></tr>

<tr><td><h1 class="Oli2 text-center" style="color: white">
  <?php 

  if ($_POST['historialGanador']!=-1)
  {

    $niggerA = getAlumnoFromId($dbh, $aTotalAlumnos[$_POST['historialGanador']]);

    echo ($_POST['historialNumMaxGanadas']>0)?('NiggerAward:<br/>'.$niggerA['NOMBRE'].' '.$niggerA['APELLIDO1'].'('.$_POST['historialNumMaxGanadas'].')'):'';
  }

  ?>
    

  </h1></td></tr>

    <tr><td><h1 class="Oli2 text-center" style="color: white">

  <?php  

  if (($_POST['contJusta']>=count($aTotalAlumnos))&&($_POST['contJusta']!=$_POST['totalJusta']))
  {
    echo (($_POST['nRepescas']+1)-($_POST['totalJusta']-$_POST['contJusta']))."ª repesca";
  }
  ?>
    
  </h1></td></tr></table>
        
</div>
<div class="col-lg-5 col-md-5 col-xs-5 thumb">
  <h1 class="Oli2 text-center" style="color: white"><?php echo ($_POST['p2']+1)."º ".$player2['NOMBRE']." ".$player2['APELLIDO1']." ".((((getValorAtributo($dbh,$player2['CORREO'])!="")?'(':'').getValorAtributo($dbh,$player2['CORREO']))).((getValorAtributo($dbh,$player2['CORREO'])!="")?')':'')?></h1>

<?php

  $cromo = getCromo($dbh,$player2['CORREO']);

?>

    <img id="imgP2" class="center-block img-responsive" height="50%" width="50%" src="https://www.mtgcardmaker.com/mcmaker/createcard.php?name=<?php echo $cromo['name'];?>&color=<?php echo $cromo['color'];?>&mana_w=<?php echo $cromo['mana_w'];?>&picture=<?php echo htmlentities($urlIni.'/imagesCromos/'.$cromo['picture'])?>&cardtype=<?php echo 
    (($cromo['cardtype']!='')?(
    ((getValorAtributo($dbh,$player2['CORREO'])>=0)?'%2B':'').getValorAtributo($dbh,$player2['CORREO']).'  '
    ):'').$cromo['cardtype'];?>&rarity=<?php echo $cromo['rarity'];?>&cardtext=<?php echo $cromo['cardtext'];?>&power=&toughness=<?php echo $cromo['toughness'];?>&artist=<?php echo $cromo['artist'];?>&bottom=<?php echo $cromo['bottom'];?>" style="border-radius:5%;"/>


</div>
</div>

<script type="text/javascript">
function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
  $( "#idAtras" ).click(function() {
    <?php 

    if (isset($_POST['historial']))
    {
      $histo = explode("|", $_POST['historial']);
      $elemHisto = explode(",", $histo[$_POST['contJusta']-1]);
      if (count($elemHisto)>1)
      {
echo "document.getElementById('p1').value='".$elemHisto[0]."';";
echo "document.getElementById('p2').value='".$elemHisto[1]."';";
echo "document.getElementById('indicePlayer').value='".$elemHisto[2]."';";
echo "document.getElementById('contJusta').value=document.getElementById('contJusta').value - 2;";


unset($aListaGanadores[count($aListaGanadores)-1]);
echo "document.getElementById('listaGanadores').value='".implode(",", $aListaGanadores)."';";

echo "document.getElementById('numMaxGanadas').value='".$backup_numMaxGanadas."';";
echo "document.getElementById('indiceUltimoGanador').value='".$backup_indiceUltimoGanador."';";
echo "document.getElementById('indiceUltimoGanadorAcum').value='".$backup_indiceUltimoGanadorAcum."';";
echo "document.getElementById('historialGanador').value='".$backup_historialGanador."';";
echo "document.getElementById('historialNumMaxGanadas').value='".$backup_historialNumMaxGanadas."';";



unset($histo[$_POST['contJusta']-1]);
$histoFinal = implode("|", $histo);
echo "document.getElementById('historial').value='".$histoFinal."';";
echo "$( '#form1' ).submit();";
      }
    }
    ?>
    
    

   }); 
  $( "#imgP1" ).click(function() {

        document.getElementById('historialNumMaxGanadas').value =<?php echo $_POST['historialNumMaxGanadas'] ?>; 
    document.getElementById('indiceUltimoGanadorAcum').value =<?php echo $_POST['indiceUltimoGanadorAcum'] ?>;    
    document.getElementById('historialGanador').value =<?php echo $_POST['historialGanador'] ?>;    
    document.getElementById('numMaxGanadas').value =<?php echo $_POST['numMaxGanadas'] ?>;    
    document.getElementById('listaGanadores').value =document.getElementById('listaGanadores').value+','+document.getElementById('p1').value;
    document.getElementById('indiceUltimoGanador').value =document.getElementById('p1').value;
  document.getElementById('indicePlayer').value=parseInt(document.getElementById('indicePlayer').value) - 1;
  if (parseInt(document.getElementById('indicePlayer').value)<0)
  {
$hacerRandom = true;
// ultima repesca se elige al premio nigger
 if ((parseInt(document.getElementById('indicePlayer').value)+parseInt(document.getElementById('nRepescas').value))==0)
 {
    document.getElementById('p2').value=<?php echo $_POST['historialGanador'] ?>;
    if (!(document.getElementById('p1').value==document.getElementById('p2').value))
    {
      $hacerRandom = false;
    }
 }

if ($hacerRandom)
{
    document.getElementById('p2').value=getRandomInt(0, <?php echo ( count($aTotalAlumnos)-1) ?>);
    while (document.getElementById('p1').value==document.getElementById('p2').value)
    {
      document.getElementById('p2').value=getRandomInt(0, <?php echo ( count($aTotalAlumnos)-1) ?>);
    }

}


  }
  else
  {
    document.getElementById('p2').value=document.getElementById('indicePlayer').value;
  }
  if ((parseInt(document.getElementById('indicePlayer').value)+parseInt(document.getElementById('nRepescas').value)+1)==0)
  {
    document.getElementById('nRepescas').value="FIN1";
    document.getElementById('p2').value=<?php echo $_POST['p2'] ?>;
  }
document.getElementById('historial').value='<?php echo $historial ?>';
  $( "#form1" ).submit();
});

  $( "#imgP2" ).click(function() {

        document.getElementById('historialNumMaxGanadas').value =<?php echo $_POST['historialNumMaxGanadas'] ?>; 
        document.getElementById('indiceUltimoGanadorAcum').value =<?php echo $_POST['indiceUltimoGanadorAcum'] ?>; 
    document.getElementById('historialGanador').value =<?php echo $_POST['historialGanador'] ?>;  
    document.getElementById('numMaxGanadas').value =<?php echo $_POST['numMaxGanadas'] ?>;

    document.getElementById('listaGanadores').value =document.getElementById('listaGanadores').value+','+document.getElementById('p2').value;
  document.getElementById('indiceUltimoGanador').value =document.getElementById('p2').value;
  document.getElementById('indicePlayer').value=parseInt(document.getElementById('indicePlayer').value) - 1;
 if (parseInt(document.getElementById('indicePlayer').value)<0)
  {
$hacerRandom = true;
// ultima repesca se elige al premio nigger
 if ((parseInt(document.getElementById('indicePlayer').value)+parseInt(document.getElementById('nRepescas').value))==0)
 {
    document.getElementById('p1').value=<?php echo $_POST['historialGanador'] ?>;
    if (!(document.getElementById('p1').value==document.getElementById('p2').value))
    {
      $hacerRandom = false;
    }
 }

if ($hacerRandom)
{
    document.getElementById('p1').value=getRandomInt(0, <?php echo ( count($aTotalAlumnos)-1) ?>);
    while (document.getElementById('p1').value==document.getElementById('p2').value)
    {
      document.getElementById('p1').value=getRandomInt(0, <?php echo ( count($aTotalAlumnos)-1) ?>);
    }
}


  }
  else
  {
    document.getElementById('p1').value=document.getElementById('indicePlayer').value;
  }

 if ((parseInt(document.getElementById('indicePlayer').value)+parseInt(document.getElementById('nRepescas').value)+1)==0)
  {
    document.getElementById('nRepescas').value="FIN2";
    document.getElementById('p1').value=<?php echo $_POST['p1'] ?>;
  }
document.getElementById('historial').value='<?php echo $historial ?>';
  $( "#form1" ).submit();
});


function validateFin()
{
  <?php
  if ($_POST["nRepescas"]=="FIN1")
  {
  ?>
  Swal.fire({
  title: '<?php echo $player1['NOMBRE']." ".$player1['APELLIDO1'];?>',
  text: '',
  imageUrl: 'img/congratulations.jpg',
  imageWidth: 400,
  imageHeight: 200,
  imageAlt: 'Custom image',
})
  <?php
    }
  ?>
  <?php
  if ($_POST["nRepescas"]=="FIN2")
  {
  ?>
  Swal.fire({
  title: '<?php echo $player2['NOMBRE']." ".$player2['APELLIDO1'];?>',
  text: '',
  imageUrl: 'img/congratulations.jpg',
  imageWidth: 400,
  imageHeight: 200,
  imageAlt: 'Custom image',
})
  <?php
    }
  ?>
}
</script>
</form>
</body>
</html>
<?php


} 
?>