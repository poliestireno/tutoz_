<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";
//var_export($_POST);
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



if (isset($_GET['idCiclo']))
{
  $idCiclo=$_GET['idCiclo'];
}
if (isset($_POST['idCiclo']))
{
  $idCiclo=$_POST['idCiclo'];
}
if (isset($_GET['idPeriodo']))
{
  $idPeriodo=$_GET['idPeriodo'];
}
if (isset($_POST['idPeriodo']))
{
  $idPeriodo=$_POST['idPeriodo'];
}


$aCiclos = ejecutarQuery($dbh,"SELECT * FROM FCT_CICLOS WHERE ID =".$idCiclo);

$NOMBRE_CICLO = $aCiclos[0]['NOMBRE'];
$CLAVE_CICLO = $aCiclos[0]['CLAVE_CICLO'];
$FAMILIA_PROFESIONAL = $aCiclos[0]['FAMILIA_PROFESIONAL'];
$aTutoresCole = ejecutarQuery($dbh,"SELECT * FROM FCT_TUTORES_PROFES WHERE ID_FCT_CICLO =".$idCiclo);

$NOMBRE_TUTOR_COLEGIO = $aTutoresCole[0]['NOMBRE']." ".$aTutoresCole[0]['APELLIDO1']." ".$aTutoresCole[0]['APELLIDO2'];
$NIF_TUTOR_COLEGIO = $aTutoresCole[0]['DNI'];

$aPeriodos = ejecutarQuery($dbh,"SELECT * FROM FCT_PERIODOS WHERE ID =".$idPeriodo);
$periodo = $aPeriodos[0];

$CURSO_ACADEMICO = $periodo['CURSO_ACADEMICO'];
$FECHA_FIRMA_DOC = $periodo['FECHA_FIRMA_DOC'];
$FECHA_INICIO = $periodo['FECHA_INICIO'];
$FECHA_TERMINACION = $periodo['FECHA_TERMINACION'];
$HORAS_DIA = $periodo['HORAS_DIA'];
$TOTAL_HORAS = $periodo['TOTAL_HORAS'];


$aPracticas = ejecutarQuery($dbh,"SELECT * FROM FCT_PRACTICAS WHERE ID_FCT_PERIODO =".$idPeriodo." ORDER BY ID_FCT_EMPRESA"); 


//var_export($aPracticas);

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
	
	<title>FCT: <?php echo $NOMBRE_CICLO?></title>

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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/fileinput.js"></script>
    <script src="js/chartData.js"></script>
    <script src="js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
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


	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

<h1>Documentación para <?php echo $NOMBRE_CICLO?></h1>
<h5>(Nota: Se pueden modificar los siguientes datos y se tendrán en cuenta al generar estos documentos, pero las modificaciones no persistirán en base de datos)</h5>

<form target="_blank" method="post" action="generacionAnexosFCT.php" id="form1" class="form-horizontal" enctype="multipart/form-data" >
<input type="hidden" name="idCiclo" id="idCiclo" value="<?php echo $idCiclo?>"/>
<input type="hidden" name="idPeriodo" id="idPeriodo" value="<?php echo $idPeriodo?>"/>
<input type="hidden" name="totalAlumnos" id="totalAlumnos" value=""/>
<div class="panel panel-default">
	<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>								
	<div class="panel-heading">GENERAL</div>

<div class="container-fluid">
	  <div class="form-group">
  <label class="col-sm-1 control-label"></label>
  <div class="col-sm-5">
  </div>
  </div>


  <div class="form-group col-sm-12">
  
  <label class="col-sm-2 control-label">NOMBRE_CICLO<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="NOMBRE_CICLO" class="form-control" value="<?php echo $NOMBRE_CICLO?>" required/>
  </div>
  <label class="col-sm-2 control-label">CLAVE_CICLO<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="CLAVE_CICLO" class="form-control" value="<?php echo $CLAVE_CICLO?>" required>
  </div>

  </div>

  <div class="form-group col-sm-12">
  <label class="col-sm-2 control-label">CURSO_ACADEMICO<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="CURSO_ACADEMICO" class="form-control" value="<?php echo $CURSO_ACADEMICO?>" required>
  </div>

  <label class="col-sm-2 control-label">FAMILIA_PROFESIONAL<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="FAMILIA_PROFESIONAL" class="form-control" value="<?php echo $FAMILIA_PROFESIONAL?>" required/>
  </div>
  </div>

  <div class="form-group col-sm-12">
  <label class="col-sm-2 control-label">FECHA_INICIO<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="FECHA_INICIO" class="form-control" value="<?php echo $FECHA_INICIO?>" required>
  </div>

  <label class="col-sm-2 control-label">FECHA_TERMINACION<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="FECHA_TERMINACION" class="form-control" value="<?php echo $FECHA_TERMINACION?>" required/>
  </div>
  </div>

  <div class="form-group col-sm-12">
  <label class="col-sm-2 control-label">HORAS_DIA<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="HORAS_DIA" class="form-control" value="<?php echo $HORAS_DIA?>" required>
  </div>

  <label class="col-sm-2 control-label">TOTAL_HORAS<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="TOTAL_HORAS" class="form-control" value="<?php echo $TOTAL_HORAS?>" required/>
  </div>
  </div>

  <div class="form-group col-sm-12">
  <label class="col-sm-2 control-label">NOMBRE_TUTOR_COLEGIO<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="NOMBRE_TUTOR_COLEGIO" class="form-control" value="<?php echo $NOMBRE_TUTOR_COLEGIO?>" required>
  </div>

  <label class="col-sm-2 control-label">NIF_TUTOR_COLEGIO<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="NIF_TUTOR_COLEGIO" class="form-control" value="<?php echo $NIF_TUTOR_COLEGIO?>" required/>
  </div>
  </div>
  
<div class="form-group col-sm-12">
 <label class="col-sm-2 control-label">FECHA_FIRMA_DOC<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="FECHA_FIRMA_DOC" class="form-control" value="<?php echo $FECHA_FIRMA_DOC?>" required/>
  </div>


  </div>

	</div>
</div>

<?php
$contAlumnos=1;
$totalAlumnos = Count($aPracticas);
foreach ($aPracticas as $practica) 
{

  $aAlumnosAux = ejecutarQuery($dbh,"SELECT * FROM FCT_ALUMNOS WHERE ID =".$practica['ID_FCT_ALUMNO']);
  $alumno = $aAlumnosAux[0];
  $NOMBRE_ALUMNO = $alumno['NOMBRE'];
  $APELLIDO1_ALUMNO = $alumno['APELLIDO1'];
  $APELLIDO2_ALUMNO = $alumno['APELLIDO2'];
  $DNI_ALUMNO = $alumno['DNI'];
  $aEmpresasAux = ejecutarQuery($dbh,"SELECT * FROM FCT_EMPRESAS WHERE ID =".$practica['ID_FCT_EMPRESA']);
  $empresa = $aEmpresasAux[0];
  $NOMBRE_EMPRESA = $empresa['NOMBRE'];
  $NOMBRE_REPRESENTANTE_EMPRESA= $empresa['NOMBRE_REPRESENTANTE_EMPRESA'];
  $N_CONVENIO= $empresa['N_CONVENIO'];
  $FECHA_CONVENIO= $empresa['FECHA_CONVENIO'];
  $DIRECCION_EMPRESA= $empresa['DIRECCION_EMPRESA'];
  $LOCALIDAD_EMPRESA= $empresa['LOCALIDAD_EMPRESA'];
  $NOMBRE_TUTOR_EMPRESA= $practica['NOMBRE_TUTOR_EMPRESA'];
  $CONTACTO_TUTOR_EMPRESA= $practica['CONTACTO_TUTOR_EMPRESA'];

?>
<div class="panel panel-default">							
	<div class="panel-heading"><?php echo $NOMBRE_ALUMNO." ".$APELLIDO1_ALUMNO." ".$APELLIDO2_ALUMNO." (".$contAlumnos."/".$totalAlumnos.")"?></div>

<div class="container-fluid">
	  <div class="form-group">
  <label class="col-sm-1 control-label"></label>
  <div class="col-sm-5">
  </div>
  </div>

  <div class="form-group col-sm-12">
  <label class="col-sm-2 control-label">NOMBRE_ALUMNO<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="NOMBRE_ALUMNO_COMPLETO<?php echo $contAlumnos?>" class="form-control" value="<?php echo $NOMBRE_ALUMNO." ".$APELLIDO1_ALUMNO." ".$APELLIDO2_ALUMNO?>" required>
  </div>
<input type="hidden" name="NOMBRE_ALUMNO<?php echo $contAlumnos?>" id="NOMBRE_ALUMNO<?php echo $contAlumnos?>" value="<?php echo $NOMBRE_ALUMNO?>"/>
<input type="hidden" name="APELLIDO1_ALUMNO<?php echo $contAlumnos?>" id="APELLIDO1_ALUMNO<?php echo $contAlumnos?>" value="<?php echo $APELLIDO1_ALUMNO?>"/>
<input type="hidden" name="APELLIDO2_ALUMNO<?php echo $contAlumnos?>" id="APELLIDO2_ALUMNO<?php echo $contAlumnos?>" value="<?php echo $APELLIDO2_ALUMNO?>"/>

  <label class="col-sm-2 control-label">DNI_ALUMNO<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="DNI_ALUMNO<?php echo $contAlumnos?>" class="form-control" value="<?php echo $DNI_ALUMNO?>" required/>
  </div>
  </div>

  <div class="form-group col-sm-12">
  <label class="col-sm-2 control-label">NOMBRE_EMPRESA<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="NOMBRE_EMPRESA<?php echo $contAlumnos?>" class="form-control" value="<?php echo $NOMBRE_EMPRESA?>" required>
  </div>

  <label class="col-sm-3 control-label">NOMBRE_REPRESENTANTE_EMPRESA<span style="color:red">*</span></label>
  <div class="col-sm-3">
  <input type="text" name="NOMBRE_REPRESENTANTE_EMPRESA<?php echo $contAlumnos?>" class="form-control" value="<?php echo $NOMBRE_REPRESENTANTE_EMPRESA?>" required/>
  </div>
  </div>

  <div class="form-group col-sm-12">
  <label class="col-sm-2 control-label">N_CONVENIO<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="N_CONVENIO<?php echo $contAlumnos?>" class="form-control" value="<?php echo $N_CONVENIO?>" required>
  </div>

  <label class="col-sm-2 control-label">FECHA_CONVENIO<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="FECHA_CONVENIO<?php echo $contAlumnos?>" class="form-control" value="<?php echo $FECHA_CONVENIO?>" required/>
  </div>
  </div>

  <div class="form-group col-sm-12">
  <label class="col-sm-2 control-label">DIRECCION_EMPRESA<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="DIRECCION_EMPRESA<?php echo $contAlumnos?>" class="form-control" value="<?php echo $DIRECCION_EMPRESA?>" required>
  </div>

  <label class="col-sm-2 control-label">LOCALIDAD_EMPRESA<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="LOCALIDAD_EMPRESA<?php echo $contAlumnos?>" class="form-control" value="<?php echo $LOCALIDAD_EMPRESA?>" required/>
  </div>
  </div>

  <div class="form-group col-sm-12">
  <label class="col-sm-2 control-label">NOMBRE_TUTOR_EMPRESA<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="NOMBRE_TUTOR_EMPRESA<?php echo $contAlumnos?>" class="form-control" value="<?php echo $NOMBRE_TUTOR_EMPRESA?>" required>
  </div>

  <label class="col-sm-2 control-label">CONTACTO_TUTOR_EMPRESA<span style="color:red">*</span></label>
  <div class="col-sm-4">
  <input type="text" name="CONTACTO_TUTOR_EMPRESA<?php echo $contAlumnos?>" class="form-control" value="<?php echo $CONTACTO_TUTOR_EMPRESA?>" required/>
  </div>
  </div>

	</div>
</div>

<?php
$contAlumnos++;
} 
?>
<div class="panel panel-default">             
  <div class="panel-heading">Generación de documentos</div>

<div class="container-fluid">
    <div class="form-group">
  <label class="col-sm-1 control-label"></label>
  <div class="col-sm-5">
  </div>
  </div>

  <div class="form-group col-sm-12">
  <div class="col-sm-6">
    <a data-toggle="tooltip" onclick="manageGeneracion();" class="btn btn-warning btn-outline " title="Generar toda la documentación" >Generar Documentación</a>
  </div>
  </div>

  </div>
  



</div>

</form>			
		</div>
	</div>
</div>

	<!-- Loading Scripts -->
	<script type="text/javascript">
				 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 5000);
					});

	



  function manageGeneracion()
  {

    document.getElementById('totalAlumnos').value = <?php echo $totalAlumnos?>;  
    document.getElementById('form1').submit();  
  }
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