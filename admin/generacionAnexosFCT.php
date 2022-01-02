<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
//require_once("../UTILS/dbutils.php");
require_once ("../PHPWord-develop/bootstrap.php");
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
else
{
	//var_export($_POST);

	$folderPadre="../docsFCT/".$_POST['CLAVE_CICLO'];
        if (!file_exists($folderPadre)) {
            mkdir($folderPadre, 0777,true);
            //file_put_contents($folder.'/default.php', 'ondevasmaestro...');
        }
	$sTextoCarpetaAMostrar= $_POST['CLAVE_CICLO']."_".date("Ymd_His");
	$folder="../docsFCT/".$_POST['CLAVE_CICLO']."/".$sTextoCarpetaAMostrar;
 	mkdir($folder, 0777,true);
 	



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
	
	<title>Documentación FCT: <?php echo $_POST['NOMBRE_CICLO']?> </title>

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
.just-padding {
  padding: 15px;
}

.list-group.list-group-root {
  padding: 0;
  overflow: hidden;
}

.list-group.list-group-root .list-group {
  margin-bottom: 0;
}

.list-group.list-group-root .list-group-item {
  border-radius: 0;
  border-width: 1px 0 0 0;
}

.list-group.list-group-root > .list-group-item:first-child {
  border-top-width: 0;
}

.list-group.list-group-root > .list-group > .list-group-item {
  padding-left: 30px;
}

.list-group.list-group-root > .list-group > .list-group > .list-group-item {
  padding-left: 45px;
}
		</style>


</head>

<body>


	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

<h3>Documentación FCT generada para <?php echo $_POST['NOMBRE_CICLO']?> en la carpeta <?php echo $sTextoCarpetaAMostrar;?></h3>


<div class="just-padding">

<div class="list-group list-group-root well">
  
  <a target="_blank" href="<?php echo $folder?>" class="list-group-item active"><b><?php echo $sTextoCarpetaAMostrar?></b></a>
  <div class="list-group">
    <?php
  	for ($i=1; $i <= $_POST['totalAlumnos']; $i++) 
		{ 
			echo '<a class="list-group-item ">'.'&emsp;&emsp;'.generarAnexo21($folder,$i).'</a>';
		}

  ?> 
  </div> 
</div> 
</div>

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


<?php


function generarAnexo21($folder, $num)
{

$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../PHPWord-develop/plantillas/anexo21.docx');

// alumno
$templateProcessor->setValue('NOMBRE_ALUMNO', $_POST['NOMBRE_ALUMNO'.$num]);
$templateProcessor->setValue('DNI_ALUMNO', $_POST['DNI_ALUMNO'.$num]);

// tutor colegio
$templateProcessor->setValue('NOMBRE_TUTOR_COLEGIO', $_POST['NOMBRE_TUTOR_COLEGIO']);
$templateProcessor->setValue('NIF_TUTOR_COLEGIO', $_POST['NIF_TUTOR_COLEGIO']);

//CICLOS de FCT

$templateProcessor->setValue('CLAVE_CICLO', $_POST['CLAVE_CICLO']);
$templateProcessor->setValue('NOMBRE_CICLO', $_POST['NOMBRE_CICLO']);

//FCT Periodo
$templateProcessor->setValue('CURSO_ACADEMICO', $_POST['CURSO_ACADEMICO']);
$templateProcessor->setValue('FECHA_INICIO', $_POST['FECHA_INICIO']);
$templateProcessor->setValue('FECHA_TERMINACION', $_POST['FECHA_TERMINACION']);
$templateProcessor->setValue('HORAS_DIA', $_POST['HORAS_DIA']);
$templateProcessor->setValue('TOTAL_HORAS', $_POST['TOTAL_HORAS']);
$templateProcessor->setValue('FECHA_FIRMA_DOC', $_POST['FECHA_FIRMA_DOC']);

//FCT Prácticas


$templateProcessor->setValue('NOMBRE_TUTOR_EMPRESA', $_POST['NOMBRE_TUTOR_EMPRESA'.$num]);
$templateProcessor->setValue('CONTACTO_TUTOR_EMPRESA', $_POST['CONTACTO_TUTOR_EMPRESA'.$num]);



//Empresa
$templateProcessor->setValue('N_CONVENIO', $_POST['N_CONVENIO'.$num]);
$templateProcessor->setValue('NOMBRE_EMPRESA', $_POST['NOMBRE_EMPRESA'.$num]);
$templateProcessor->setValue('FECHA_CONVENIO', $_POST['FECHA_CONVENIO'.$num]);
$templateProcessor->setValue('LOCALIDAD_EMPRESA', $_POST['LOCALIDAD_EMPRESA'.$num]);
$templateProcessor->setValue('DIRECCION_EMPRESA', $_POST['DIRECCION_EMPRESA'.$num]);
$templateProcessor->setValue('NOMBRE_REPRESENTANTE_EMPRESA', $_POST['NOMBRE_REPRESENTANTE_EMPRESA'.$num]);

$templateProcessor->saveAs($folder.'/anexo21_'.$_POST['NOMBRE_ALUMNO'.$num].'.docx');
return 'anexo21_'.$_POST['NOMBRE_ALUMNO'.$num].'.docx';
}


?>