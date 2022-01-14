<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
//require_once("../UTILS/dbutils.php");
require_once ("../PHPWord-develop/bootstrap.php");
require_once ("../UTILS/driveutils.php");
$msg="";
//var_export($_POST);
try
  {
$sql = "SELECT username from admin where username='ADMIN_FCT'";
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

	<nav class="ts-sidebar">
			<ul class="ts-sidebar-menu">
		
						<li class="ts-label">FCT</li>
<li><a href="resumenFCT.php"><i class="fa fa-dashboard"></i>RESUMEN Y TABLAS</a></li>
<?php

	$aCiclos = array();
    $stmt = $dbh->query("SELECT * FROM FCT_CICLOS");
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $aCiclos [] = $fila;
    }
	foreach ($aCiclos as $ciclo) 
	{
		echo '<li><a href="controlFCT.php?idCiclo='.$ciclo['ID'].'"><i class="fa fa-users"></i>'.$ciclo['INFO'].'</a></li>';
	}
?>
			</ul>
			<p class="text-center" style="color:#ffffff; margin-top: 100px;">© Gilbert</p>
		</nav>




		<div class="content-wrapper">
			<div class="container-fluid">

<?php
$idCarpetaRaiz = '1jU6GD0c_H33gM_TFjgdmSUHRRE_iGlbV';
$idFolder = crearCarpetaDrive($sTextoCarpetaAMostrar,$sTextoCarpetaAMostrar,$idCarpetaRaiz);
?>


<h2>Documentación generada para <?php echo $_POST['NOMBRE_CICLO']?></h2>
<h2>Periodo: <?php echo $_POST['nombrePeriodo']?></h2>
<h3>Ubicada en dos sitios:</h3>
<h3><a target="_blank" href="<?php echo $folder?>" class="list-group-item active"><b><?php echo $sTextoCarpetaAMostrar?> (carpeta servidor)</b></a></h3>
<h3><a target="_blank" href="https://drive.google.com/drive/folders/<?php echo $idFolder?>" class="list-group-item active"><b><?php echo $sTextoCarpetaAMostrar?> (Google drive)</b></a></h3>
<br/>
<h3>Estructura de la lista de archivos generada:</h3>
<div class="just-padding">

<div class="list-group list-group-root well">
  
  <a target="_blank" href="<?php echo $folder?>" class="list-group-item active"><b><?php echo $sTextoCarpetaAMostrar?></b></a>
  <div class="list-group">
  
   <?php

    // INICIO ANEXO 21
    if (isset($_POST['anexos21']))
    {
    	bloqueAnexo21($sTextoCarpetaAMostrar,$folder,$idFolder);
    }
    

		// FIN ANEXO 21

		// INICIO ANEXO 22
		if (isset($_POST['anexo22']))
    {
			echo '<a class="list-group-item ">'.'&emsp;&emsp;'.generarAnexo22($folder, $sTextoCarpetaAMostrar,$idFolder).'</a>';
		}
		// FIN ANEXO 22
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


function generarAnexo21($folder, $sTextoCarpetaAMostrar,$num,$aNombres,$aNombresConApellido1,$aDNIs,$idFolder)
{

$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../PHPWord-develop/plantillas/anexo21.docx');

// alumno
$templateProcessor->setValue('NOMBRE_ALUMNO', $_POST['NOMBRE_ALUMNO'.$num]." ".$_POST['APELLIDO1_ALUMNO'.$num]." ".$_POST['APELLIDO2_ALUMNO'.$num]);
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
$templateProcessor->setValue('HORA_INICIO', $_POST['HORA_INICIO'.$num]);
$templateProcessor->setValue('HORA_TERMINACION', $_POST['HORA_TERMINACION'.$num]);
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




//Create table
$document_with_table = new \PhpOffice\PhpWord\PhpWord();


$styleCell =
[
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'000000',
    'borderRightSize' => 6,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'000000',
    'borderLeftSize' => 6,
    'align' => 'center', 
     
];
$section = $document_with_table->addSection();
$table = $section->addTable($styleCell);

// cabecera de la tabla
$table->addRow();
$table->addCell(1750,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("D.N.I.",array('name'=>'Arial Narrow', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(3000,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("APELLIDOS Y NOMBRE",array('name'=>'Arial Narrow', 'size'=>9,'bold' => true,'align' => 'center'));

// filas de la tabla
$nombresNombreFichero= "";
$barraBaja = "";
for ($i=0; $i < Count($aNombres); $i++) 
{ 
		$table->addRow();
		$table->addCell(1750,$styleCell)->addText($aDNIs[$i],array('name'=>'Arial Narrow', 'size'=>9));
		$table->addCell(3000,$styleCell)->addText($aNombres[$i],array('name'=>'Arial Narrow', 'size'=>9));
		if (Count($aNombres)>1)
		{
				$nombresNombreFichero.= $barraBaja.$aNombresConApellido1[$i];
		}
		else
		{
				$nombresNombreFichero.= $barraBaja.$aNombres[$i];
		}
		
		$barraBaja="_";
}


// Create writer to convert document to xml
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($document_with_table, 'Word2007');

// Get all document xml code
$fullxml = $objWriter->getWriterPart('Document')->write();

// Get only table xml code
$tablexml = preg_replace('/^[\s\S]*(<w:tbl\b.*<\/w:tbl>).*/', '$1', $fullxml);


	
$templateProcessor->setValue('table1', $tablexml);







$templateProcessor->saveAs($folder.'/anexo21_'.$nombresNombreFichero.'.docx');

// subir a drive
subirDocumentoWordDrive	($folder.'/anexo21_'.$nombresNombreFichero.'.docx','anexo21_'.$nombresNombreFichero.'.docx',"anexo21",$idFolder);

return 'anexo21_'.$nombresNombreFichero.'.docx';
}


function generarAnexo22($folder, $sTextoCarpetaAMostrar,$idFolder)
{

$templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('../PHPWord-develop/plantillas/anexo22.docx');

$templateProcessor->setValue('CLAVE_CICLO', $_POST['CLAVE_CICLO']);
$templateProcessor->setValue('NOMBRE_CICLO', $_POST['NOMBRE_CICLO']);
$templateProcessor->setValue('FECHA_FIRMA_DOC', $_POST['FECHA_FIRMA_DOC']);
$templateProcessor->setValue('FAMILIA_PROFESIONAL', $_POST['FAMILIA_PROFESIONAL']);

//Create table
$document_with_table = new \PhpOffice\PhpWord\PhpWord();
$styleCell =
[
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'000000',
    'borderRightSize' => 6,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'000000',
    'borderLeftSize' => 6,
     
];
$section = $document_with_table->addSection();
$table = $section->addTable($styleCell);

// cabecera de la tabla
$table->addRow();
$table->addCell(1500,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Tutor Empresa",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(1750,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Tutor Centro",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(1500,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Total Horas",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(1750,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Horas/día",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(1300,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Final",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(1300,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Inicio",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(1600,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Localidad",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(1200,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Nº convenio",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(1500,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("NIF/NIE",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(1300,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Nombre",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(2700,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Apellidos",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));
$table->addCell(650,array_merge($styleCell,array('bgColor'=>'dbdbdb')))->addText("Nº.",array('name'=>'Arial', 'size'=>9,'bold' => true,'align' => 'center'));

// filas de la tabla
for ($i=1; $i <= $_POST['totalAlumnos']; $i++) 
{ 
	$table->addRow();
	$table->addCell(1500,$styleCell)->addText($_POST['NOMBRE_TUTOR_EMPRESA'.$i],array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(1750,$styleCell)->addText($_POST['NOMBRE_TUTOR_COLEGIO'],array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(1500,$styleCell)->addText($_POST['TOTAL_HORAS'],array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(1750,$styleCell)->addText($_POST['HORAS_DIA'],array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(1300,$styleCell)->addText($_POST['FECHA_TERMINACION'],array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(1300,$styleCell)->addText($_POST['FECHA_INICIO'],array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(1600,$styleCell)->addText($_POST['LOCALIDAD_EMPRESA'.$i],array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(1200,$styleCell)->addText($_POST['N_CONVENIO'.$i]."CM",array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(1500,$styleCell)->addText($_POST['DNI_ALUMNO'.$i],array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(1300,$styleCell)->addText($_POST['NOMBRE_ALUMNO'.$i],array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(2700,$styleCell)->addText($_POST['APELLIDO1_ALUMNO'.$i]." ".$_POST['APELLIDO2_ALUMNO'.$i],array('name'=>'Arial Narrow', 'size'=>8));
	$table->addCell(650,$styleCell)->addText($i,array('name'=>'Arial Narrow', 'size'=>8));
}


// Create writer to convert document to xml
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($document_with_table, 'Word2007');

// Get all document xml code
$fullxml = $objWriter->getWriterPart('Document')->write();

// Get only table xml code
$tablexml = preg_replace('/^[\s\S]*(<w:tbl\b.*<\/w:tbl>).*/', '$1', $fullxml);


$templateProcessor->setValue('table1', $tablexml);

$templateProcessor->saveAs($folder.'/anexo22_'.$_POST['NOMBRE_CICLO'].'.docx');

// subir a drive
subirDocumentoWordDrive	($folder.'/anexo22_'.$_POST['NOMBRE_CICLO'].'.docx','anexo22_'.$_POST['NOMBRE_CICLO'].'.docx',"anexo22",$idFolder);

return 'anexo22_'.$_POST['NOMBRE_CICLO'].'.docx';
}


// BLOQUES DE ANEXOS



function bloqueAnexo21($sTextoCarpetaAMostrar,$folder,$idFolder)
{
	  $empresaAnterior=$_POST['NOMBRE_EMPRESA1'];
    $aNombres = array();
    $aNombresConApellido1 = array();
   
    $aDNIs = array();
  	
  	for ($i=1; $i <= $_POST['totalAlumnos']; $i++) 
		{ 
			
			if ($empresaAnterior == $_POST['NOMBRE_EMPRESA'.$i])
			{
				$aNombres [] = $_POST['NOMBRE_ALUMNO'.$i]." ".$_POST['APELLIDO1_ALUMNO'.$i]." ".$_POST['APELLIDO2_ALUMNO'.$i];
				$aNombresConApellido1 [] = $_POST['NOMBRE_ALUMNO'.$i]." ".$_POST['APELLIDO1_ALUMNO'.$i];
				$aDNIs [] = $_POST['DNI_ALUMNO'.$i];
				if ($i == $_POST['totalAlumnos'])
				{
					echo '<a class="list-group-item ">'.'&emsp;&emsp;'.generarAnexo21($folder,$sTextoCarpetaAMostrar,$i,$aNombres,$aNombresConApellido1,$aDNIs,$idFolder).'</a>';
				}		
			}
			else
			{
				echo '<a class="list-group-item ">'.'&emsp;&emsp;'.generarAnexo21($folder,$sTextoCarpetaAMostrar,$i-1,$aNombres,$aNombresConApellido1,$aDNIs,$idFolder).'</a>';
				$empresaAnterior = $_POST['NOMBRE_EMPRESA'.$i];
				$aNombres = array();
				$aNombresConApellido1 = array();
    		$aDNIs = array();
				$aNombres [] = $_POST['NOMBRE_ALUMNO'.$i]." ".$_POST['APELLIDO1_ALUMNO'.$i]." ".$_POST['APELLIDO2_ALUMNO'.$i];
				$aNombresConApellido1 [] = $_POST['NOMBRE_ALUMNO'.$i]." ".$_POST['APELLIDO1_ALUMNO'.$i];
				$aDNIs [] = $_POST['DNI_ALUMNO'.$i];
				if ($i == $_POST['totalAlumnos'])
				{
					echo '<a class="list-group-item ">'.'&emsp;&emsp;'.generarAnexo21($folder,$sTextoCarpetaAMostrar,$i,$aNombres,$aNombresConApellido1,$aDNIs,$idFolder).'</a>';
				}		
			}
		}

}





?>