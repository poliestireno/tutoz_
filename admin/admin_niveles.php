<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
	{	
header('location:index.php');
}
if (isset($_POST['actu_items']))
{
  foreach($_POST as $key => $value) {
    if (strpos($key, 'inniveles') === 0) {
      $posPipe = strrpos($key, "|");
      $categoria = substr($key,9,$posPipe-9);
      $numNivel=substr($key,$posPipe+1,strlen($key));
      actualizarNivel($dbh,$categoria,$numNivel,$value);
    }
  }
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
	
	<title>Niveles</title>

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


</head>

<body>
<?php
	
?>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">


<form method="post" class="form-horizontal" enctype="multipart/form-data">

<?php
        $sListaGenericos="";
        $comma = "";
        $listaCursos= getAsignaturasConCurso($dbh);
        foreach ($listaCursos as $curso)
        {
            $pos = strrpos($curso, "--");
            $idAs=substr($curso,$pos+2,strlen($curso));
            $nombre = substr($curso,0,$pos);
            $posAs= strrpos($nombre,"*");
            $clase = substr($nombre,$posAs+1,strlen($nombre));
            //echo "ITEM:'".$curso."+++".$nombre."<br/>";
            $filaAsig = getAsignaturaFromAsignaturaID($dbh,$idAs);
            
            if ($filaAsig["CATEGORIA_NIVEL"]=="GENERICO")
            {
                $sListaGenericos = $sListaGenericos . $comma . $nombre;
                $comma=",";
            }
            else
            {
              echo '<h3>Niveles Clase: '.$nombre.'</h3>';
?>
<!--Table-->
<table class="table table-striped w-auto table-bordered">
   <!--Table head-->
   <thead>
      <tr>
         <th>Nivel</th>
         <th>Nombre</th>
         <th>Estrellas para desbloquear</th>
         <th>Recompensas</th>
      </tr>
   </thead>
   <!--Table head-->

   <!--Table body-->
   <tbody>
<?php
$nivelesCat = getNivelesFromCategoria($dbh,$filaAsig["CATEGORIA_NIVEL"]);
foreach ($nivelesCat as $niveli) {
?>
      <tr class="table-info">
         <td><?php echo $niveli['NUMERO']?></td>
         <td><?php echo $niveli['NOMBRE']?></td>
         <td><input type="text" class="form-control" id="filtroc" 
          name = "inniveles<?php echo $filaAsig["CATEGORIA_NIVEL"].'|'.$niveli['NUMERO']?>" value="<?php echo $niveli['ESTRELLAS_DESBLOQUEO']?>"></td>
         <td><?php echo $niveli['RECOMPENSAS']?></td>
      </tr>
<?php
}
?>
   </tbody>
   <!--Table body-->
</table>

<?php              
            }
        }

if ($sListaGenericos!="")
{

              echo '<h3>Niveles Genéricos Clases: '.$sListaGenericos.'</h3>';
?>
<!--Table-->
<table class="table table-striped w-auto table-bordered">
   <!--Table head-->
   <thead>
      <tr>
         <th>Nivel</th>
         <th>Nombre</th>
         <th>Estrellas para desbloquear</th>
         <th>Recompensas</th>
      </tr>
   </thead>
   <!--Table head-->

   <!--Table body-->
   <tbody>
<?php
$nivelesCat = getNivelesFromCategoria($dbh,"GENERICO");
foreach ($nivelesCat as $niveli) {
?>
      <tr class="table-info">
         <td><?php echo $niveli['NUMERO']?></td>
         <td><?php echo $niveli['NOMBRE']?></td>
         <td><input type="text" class="form-control" id="filtroc" name = "inniveles<?php echo 'GENERICO|'.$niveli['NUMERO']?>" value="<?php echo $niveli['ESTRELLAS_DESBLOQUEO']?>"></td>
         <td><?php echo $niveli['RECOMPENSAS']?></td>
      </tr>
<?php
}
?>
   </tbody>
   <!--Table body-->
</table>

<?php              
            }



        ?>

		</div>

	</div>

<div class="form-group">
  <div class="col-sm-8 col-sm-offset-2">
    <button class="btn btn-primary" name="actu_items" type="submit">Guardar cambios</button>
  </div>
</div>

	<!-- Loading Scripts -->
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
	
</form>

</body>
</html>
