<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{   
    header('location:index.php');
} 
if (!isset($_SESSION['jugando']))
{
    header('location:index.php');
}
function url()
{
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    $_SERVER['REQUEST_URI']
  );
}
function getAsteriscos($n)
{
  $sAsteriscos = "";
  for ($i=0; $i < $n ; $i++) 
  { 
    $sAsteriscos = $sAsteriscos ."*";
  }
  return $sAsteriscos;
}


$alumno =getAlumnoFromCorreo($dbh,$_SESSION['alogin']);
$actor = getMiActorFromAlumnoID($dbh,$alumno['ID']);
$compraRealizada = 0;
if (isset($_POST['idProducto']))
{
    $arti = getArticuloFromId($dbh,$_POST['idProducto']);
    
    if ($actor['CALAS']>=$arti['PRECIO'])
    {

        modificarCalas($dbh,$_SESSION['alogin'],-1*$arti['PRECIO']);
        modificarCantidadArticulo($dbh,$_POST['idProducto'],-1);
        insertarArticuloAlumno($dbh,$_POST['idProducto'],$alumno['ID'],'pendiente','Compra de '.$arti['NOMBRE'].' a cargo de '.$alumno['NOMBRE'].' '.$alumno['APELLIDO1'].' '.$alumno['APELLIDO2'],'');   
        $compraRealizada = 1;
    }
    else
    {
        $compraRealizada = -1;
    }
}
else if(isset($_POST['bComentario']))
{
    modificarComentarioCompraFromCompraId($dbh,$_POST['compraI'],$_POST['texa']);
}




$asignatura = getAsignaturasFromCurso($dbh,$alumno['ID_CURSO'])[0];

$aArticulos = getArticulosFromTienda($dbh,getTiendaFromAsignatura($dbh,$asignatura['ID'])['ID']);
?>
<!doctype html>
<html lang="en" class="no-js">

<head>
    <title>Tienda Susi</title>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">

  
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

  <style type="text/css">
.vcenter {
  display: flex;
  justify-content:center; //making content center;
  align-items:center; //vertical align to middle of div  
}
h2 span {
    font-size: 10pt;
}

.item {
    padding-top:20px;
    position:relative;
    display:inline-block;
}
.notify-badge{
    left: 900px;
    position: absolute;  
    font-size: 10pt;
    border-radius: 5px 5px 5px 5px;
    background: #131921;
    color: #c9cccf;
}
body
{
    background-color:#131921;
}
 
        @font-face {
  font-family: 'Oli2';
  src: 
    url('fonts/Bookerly-Regular.ttf') format('truetype');
}
* {
      font-family: 'Oli2';
    }

.Oli2    {
    font-family: 'Oli2';
    text-align: center;
}

  </style>

    <?php


//var_export($_POST);

//$idTarea = (isset($_GET['idt']))?$_GET['idt']:$_POST['idt'];



?>



    <script type="text/javascript">

function init()
{
    <?php
    if ($compraRealizada ==-1)
    {
        echo "Swal.fire('UFFF!','No tienes suficientes calas para comprar el artículo' ,'error');";
    }
    else if ($compraRealizada ==1)
    {
        echo "Swal.fire('GUAY!','Artículo correctamente pedido, su entrega puede sufrir una pequeña demora debido que estamos en ERTE parcial por causas varias y actualmente solamente tenemos un trabajador en plantilla, aun así en menos de 32 horas te llegará por SusiAmazon. Si necesitas algo sobre este pedido escribe un comentario en el propio pedido en la sección de abajo de \"Mis pedidos realizados\"' ,'success');";
    }
    ?>
}

    function submitComprar(idProducto)
    { 
      document.getElementById("idProducto").value=idProducto;
      Swal.fire({
        title: 'Confirmación',
        text: '¿Seguro que quieres realizar la compra?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí'
      }).then((result) => {
        if (result.value) {
          document.getElementById("f1").submit();
        }
      })      
    }    
</script>
</head>

<body onload="init()">

<form id="f1" action="tienda.php" method="POST">
 <input type="hidden" name="idProducto" id="idProducto">   
</form>

<div class="container">

    <div class="d-flex justify-content-center item">
<span class="notify-badge"><b><?php echo 'Tienes '.getMiActorFromAlumnoID($dbh,$alumno['ID'])['CALAS']?> calas&nbsp;</b></span>
            <img src="images/ama.png"  alt="" />
    </div>
    <br/>

<?php foreach ($aArticulos as $articulo) {
    $dispo = !($articulo['CANTIDAD']<=0);
    $disponible = $articulo['CANTIDAD'].' disponible'.(($articulo['CANTIDAD']==1)?'':'s');
    if (!$dispo)
    {
        $disponible = ' proximamente';
    }
?>

<div class="text-center" style="padding-bottom:10px;">
  <?php 
  if (($articulo['ID_CROMO'])!=NULL)
  {
    $cromo = getCromoFromID($dbh,$articulo['ID_CROMO']);
 ?>

  <h2 style="color: #c9cccf;"><?php echo $articulo['NOMBRE'].' '.$cromo['name']." | ".$cromo['power']." | ".getAsteriscos($cromo['mana_w']).'<span style="font-size=10px"> ('.$disponible.') </span>'?></h2>
  <div style="background-color:white; border-color: #131921; border-radius: 10px;height: 300px;width: 500px;border: 2px solid; text-align: center; margin: 0 auto;padding: 30px" class="vcenter center-block">
    <div class="text-center" style="padding-bottom:10px;">

<img src='https://www.mtgcardmaker.com/mcmaker/createcard.php?name=<?php echo $cromo['name'];?>&color=<?php echo $cromo['color'];?>&mana_w=<?php echo $cromo['mana_w'];?>&picture=<?php echo htmlentities(substr(url(),0,strrpos(url(), '/')).'/imagesCromos/'.$cromo['picture'])?>&cardtype=<?php echo $cromo['cardtype'];?>&rarity=<?php echo $cromo['rarity'];?>&cardtext=<?php echo $cromo['cardtext'];?>&power=<?php echo $cromo['power'];?>&toughness=<?php echo $cromo['toughness'];?>&artist=<?php echo $cromo['artist'];?>&bottom=<?php echo $cromo['bottom'];?>' width="150px" height="230px" />

    <h5><?php echo $articulo['DESCRIPCION'];                                                                ;
if (strpos($articulo['NOMBRE'], 'Donaci')!==false) 
{
  echo ' La recaudación actual es de <b>'.$asignatura['TOTAL_DONACION'].'</b> calas.';
}
    ?></h5>
    </div>
    <div class="text-center" style="padding-bottom:10px;">

      <a onclick="submitComprar(<?php echo $articulo['ID']?>)" style="<?php echo ((!$dispo)?'pointer-events: none;cursor: default;':'')?>  background: #131921;color: white" class="btn btn-squared-default">
                    <!--i class="fa fa-money fa-3x"></i-->
                    <span class="badge badge-warning"><?php echo $articulo['PRECIO']?> calas</span>
                    <br />
                
                <span style="color: #c9cccf;"><?php echo (($dispo)?'comprar':'agotado')?></span>
                </a>
    </div>
  </div>

  <?php   
  }
else
{

  ?>
  <h2 style="color: #c9cccf;"><?php echo $articulo['NOMBRE'].'<span style="font-size=10px"> ('.$disponible.') </span>'?></h2>
  <div style="background-color:white; border-color: #131921; border-radius: 10px;height: 300px;width: 500px;border: 2px solid; text-align: center; margin: 0 auto;padding: 30px" class="vcenter center-block">
    <div class="text-center" style="padding-bottom:10px;">
      <img class="img-fluid mx-auto d-block" src="images/articulos/<?php 
        $dbImage = htmlentities($articulo['IMAGEN']);
        if (($dbImage!="")&&(file_exists("images/articulos/".$dbImage)))
        {   
            echo $dbImage;
        }
        else
        {
            echo "interrogante.png";
        }
        ?>" width="100px" height="80px">
    <h5><?php echo $articulo['DESCRIPCION'];                                                                ;
if (strpos($articulo['NOMBRE'], 'Donaci')!==false) 
{
  echo ' La recaudación actual es de <b>'.$asignatura['TOTAL_DONACION'].'</b> calas.';
}
    ?></h5>
    </div>
    <div class="text-center" style="padding-bottom:10px;">

      <a onclick="submitComprar(<?php echo $articulo['ID']?>)" style="<?php echo ((!$dispo)?'pointer-events: none;cursor: default;':'')?>  background: #131921;color: white" class="btn btn-squared-default">
                    <!--i class="fa fa-money fa-3x"></i-->
                    <span class="badge badge-warning"><?php echo $articulo['PRECIO']?> calas</span>
                    <br />
                
                <span style="color: #c9cccf;"><?php echo (($dispo)?'comprar':'agotado')?></span>
                </a>
    </div>
  </div>

<?php } ?>
</div><br/>
<?php } 

$aComprasAlumno = getComprasFromAlumno($dbh,$alumno['ID']);

?>


             <div class="row">

              <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading"><span  style="color: #c9cccf;" > Mis pedidos realizados(<?php echo count($aComprasAlumno)?>)</span></div>
                  
                  <div  style="background: white;overflow: hidden;border-color: #131921; border-radius: 10px;border: 2px solid; margin: 0 auto;padding: 3px" class="panel-body">


<?php

$contCompra=0;
foreach ($aComprasAlumno as $compraI) {
$contCompra++;
//var_export($compraI);
$textoEstado=$compraI['ESTADO'];
$styleBackLabel ="";
if ($compraI['ESTADO']=='pendiente')
{
    $textoEstado='pendiente de entrega';
    $styleBackLabel ="style='background-color: orange'";
}
else if ($compraI['ESTADO']=='devuelto')
{
    $styleBackLabel ="style='background-color: red'";
}
?>

<form method="post" class="form-horizontal" enctype="multipart/form-data" >
<input type="hidden" name="compraI" value="<?php echo $compraI['ID'];?>">
<div class="form-group">
  <div class="col-sm-4">
  </div>
  <div class="col-sm-4">
  </div>
</div>



<div class="form-group">
  <label <?php echo $styleBackLabel;?>  class="col-sm-4 control-label">Pedido nº <?php echo $compraI['ID']." (".$textoEstado.")"?></label>
  <div class="col-sm-9">
  <?php 

$artiAux= getArticuloFromId($dbh,$compraI['ID_ARTICULO']);

    echo "</b>ARTÍCULO:<b>".$artiAux['NOMBRE']
    ."</b><br/>PRECIO:".$artiAux['PRECIO']."<br/>DESCRIPCIÓN:".$artiAux['DESCRIPCION']."<br/>FECHA COMPRA:".$compraI['FECHA_COMPRA']."<br/>COMENTARIO:";
    ?>
      <div class="md-form">
  <textarea name="texa" id="texa" class="md-textarea form-control" rows="3"><?php echo $compraI['COMENTARIO']?></textarea>
</div>
<button class="btn" style="color: #c9cccf;background: #131921" name="bComentario" type="submit">Enviar comentario</button>

    
  </div>
</div>

</form>
<br/>
<?php

}
?>

                  </div>
                  </div>
                </div>              
              </div>
</div>

</body>
</html>