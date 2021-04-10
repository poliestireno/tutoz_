<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";

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


if (isset($_GET['idc7']))
{
  $idCur=$_GET['idc7'];
}
else
{
  $idCur=$_POST['idc7'];
}
if(isset($_POST['bOk']))
{ 
  
modificarEstadoCompraFromCompraId($dbh,$_POST['compraI'],'entregado');
$laCompra = getCompraFromId($dbh,$_POST['compraI']);
$alumnoAA = getAlumnoFromId($dbh,$laCompra['ID_ALUMNO']);
$articuloAA = getArticuloFromId($dbh,$laCompra['ID_ARTICULO']);
$message = "Hola ".$alumnoAA['NOMBRE']."!, estimado comprador de SalleZAmazon, el artículo ".$articuloAA['NOMBRE']." ha sido entregado, si no lo tienes contesta a este correo.
Un gran saludo.
Tuto Z";
  $subject="SalleZAmazon Entrega artículo ".$articuloAA['NOMBRE'] ;
  $to=$alumnoAA['CORREO'];

  $okEnvio = enviarCorreo($to,$subject,$message);
  if (!$okEnvio)
  {
    mi_info_log( 'Error al enviar a:'.$to." con subject:".$subject);
  }
    $msg="Compra ENTREGADA correctamente";
}
else if(isset($_POST['bKo']))
{
  $laCompra = getCompraFromId($dbh,$_POST['compraI']);
$alumnoAA = getAlumnoFromId($dbh,$laCompra['ID_ALUMNO']);
$articuloAA = getArticuloFromId($dbh,$laCompra['ID_ARTICULO']);
  modificarEstadoCompraFromCompraId($dbh,$_POST['compraI'],'devuelto');
  modificarCalas($dbh,$alumnoAA['CORREO'],$articuloAA["PRECIO"]);
modificarCantidadArticulo($dbh,$laCompra['ID_ARTICULO'],1);

$message = "Hola ".$alumnoAA['NOMBRE']."!, estimado comprador de SalleZAmazon, el artículo ".$articuloAA['NOMBRE']." ha sido devuelto, si tienes alguna duda contesta a este correo.
Un gran saludo.
Tuto Z";
  $subject="SalleZAmazon Devolución artículo ".$articuloAA['NOMBRE'] ;
  $to=$alumnoAA['CORREO'];

  $okEnvio = enviarCorreo($to,$subject,$message);
  if (!$okEnvio)
  {
    mi_info_log( 'Error al enviar a:'.$to." con subject:".$subject);
  }

$msg="Compra DEVUELTA correctamente";

}   
else if(isset($_POST['bPen']))
{
modificarEstadoCompraFromCompraId($dbh,$_POST['compraI'],'pendiente');
 
    $msg="Compra PUESTA A PENDIENTE correctamente";

}   
else if(isset($_POST['bComentario']))
{
modificarComentarioCompraFromCompraId($dbh,$_POST['compraI'],$_POST['texa']);
 
    $msg="Modificado Comentario correctamente";

}   
?>

<!doctype html>
<html lang="en" class="no-js">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="theme-color" content="#3e454c">
  
  <title>Compras artículos</title>

  <!-- Font awesome -->
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <!-- Sandstone Bootstrap CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- Bootstrap Datatables -->
  <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
  <!-- Bootstrap social button library -->
  <link rel="stylesheet" href="css/bootstrap-social.css">
  <!-- Bootstrap select -->
  <link rel="stylesheet" href="css/bootstrap-select.css">
  <!-- Bootstrap file input -->
  <link rel="stylesheet" href="css/fileinput.min.css">
  <!-- Awesome Bootstrap checkbox -->
  <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
  <!-- Admin Stye -->
  <link rel="stylesheet" href="css/style.css">

  <style>
  .errorWrap {
    padding: 10px;
    margin: 0 0 20px 0;
  background: #dd3d36;
  color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
.succWrap{
    padding: 10px;
    margin: 0 0 20px 0;
  background: #5cb85c;
  color:#fff;
    -webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
}
    </style>
<script type="text/javascript">


</script>
</head>

<body>
<?php

?>
  <?php include('includes/header.php');?>
  <div class="ts-main-content">
  <?php include('includes/leftbar.php');?>
    <div class="content-wrapper">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <h3 class="page-title">Gestionar Compras de artículos de clase <?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
           

  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE'];

 $tienda = getTiendaFromAsignatura($dbh,getAsignaturasFromCurso($dbh,$idCur)[0]['ID']);
    $aArticulos = getArticulosFromIdTienda($dbh,$tienda['ID']);

    $aComprasPentientes = getComprasFromArticulosYEstadoOrderBy($dbh,$aArticulos,'pendiente','FECHA_COMPRA');    
    $aComprasEntregadas = getComprasFromArticulosYEstadoOrderBy($dbh,$aArticulos,'entregado','FECHA_COMPRA');
      $aComprasDevueltas = getComprasFromArticulosYEstadoOrderBy($dbh,$aArticulos,'devuelto','FECHA_COMPRA');

  ?>
    

  </h3>




<!-- COMPRAS PENDIENTES -->

  
            <div class="row">

              <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading"><?php echo count($aComprasPentientes)?> compras pendientes</div>
                  <?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>
                  <div class="panel-body">


<?php
$contCompra=0;
foreach ($aComprasPentientes as $compraI) {
$contCompra++;
//var_export($compraI);
?>

<form method="post" class="form-horizontal" enctype="multipart/form-data" >
<input type="hidden" name="idc7" value="<?php echo $idCur;?>">
<input type="hidden" name="compraI" value="<?php echo $compraI['ID'];?>">
<div class="form-group">
  <div class="col-sm-4">
  </div>
  <div class="col-sm-4">
  </div>
</div>
<div class="form-group">
  <label class="col-sm-2 control-label"><?php echo 'Pedido nº '.$compraI['ID']; ?></label>
  <div class="col-sm-9">
  <?php 

$ali = getAlumnoFromId($dbh,$compraI['ID_ALUMNO']);
$artiAux= getArticuloFromId($dbh,$compraI['ID_ARTICULO']);
    echo 'COMPRADOR:<b>'.$ali['NOMBRE'].' '.$ali['APELLIDO1'].' '.$ali['APELLIDO2'];


    echo "</b><br/>ARTÍCULO:<b>".$artiAux['NOMBRE']
    ."</b><br/>FECHA:<b>".$compraI['FECHA_COMPRA']
    ."</b><br/>PRECIO:".$artiAux['PRECIO']."<br/>DESCRIPCIÓN:".$artiAux['DESCRIPCION']."<br/>COMENTARIO:";
    ?>
    
  <textarea name="texa" id="texa" class="md-textarea form-control" rows="3"><?php echo $compraI['COMENTARIO']?></textarea>
    
  </div>
</div>
<div class="form-group">
  <div class="col-sm-8 col-sm-offset-2">
    <button class="btn btn-success" name="bOk" type="submit">Entregar</button>
    <button class="btn btn-danger" name="bKo" type="submit">Devolver</button>
    <button class="btn btn-warning" name="bComentario" type="submit">Modificar Comentario</button>
    <a class="btn btn-info" href="https://mail.google.com/mail/u/0/?fs=1&to=<?php echo $ali['CORREO']?>&su=<?php
    echo 'INFO SalleZAmazon Pedido nº '.$compraI['ID'].': '.$artiAux['NOMBRE'];
    ?>
    &tf=cm" target="_blank">Enviar correo</a>
  </div>
</div>

</form>

<?php

}
?>

                  </div>





                  </div>
                </div>              


              </div>

<!-- COMPRAS ENTREGADAS -->

  
            <div class="row">

              <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading"><?php echo count($aComprasEntregadas)?> compras entregadas</div>
                  <?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>
                  <div style="background: #FFB833;overflow: hidden;" class="panel-body">


<?php
$contCompra=0;
foreach ($aComprasEntregadas as $compraI) {
$contCompra++;
//var_export($compraI);
?>

<form method="post" class="form-horizontal" enctype="multipart/form-data" >
<input type="hidden" name="idc7" value="<?php echo $idCur;?>">
<input type="hidden" name="compraI" value="<?php echo $compraI['ID'];?>">
<div class="form-group">
  <div class="col-sm-4">
  </div>
  <div class="col-sm-4">
  </div>
</div>



<div class="form-group">
  <label class="col-sm-2 control-label"><?php echo 'Pedido nº '.$compraI['ID']; ?></label>
  <div class="col-sm-9">
  <?php 

$ali = getAlumnoFromId($dbh,$compraI['ID_ALUMNO']);
$artiAux= getArticuloFromId($dbh,$compraI['ID_ARTICULO']);
    echo 'COMPRADOR:<b>'.$ali['NOMBRE'].' '.$ali['APELLIDO1'].' '.$ali['APELLIDO2'];


    echo "</b><br/>ARTÍCULO:<b>".$artiAux['NOMBRE']
    ."</b><br/>FECHA:<b>".$compraI['FECHA_COMPRA']
    ."</b><br/>PRECIO:".$artiAux['PRECIO']."<br/>DESCRIPCIÓN:".$artiAux['DESCRIPCION']."<br/>COMENTARIO:";
    ?>
    
  <textarea name="texa" id="texa" class="md-textarea form-control" rows="3"><?php echo $compraI['COMENTARIO']?></textarea>
      


    
  </div>
</div>
<div class="form-group">
  <div class="col-sm-8 col-sm-offset-2">
    <button class="btn btn-success" name="bPen" type="submit">Pasar Pendiente</button>
    <button class="btn btn-warning" name="bComentario" type="submit">Modificar Comentario</button>
    <a class="btn btn-info" href="https://mail.google.com/mail/u/0/?fs=1&to=<?php echo $ali['CORREO']?>&su=<?php
    echo 'INFO SalleZAmazon Pedido nº '.$compraI['ID'].': '.$artiAux['NOMBRE'];
    ?>
    &tf=cm" target="_blank">Enviar correo</a>
  </div>
</div>

</form>

<?php

}
?>

                  </div>





                  </div>
                </div>              


              </div>


<!-- COMPRAS DEVUELTAS -->

            <div class="row">

              <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading"><?php echo count($aComprasDevueltas)?> compras devueltas</div>
                  <?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>
                  <div style="background: #f99;overflow: hidden;" class="panel-body">


<?php
$contCompra=0;
foreach ($aComprasDevueltas as $compraI) {
$contCompra++;
//var_export($compraI);
?>

<form method="post" class="form-horizontal" enctype="multipart/form-data" >
<input type="hidden" name="idc7" value="<?php echo $idCur;?>">
<input type="hidden" name="compraI" value="<?php echo $compraI['ID'];?>">
<div class="form-group">
  <div class="col-sm-4">
  </div>
  <div class="col-sm-4">
  </div>
</div>



<div class="form-group">
  <label class="col-sm-2 control-label"><?php echo 'Pedido nº '.$compraI['ID']; ?></label>
  <div class="col-sm-9">
  <?php 

$ali = getAlumnoFromId($dbh,$compraI['ID_ALUMNO']);
$artiAux= getArticuloFromId($dbh,$compraI['ID_ARTICULO']);
    echo 'COMPRADOR:<b>'.$ali['NOMBRE'].' '.$ali['APELLIDO1'].' '.$ali['APELLIDO2'];


    echo "</b><br/>ARTÍCULO:<b>".$artiAux['NOMBRE']
    ."</b><br/>FECHA:<b>".$compraI['FECHA_COMPRA']
    ."</b><br/>PRECIO:".$artiAux['PRECIO']."<br/>DESCRIPCIÓN:".$artiAux['DESCRIPCION']."<br/>COMENTARIO:";
    ?>
    
  <textarea name="texa" id="texa" class="md-textarea form-control" rows="3"><?php echo $compraI['COMENTARIO']?></textarea>
      


    
  </div>
</div>
<div class="form-group">
  <div class="col-sm-8 col-sm-offset-2">
       <button class="btn btn-warning" name="bComentario" type="submit">Modificar Comentario</button>
     <a class="btn btn-info" href="https://mail.google.com/mail/u/0/?fs=1&to=<?php echo $ali['CORREO']?>&su=<?php
    echo 'INFO SalleZAmazon Pedido nº '.$artiAux['ID'].': '.$artiAux['NOMBRE'];
    ?>
    &tf=cm" target="_blank">Enviar correo</a>
  </div>
</div>

</form>

<?php

}
?>

                  </div>





                  </div>
                </div>              


              </div>


            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script>
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap-select.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap.min.js"></script>
  <script src="js/Chart.min.js"></script>
  <script src="js/fileinput.js"></script>
  <script src="js/chartData.js"></script>
  <script src="js/main.js"></script>
  <script type="text/javascript">
         $(document).ready(function () {          
          setTimeout(function() {
            $('.succWrap').slideUp("slow");
          }, 3000);
          });
  </script>
</body>
</html>
<?php } 


}
  catch (Exception $ex)
  {
      echo "Error:".$ex->getMessage();
  }  

  ?>