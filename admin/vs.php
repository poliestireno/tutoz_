<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";
$sql = "SELECT username from admin;";
    $query = $dbh -> prepare($sql);
    $query->execute();
    $result=$query->fetch(PDO::FETCH_OBJ);

if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))
  { 
header('location:index.php');
}
else{
  
$aFrasesAzar = array ("¿No es, a veces, el azar la más insolente de las aristocracias?. ETIENNE REY",
"El azar es un milagro disfrazado. ALEJANDRO JODOROWSKY",
"Para un hombre sensible no existe esa cosa que llaman azar. LUDWIG TIECK",
"Nuestra sabiduría no se encuentra menos a merced del azar que nuestra propiedad. FRANÇOIS DE LA ROCHEFOUCAULD",
"Es el azar, no la prudencia, quien rige la vida. CICERÓN",
"El azar favorece a una mente bien entrenada. LOUIS PASTEUR",
"Tampoco es inescrutable el azar, también está regido por un orden. NOVALIS",
"Descubrí que la vida es un juego de azar donde pierde el que gana. RICARDO ARJONA",
"La suerte es el azar aprovechado. JOAQUÍN LORENTE",
"Todas las cosas del mundo son hijas o nietas del azar. JOAN MARAGALL Y GORINA",
"El azar es una explicación que sólo tranquiliza a los idiotas. ARTURO PÉREZ-REVERTE",
"Hombre, hazte esencial: cuando el mundo pase, lo que es del azar caerá; la esencia quedará. ANGELUS SILESIUS",
"Los azares de la vida son tales, que toda eventualidad se hace posible. ANDRÉ MAUROIS",
"La Providencia es el nombre cristiano de bautismo para el azar. ALPHONSE KARR",
"¿para qué llamar caminos a los surcos del azar?... Todo el que camina anda, como jesús, sobre el mar. ANTONIO MACHADO Y RUIZ",
"El azar o su pariente de gala, el destino. CARLOS RUIZ ZAFÓN",
"Hay personas que no pueden contar con nada, ni siquiera con el azar, pues hay existencias sin azar. HONORÉ DE BALZAC",
"El futuro es un campo de minas en el que estamos condenados a pisar al azar. JAVIER SANZ",
"Ocurre lo mismo en el caso de la probabilidad de las causas que en la del azar. DAVID HUME",
"Las mejores cosas pasan por azar, porque así es la vida. ELLEN LEE DEGENERES",
"En el mundo todo es señal, amigo mío. El azar no existe. ANTONIO BUERO VALLEJO",
"No del pasado azar que considera, la vida crece sólo dilatada, ni el objeto futuro la sustenta. JORGE CUESTA",
"Si nuestro origen es el azar, ¿qué podemos tomarnos en serio?. JAVIER SANZ",
"No ha de maravillarnos que el azar pueda tanto sobre nosotros partiendo de que vivimos por azar. MICHEL EYQUEM",
"El azar solo sabe escribir ironías. JAVIER SANZ",
"Depende del azar que estemos atados a una rueda móvil o a una inmóvil. Desatarse siempre es difícil. ITALO SVEVO",
"En el reino del azar todo es ironía. JAVIER SANZ",
"El amor, del mismo modo que el azar, ignora la moral. Son dos cómplices nacidos para entenderse. ETIENNE REY");


//var_export($_POST);



  $idCur=$_POST['idc'];
  shuffle($_POST['cCheckIds']);
  $idJugador1 = $_POST['cCheckIds'][0];
  $idJugador2 = $_POST['cCheckIds'][1];




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
  </style>
</head>
<body onload="validate();" style="background-color: black">
<form action="lista_justas.php" id="form3" method="post">
<input type="hidden" name="idc4" id="idc4" value="<?php echo $_POST['idc']?>"/>
<input type="hidden" name="idc" id="idc" value="<?php echo $_POST['idc']?>"/>
<?php
  $player1 = getAlumnoFromId($dbh, $idJugador1);
  $player2 = getAlumnoFromId($dbh, $idJugador2);
?>
<div>
  <h1 class="Oli2" ><span style="font-size: 70px; color: white;" title="la justa se calcula los dados más los atributos">Justas <?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE']?></span><span  title="la justa se calcula los dados más los atributos"><a style="font-size: 20px; color: white;" onclick="volverLista();">[volver a la lista]</a></span></h1>
  <!--p style="color: white">Suerte a los tres!</p--> 
</div>
  
<div class="row">

<div class="col-lg-4 col-md-4 col-xs-4 thumb">
  <h1 class="Oli2 text-center" style="color: white"><?php echo $player1['NOMBRE']." ".$player1['APELLIDO1']." ".((((getValorAtributo($dbh,$player1['CORREO'])!="")?'(':'').getValorAtributo($dbh,$player1['CORREO']))).((getValorAtributo($dbh,$player1['CORREO'])!="")?')':'')?></h1>



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
    <div class="dice">
   <ol class="die-list even-roll" data-roll="1" id="die-1">
      <li class="die-item" data-side="1"> <span class="dot"></span> </li>
      <li class="die-item" data-side="2"> <span class="dot"></span> <span class="dot"></span> </li>
      <li class="die-item" data-side="3"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li>
      <li class="die-item" data-side="4"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li>
      <li class="die-item" data-side="5"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li>
      <li class="die-item" data-side="6"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li>
   </ol>
</div>
</div>
<div class=" col-md-2  ">
  <table><tr><td><img onclick="rollDice()" id="idAtras" class="img-responsive" src="img/pp.png" alt=""></td></tr>



    <tr><td><h2 class="Oli2" style="color: white"><?php echo $aFrasesAzar[rand(0,count($aFrasesAzar)-1)]?></h2></td></tr></table>
        
</div>
<div class="col-lg-4 col-md-4 col-xs-4 thumb">
  <h1 class="Oli2 text-center" style="color: white"><?php echo $player2['NOMBRE']." ".$player2['APELLIDO1']." ".((((getValorAtributo($dbh,$player2['CORREO'])!="")?'(':'').getValorAtributo($dbh,$player2['CORREO']))).((getValorAtributo($dbh,$player2['CORREO'])!="")?')':'')?></h1>

<?php

  $cromo = getCromo($dbh,$player2['CORREO']);

?>

    <img  id="imgP2" class="center-block img-responsive" height="50%" width="50%" src="https://www.mtgcardmaker.com/mcmaker/createcard.php?name=<?php echo $cromo['name'];?>&color=<?php echo $cromo['color'];?>&mana_w=<?php echo $cromo['mana_w'];?>&picture=<?php echo htmlentities($urlIni.'/imagesCromos/'.$cromo['picture'])?>&cardtype=<?php echo 
    (($cromo['cardtype']!='')?(
    ((getValorAtributo($dbh,$player2['CORREO'])>=0)?'%2B':'').getValorAtributo($dbh,$player2['CORREO']).'  '
    ):'').$cromo['cardtype'];?>&rarity=<?php echo $cromo['rarity'];?>&cardtext=<?php echo $cromo['cardtext'];?>&power=&toughness=<?php echo $cromo['toughness'];?>&artist=<?php echo $cromo['artist'];?>&bottom=<?php echo $cromo['bottom'];?>" style="border-radius:5%;"/>

    <div class="dice">
   <ol class="die-list even-roll" data-roll="1" id="die-1">
      <li class="die-item" data-side="1"> <span class="dot"></span> </li>
      <li class="die-item" data-side="2"> <span class="dot"></span> <span class="dot"></span> </li>
      <li class="die-item" data-side="3"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li>
      <li class="die-item" data-side="4"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li>
      <li class="die-item" data-side="5"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li>
      <li class="die-item" data-side="6"> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> <span class="dot"></span> </li>
   </ol>
</div>
</div>
</div>

<script type="text/javascript">

function volverLista()
{
  document.getElementById("form3").submit(); 
}
function validate()
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

</script>
</form>
</body>
</html>
<?php


} 






?>