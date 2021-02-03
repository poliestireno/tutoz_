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
$idCur="";

	var_export($_POST);	
		if (isset($_GET['idc']))
		{
		  $idCur=$_GET['idc'];
		}
		else
		{
		  $idCur=$_POST['idc4'];
		}

//	$msg="todo ok";
if (isset($_POST['accionI']))
{
if ($_POST['accionI']=='desactivar')
{

	modificarDesactivarFastests($dbh,getAsignaturasFromCurso($dbh,$idCur)[0]['ID']);
}
else if ($_POST['accionI']=='activar')
{
	modificarDesactivarFastests($dbh,getAsignaturasFromCurso($dbh,$idCur)[0]['ID']);
	modificarActivarFastest($dbh,$_POST['sFTId']);
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
	
	<title>Resultados Test Rápidos</title>

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

<body onload="init()">
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h3 class="page-title">Resultados Test Rápidos de clase <?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE']?></h3>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Test Rápidos</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">

<form action="resultados_fastests.php" id="form3" method="post">
  <input type="hidden" name="idc4" id="idc4" value="<?php echo $_POST['idc4']?>"/>
<input type="hidden" name="accionI" id="accionI"/>
<input type="hidden" name="flash" id="flash"/>

	<div class="form-group">
<label class="col-sm-1 control-label">Test Rápidos</label>
                            <div class="col-sm-5">
  <select class="form-control col-md-2" ID="sFTId" name="sFTId">
  	<option></option>
<?php
$seleccionadoActivo = false;
	$aFTs = getFastTestsFromAsignatura($dbh,getAsignaturasFromCurso($dbh,$idCur)[0]['ID']);
    foreach ($aFTs as $fastestI) 
    {
    	if (isset($_POST['sFTId']))
    	{
 			if (($fastestI['ID']==$_POST['sFTId']) &&($fastestI['ACTIVO']!=0))
    		{
    			$seleccionadoActivo = true;
    		}   		
    	}

echo "<option ".((isset($_POST['sFTId'])&&($fastestI['ID']==$_POST['sFTId']))?" selected='selected' ":"")." value='".$fastestI['ID']."'>".$fastestI['NOMBRE']." (".$fastestI['FECHA'].")".(($fastestI['ACTIVO']==0)?"":"-ACTIVO")."</option>";
    }

?>
  </select>
                            </div>                          

	</div>
	<div class="col-sm-8 col-sm-offset-2"></div>
	<div class="col-sm-8 col-sm-offset-2"></div>
    <div class="form-group">
  <div class="col-sm-5 col-sm-offset-1">

<?php
echo '<div id="aa_0" class="btn-group btn-group-justified" ><a id="bb_0"  onclick="managebutton(\'ver\')" class="btn btn-info">Ver Resultados</a><a id="bb_0" onclick="managebutton(\'desactivar\')"  class="btn btn-success">Desactivar Tests</a><a id="bb_0" onclick="managebutton(\'activar\')"  class="btn btn-primary">Activar Test</a></div>';
?>
  </div>
</div>
	
<br/><br/><br/><br/><br/>
<div class="form-group">
<?php
if (isset($_POST['accionI']))
{


if ($_POST['accionI']=='ver')
{


//modificarAtributoAuxCromo($db,$correo,$atributoAux)

$tiposBotones = array( "btn btn-primary", "btn btn-success", "btn btn-warning", "btn btn-info");
$alumnoDB = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);

$fastestElegido = getFastestFromId($dbh,$_POST['sFTId']);
$tamDesc = strlen($fastestElegido['DESCRIPCION']);

echo '<div class="form-group"><label class="col-sm-8 control-label" style="text-align:left;font-family:Courier; font-size:'.(60-$tamDesc).'px;" > '.$fastestElegido['DESCRIPCION'].'</label></div>';
	
	echo '<label class="col-sm-8 control-label">'.(($seleccionadoActivo)?"<br/>[SE MOSTRARÁN LOS RESULTADOS CUANDO SE DESACTIVE EL TEST] ":"").'</label>';

}

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

<?php
if((!$seleccionadoActivo)&&(isset($_POST['sFTId'])))
{
?>

											<table ID="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
										<th>#</th>
												<th>Nº Deluxe</th>
                                                <th>Imagen</th>
                                                <th>Nombre</th>
                                                <th>Apellido1</th>
                                                <th>Apellido2</th>
										</tr>
									</thead>
									
									<tbody>




<?php 


$aAlumnosFastest = getAlumnosfromFastestId($dbh,$_POST['sFTId']);

$cnt=1;

foreach($aAlumnosFastest as $alumnoFastI)
{				
	$alumnoII = getAlumnoFromId($dbh,$alumnoFastI['ID_ALUMNO']);

	?>	
										<tr>

											<td><?php echo htmlentities($cnt);?></td>
											<td><?php echo htmlentities($alumnoFastI['RESULTADO']);?></td>
											<td><img src="../images/<?php 
		$dbImage = htmlentities($alumnoII['image']);		
		if (($dbImage!="")&&(file_exists("../images/".$dbImage)))
		{   
			echo $dbImage;
		}
		else
		{
			echo "anonimous_profile.jpg";
		}

											?>" style="width:50px; border-radius:50%;"/></td>
                                            
                                            <td><?php echo htmlentities($alumnoII['NOMBRE']);?></td>
                                             <td><?php echo htmlentities($alumnoII['APELLIDO1']);?></td>
                                            <td><?php echo htmlentities($alumnoII['APELLIDO2']);?> 
                                            </td>
										</tr>
										<?php 

if ((isset($_POST['flash']))&&($_POST['flash']==1))
{
$atributoAux=getValorAtributoDesdeTest($cnt,count($aAlumnosFastest),$_POST['minAtri'],$_POST['maxAtri']);
modificarAtributoAuxCromo($dbh,$alumnoII['CORREO'],$atributoAux);
}
$cnt=$cnt+1; 
								}?>


										
									</tbody>
								</table>






<?php

echo '<div id="aab_0" class=" col-sm-3 form-inline" ><a id="bbb_0"  onclick="managebutton(\'modAtriAux\')" class="btn btn-danger">Flashear atributos con el orden actual de la tabla</a>
                            </div><label class=" form-inline" >Mínimo valor atributoAux:&nbsp;   </label><div class="btn-group form-inline "><input type="text" maxlength = "5" name="minAtri" class="form-control"  value="0"  required>
                            </div><label class=" form-inline" >&nbsp;Máximo valor atributoAux:&nbsp;  </label><div class="btn-group btn-group-inline"><input type="text" maxlength = "5" name="maxAtri" class="form-control"  value="2" required>
                            </div>';

 }?>




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
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>						
	<script type="text/javascript">
		$('#zctb').DataTable( {
    scrollY: 300,
    paging: false,
    scrollCollapse: true
} );
		 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 5000);
					});

	function managebutton(accion)
{
	var valorS = document.getElementById("sFTId").options[document.getElementById("sFTId").selectedIndex].text;
	if ((valorS=="")&&(accion!="desactivar"))
	{
		swal.fire("Selecciona algún test rápido!", "please...", "warning"); 
	}
	else
	{
		if (accion=='modAtriAux')
		{
			document.getElementById('flash').value=1;
		}
		else
		{
			document.getElementById('flash').value=0;
		}
		
   		document.getElementById('accionI').value=accion;
   		document.getElementById("form3").submit(); 
	}	
}

function init()
{
	<?php
if ((isset($_POST['flash']))&&($_POST['flash']==1))
{
	echo 'swal.fire("Flasheados atributos correctamente", "según la el orden de la tabla", "success");';	
}	
	?>
}
	</script>
	</form>	
</body>
</html>
<?php } ?>