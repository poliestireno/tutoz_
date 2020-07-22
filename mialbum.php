<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");

if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{ 
  header('location:index.php');
}
else{


//var_dump($_POST);
if(isset($_POST['ordentotal']))
{ 
  
  modificarOrdenAlbum($dbh, $_SESSION['alogin'],$_POST["ordentotal"]);   
  modificarOrdenCreadores($dbh, $_SESSION['alogin'],$_POST["creatorstotal"]); 
  modificarOrdenReferenciasTotal($dbh, $_SESSION['alogin'],$_POST["ordenreferenciastotal"]); 
}

 
$ordenAlbumDB = getAlumnoFromCorreo($dbh, $_SESSION['alogin'])['ORDEN_ALBUM'];
$vectorOrden = explode(",", $ordenAlbumDB);



$aRe = getEstrellasCombinaciones($dbh,$_SESSION['alogin']);
$estrellasCombinaciones=$aRe [0];
$sEstrellas=$aRe [1];
$aCombos=$aRe [2];

if(isset($_POST['ordentotal']))
{ 
    modificarOrdenCombos($dbh, $_SESSION['alogin'],$aCombos);   
}

$ordenCombosDB = getAlumnoFromCorreo($dbh, $_SESSION['alogin'])['ORDEN_COMBOS'];

$vectorOrdenCombos = explode(",", $ordenCombosDB);



  $totalEstrellasCromos = 0;
 $vectorCromos = getCromosDeAlbum($dbh,$_SESSION['alogin']);
 foreach ($vectorCromos as $croo) 
 {
    $totalEstrellasCromos +=$croo['mana_w'];
 }

?>




<!DOCTYPE html>
<html lang="en" >
<head>
  <meta charset="UTF-8">
  <title>Mi Album</title>
  <link href="https://fonts.googleapis.com/css?family=Muli" rel="stylesheet">

<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
<style>
.body3 {
   display: flex;
   height: 100vh;
   flex-wrap: wrap;
}

.body2 {
  display: inline-flex;
  flex-direction: row;
  flex-wrap: wrap;
  max-width: 800px;
  margin: 10px;
  height: 100vh;
}

body > * {
   margin: auto;
}

.material-icons {
   color: #fff;
}

.image-thumbnail {
   cursor: all-scroll;
   width: 179px;
   height: 250px;
   background-size: cover;
   background-repeat: no-repeat;
   margin: 5px;
   border-radius: 5px;
   display: flex;
   flex-direction: column;
   justify-content: space-between;
}

.image-thumbnail:hover {
   background-blend-mode: overlay;
   background-color: #555;
}

.image-thumbnail:hover .actions {
   visibility: visible;
}

.image-thumbnail-dragging {
   position: absolute;
   cursor: all-scroll;
   width: 179px;
   height: 250px;
   background-size: cover;
   background-repeat: no-repeat;
   margin: 5px;
   border-radius: 5px;
   display: flex;
   flex-direction: column;
   justify-content: space-between;
}

[selected] .details  {
   background: #303F9F;
}

[selected] .actions {
   visibility: visible;
}

[selected] .actions i {
   color: #C2185B;
}

.actions {
   visibility: hidden;
   display: flex;
   height: 40px;
   flex-direction: row;
   align-items: center;
   justify-content: space-between;
   padding: 0 11px;
}

.actions > * {
   cursor: pointer;
}

.details {
   height: 46px;
   background-color: rgba(0, 0, 0, 0.87);
   display: flex;
   flex-direction: row;
   align-items: center;
}

.details span {
   font-family: Muli;
   font-size: 12.6px;
   font-style: normal;
   font-stretch: normal;
   color: #ffffff;
   text-overflow: ellipsis;
   white-space: nowrap;
}

.details__title {
   flex: 1;
   margin-left: 6px;
   margin-right: 34px;
   white-space: nowrap;
   overflow: hidden;
   text-overflow: ellipsis;
}

.details__sequence {
   background-color: rgba(255, 255, 255, .24);
   width: 22px;
   height: 22px;
   display: inline-flex;
   align-items: center;
   font-weight: initial;
   border-top-right-radius: 15px;
   border-bottom-right-radius: 15px;
   padding-left: 6px;
}

</style>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
</head>
<body>
  <form id="form2" method="post" action="profile.php">
    <input type='hidden' name='ordentotal' id='ordentotal'/>
    <input type='hidden' name='creatorstotal' id='creatorstotal'/>
    <input type='hidden' name='ordenreferenciastotal' id='ordenreferenciastotal'/>

<div style="margin: 10px">
<a onclick="managebuttonDash()"  class="btn btn-danger btn-outline btn-wrap-text">Volver</a>
<?php
echo '<span class="label label-danger">PREMIOS</span>';
echo '<span class="label label-primary">PAREJA</span>';
echo '<span class="label label-success">'.getAdminCromos($dbh)['PAREJA'].'</span>';
echo '<span class="label label-primary">DOBLE PAREJA</span>';
echo '<span class="label label-success">'.getAdminCromos($dbh)['DOBLEPAREJA'].'</span>';
echo '<span class="label label-primary">TRIO</span>';
echo '<span class="label label-success">'.getAdminCromos($dbh)['TRIO'].'</span>';
echo '<span class="label label-primary">CUARTETO</span>';
echo '<span class="label label-success">'.getAdminCromos($dbh)['CUARTETO'].'</span>';
echo '<span class="label label-primary">ESCALERA(3)(referencia)</span>';
echo '<span class="label label-success">'.getAdminCromos($dbh)['ESCALERA3'].'</span>';
echo '<span class="label label-primary">ESCALERA(4)(referencia)</span>';
echo '<span class="label label-success">'.getAdminCromos($dbh)['ESCALERA4'].'</span>';
echo '<span class="label label-primary">ESCALERA(3)(estrellas)</span>';
echo '<span class="label label-success">'.getAdminCromos($dbh)['ESCALERA3_ESTRELLAS'].'</span>';
echo '<span class="label label-primary">ESCALERA(4)(estrellas)</span>';
echo '<span class="label label-success">'.getAdminCromos($dbh)['ESCALERA4_ESTRELLAS'].'</span>';
?>


</div>
<div style="margin: 10px">
<?php
echo '<span class="label label-danger">ESTRELLAS</span>';
echo '<span class="label label-success">'.($totalEstrellasCromos+$estrellasCombinaciones).'</span>';
?>
<div >
<?php
echo '<span class="label label-danger">CROMOS</span>';
echo '<span class="label label-success">'.$totalEstrellasCromos.'</span>';
?>
</div>
<div >
<?php
echo '<span class="label label-danger">COMBINACIONES</span>';
echo '<span class="label label-warning">'.$sEstrellas.'</span>';
echo '<span class="label label-success">'.$estrellasCombinaciones.'</span>';
?>
</div>



</div>



<!-- partial:index.partial.html -->
<?php 

function url(){
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}


// var_dump($vectorCromos);
 $vectorOrdenado = array();
if ($vectorOrden[0]!="")  
{
//  var_dump($vectorOrden);
 
foreach ($vectorOrden as $ordenI) {
    if ($ordenI!=-1)
    { 
      for ($i=0; $i < Count($vectorCromos); $i++) { 
        if ($vectorCromos[$i]['ID']==$ordenI)
        {
          $vectorOrdenado[]=$vectorCromos[$i];
          //unset($vectorCromos[$i]);
          $vectorCromos[$i]=NULL;
          break;
        }
      }
    }
    else
    {
      $vectorOrdenado[]="-1";
    }
}
//  var_dump($vectorCromos);
  foreach ($vectorCromos as $cc) 
  {
    if ($cc!=NULL)
    {
      for ($i=Count($vectorOrdenado)-1; $i >=0 ; $i--) 
      {
        if ($vectorOrdenado[$i]=="-1")
        {
          $vectorOrdenado[$i]=$cc;
          break;
        }
      }
    }
  }
}
else
{
  $vectorOrdenado=$vectorCromos;
}
$cont = 0;
//var_dump($vectorOrdenado);

$num = Count($vectorOrdenado);
$num = $num % 4;
for ($i=0; ( ($num>0) && $i < (4 - $num)); $i++) 
{ 
  $vectorOrdenado[]="-1";
}

$numSelect = getAdminCromos($dbh)['NUM_SLOTS'];
$numSelectRellenos = round(Count($vectorOrdenado) / 4);

for ($i=0; $i < ($numSelect-$numSelectRellenos); $i++) 
{ 
  $vectorOrdenado[]="-1";
  $vectorOrdenado[]="-1";
  $vectorOrdenado[]="-1";
  $vectorOrdenado[]="-1";
}



  ?>
<div class="input-group">
<?php 
for ($i=1; $i < $numSelect+1; $i++) 
{ 
  ?>
    <span class="input-group-addon" title="* Fila <?php echo $i?>" id="ffff">Fila <?php echo $i?></span>
        <!-- insert this line -->
    <span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>
  
  <select class="form-control" id="selfila_<?php echo $i?>" name="selfila_<?php echo $i?>">

        <option value="0"></option>
        <option value="1" <?php if (Count($vectorOrdenCombos)>1) echo (($vectorOrdenCombos[$i-1]=="1")?" selected='selected' ":"")?>>pareja</option>
        <option value="2" <?php if (Count($vectorOrdenCombos)>1) echo (($vectorOrdenCombos[$i-1]=="2")?" selected='selected' ":"")?>>doble pareja</option>
        <option value="3" <?php if (Count($vectorOrdenCombos)>1) echo (($vectorOrdenCombos[$i-1]=="3")?" selected='selected' ":"")?>>trio</option>
        <option value="4" <?php if (Count($vectorOrdenCombos)>1) echo (($vectorOrdenCombos[$i-1]=="4")?" selected='selected' ":"")?>>cuarteto</option>
        <option value="5" <?php if (Count($vectorOrdenCombos)>1) echo (($vectorOrdenCombos[$i-1]=="5")?" selected='selected' ":"")?>>escalera(3)(referencia)</option>
        <option value="6" <?php if (Count($vectorOrdenCombos)>1) echo (($vectorOrdenCombos[$i-1]=="6")?" selected='selected' ":"")?>>escalera(4)(referencia)</option>
        <option value="7" <?php if (Count($vectorOrdenCombos)>1) echo (($vectorOrdenCombos[$i-1]=="7")?" selected='selected' ":"")?>>escalera(3)(estrellas)</option>
        <option value="8" <?php if (Count($vectorOrdenCombos)>1) echo (($vectorOrdenCombos[$i-1]=="8")?" selected='selected' ":"")?>>escalera(4)(estrellas)</option>
      </select>

<?php 
}

?>
 <span class="input-group-addon" style="width:0px; padding-left:0px; padding-right:0px; border:none;"></span>
<a onclick="calcular()" name="calcular" class="btn btn-danger btn-outline btn-wrap-text form-control">Calcular</a>
</div>

<div class="body2">
<?php 

foreach ($vectorOrdenado as $cromo) 
{
  if ($cromo!="-1")
  {
?>
<div id= "kko_<?php echo $cont?>" 
  data-creador="<?php echo $cromo['ID_CREADOR']?>" 
  data-estrellas="<?php echo $cromo['mana_w']?>" 
  data-orden="<?php echo $cromo['power']?>"  
  data-idcromo="<?php echo $cromo['ID']?>"  

  class="image-thumbnail"

style="background-image: url('https://www.mtgcardmaker.com/mcmaker/createcard.php?name=<?php echo $cromo['name'];?>&color=<?php echo $cromo['color'];?>&mana_w=<?php echo $cromo['mana_w'];?>&picture=<?php echo htmlentities(substr(url(),0,strrpos(url(), '/')).'/imagesCromos/'.$cromo['picture'])?>&cardtype=<?php echo $cromo['cardtype'];?>&rarity=<?php echo $cromo['rarity'];?>&cardtext=<?php echo $cromo['cardtext'];?>&power=<?php echo $cromo['power'];?>&toughness=<?php echo $cromo['toughness'];?>&artist=<?php echo $cromo['artist'];?>&bottom=<?php echo $cromo['bottom'];?>')"

     onmousemove="move(event)"
     ontouchmove="move(event)"

     onmousedown="moveStart(event)"
     ontouchstart="moveStart(event)"

     onmouseup="moveEnd()"
     ontouchend="moveEnd()"
   >
</div>
<?php 

  }
  else
  {
 ?>
<div id= "kko_<?php echo $cont?>" 
  data-creador="-1" 
  data-estrellas="-1" 
  data-orden="-1"  
  data-idcromo="-1"  

  class="image-thumbnail"

style="background-color: black"

     onmousemove="move(event)"
     ontouchmove="move(event)"

     onmousedown="moveStart(event)"
     ontouchstart="moveStart(event)"

     onmouseup="moveEnd()"
     ontouchend="moveEnd()"
   >
</div>
<?php 
   
  }


$cont = $cont + 1;
}

?>
</div>

<!-- partial -->
  <script>


  function managebuttonDash()
  { 
      document.getElementById("form2").submit();
  }


  function calcular()
  { 
      

      // fila 1 0-3
      // fila 2 4-7
      // fila 3 8-11
      // 0 pareja 1 doblepareja 2 trio 3 escalera34 escalera4
     /* $('[id^=selfila]').each(function() 
      {
        //alert(this.dataset.creador+","+this.dataset.estrellas+","+this.dataset.orden+","+this.dataset.idcromo);
        alert(this.value);

      });
     */


      
      comma="";
      ordenTotal="";
      ordenreferenciastotal="";
      creatorsTotal="";
     // estrellasCromosTotal=0;
      $('[id^=kko]').each(function() 
      {
        //alert(this.dataset.creador+","+this.dataset.estrellas+","+this.dataset.orden+","+this.dataset.idcromo);
        ordenTotal+=comma+this.dataset.idcromo;
        ordenreferenciastotal+=comma+this.dataset.orden;
        creatorsTotal+=comma+this.dataset.creador;
       // estrellasCromosTotal+=(this.dataset.estrellas==-1)?0:parseInt(this.dataset.estrellas);
        comma=",";
      });
     // alert(estrellasCromosTotal);
      document.getElementById("creatorstotal").value=creatorsTotal;
      document.getElementById("ordentotal").value=ordenTotal;
      document.getElementById("ordenreferenciastotal").value=ordenreferenciastotal;
      document.getElementById("form2").action="mialbum.php";
      document.getElementById("form2").submit();

  }

  /**
   * Global State
   * { Node } placingElement - Element being translated
   * { Node } movingElement - Element visually translated
   */
  let placingElement;
  let movingElement;

  /**
   * move: This callback is invoked when the move starts (touchmove / mousemove)
   * @function move
   * @param { Event } event - Fired event
   */
  function move(event) {
     // If there's an moving element
     if(movingElement && placingElement) {
        // grab the location of touch
        const eventLocation = event.targetTouches && event.targetTouches[0] || event;

        // assign box new coordinates based on the touch.
        const x = (eventLocation.pageX - (movingElement.offsetWidth / 2)) + 'px';
        const y = (eventLocation.pageY - (movingElement.offsetHeight / 2)) + 'px';
        movingElement.style.transform = `translate(${x}, ${y})`;

        // get the element being hovered (you need a class / localName / id to indentify it)
        const target = document.elementsFromPoint(eventLocation.clientX, eventLocation.clientY).find(element => Array.from(element.classList).includes('image-thumbnail'));

        // If there's a target then place the node
        if(target) {
           placeNode(placingElement, target)
        }
     }
  }

  /**
   * moveStart: This callback is invoked before the move starts (touchstarts / mousedown)
   * @function moveStart
   * @param { Event } event - Fired event
   */
  function moveStart(event) {
     // If there's an moving element
     if(!movingElement && !placingElement && Array.from(event.target.classList).includes('image-thumbnail')) {
        // Event Location
        const eventLocation = event.targetTouches && event.targetTouches[0] || event;
        // Define the global states
        movingElement = event.target.cloneNode();
        placingElement = event.target;
        // Attach Visual Translate
        document.body.appendChild(movingElement);

        // assign box new coordinates based on the touch.
        movingElement.classList = ['image-thumbnail-dragging'];
        // assign box new coordinates based on the touch.
        const x = (eventLocation.pageX - (movingElement.offsetWidth / 2)) + 'px';
        const y = (eventLocation.pageY - (movingElement.offsetHeight / 2)) + 'px';
        movingElement.style.willChange = 'transform';
        movingElement.style.transform = `translate(${x}, ${y})`;
     }
  }

  /**
   * moveEnd: Callback invoked when the move ends (touchends / mouseup)
   * @function moveEnd
   */
  function moveEnd() {
     // Remove Moving element and clean placing element
     if (movingElement) {
        document.body.removeChild(movingElement);
     }
     movingElement = null;
     placingElement = null;
  }

  /**
   * placeNode: This method places the firstNode before or after the nextNode based on the presedence
   * @function placeNode
   * @param { Node } firstNode - Element to place
   * @param { Node } nextNode - Element in reference to place the element (before / after)
   */
  function placeNode (firstNode, nextNode) {
     if (isBefore(firstNode, nextNode)) {
        nextNode.parentNode.insertBefore(firstNode, nextNode);
     }
     else {
        nextNode.parentNode.insertBefore(firstNode, nextNode.nextSibling);
     }
  }

  /**
   * isBefore: This tells if the firstNode is located before or after the nextNode
   * @function isBefore
   * @param { Node } firstNode - Element being dragged
   * @param { Node } nextNode - Element targeted
   */
  function isBefore(firstNode, nextNode) {
     let sibling = firstNode.previousSibling;
     while(sibling) {
        if(sibling === nextNode) {
           return true;
        }
        sibling = sibling.previousSibling;
     }
     return false;
  }

  </script>
  

<?php 
} 
  
?>  
</body>
</html>
