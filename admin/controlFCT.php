<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
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
else{



if (isset($_GET['idCiclo']))
{
  $idCiclo=$_GET['idCiclo'];
}
if (isset($_POST['idCiclo']))
{
  $idCiclo=$_POST['idCiclo'];
}
$filtroPeriodoSQL="";
if ((isset($_POST['idPeriFiltro']))&&($_POST['idPeriFiltro']!=""))
{
	$aPeriodos = explode(',', $_POST['idPeriFiltro']);
	$orr = "";
	$cond_or = "";
	foreach ($aPeriodos as $perii) 
	{
		if ($perii!="")
		{
			$cond_or=$cond_or.$orr." ID_FCT_PERIODO =".$perii." ";
			$orr=" OR ";
		}
	}
  $filtroPeriodoSQL="(".$cond_or.") AND ";
}


$aCiclos = ejecutarQuery($dbh,"SELECT * FROM FCT_CICLOS WHERE ID =".$idCiclo);

$nombreCiclo = $aCiclos[0]['NOMBRE'];
//var_export($aCiclos[0]);
$claveCiclo = $aCiclos[0]['CLAVE_CICLO'];
$aTutoresCole = ejecutarQuery($dbh,"SELECT * FROM FCT_TUTORES_PROFES WHERE ID_FCT_CICLO =".$idCiclo);

$nombreTutorCole = $aTutoresCole[0]['NOMBRE']." ".$aTutoresCole[0]['APELLIDO1']." ".$aTutoresCole[0]['APELLIDO2'];

$aAlumnos = ejecutarQuery($dbh,"SELECT * FROM FCT_ALUMNOS WHERE ID_FCT_CICLO =".$idCiclo);
$aPracticas = array();	
foreach ($aAlumnos as $alumnoAux) 
{
	$aPracticas = array_merge($aPracticas, ejecutarQuery($dbh,"SELECT * FROM FCT_PRACTICAS WHERE ".$filtroPeriodoSQL." ID_FCT_ALUMNO =".$alumnoAux['ID'])); 
}

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
	
	<title>FCT: <?php echo $nombreCiclo?></title>

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

  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css"/>
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
tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
		</style>


</head>

<body>


		<div class="content-wrapper">
			<div class="container-fluid">
								<a onclick="managebuttonDash()"  class="btn btn-dark btn-outline btn-wrap-text">Volver MENU</a>
								<div class="form-group">
									<div class="col-sm-4">
									</div>
								</div>

<div class="panel panel-default">
	<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>								
	<div class="panel-heading">CONTROL FCT</div>

<div class="container-fluid">
	
<form id="form2" method="post" action="resumenFCT.php">
</form>
<form id="form3" method="post" action="controlFCT.php">
	<input type="hidden" name="idPeriFiltro" id="idPeriFiltro"/>
	<input type="hidden" name="idCiclo" id="idCiclo" value="<?php echo $idCiclo?>"/>

</form>

<form target="_blank" action="generarDocFCT.php" method="post" id="form1" class="form-horizontal" enctype="multipart/form-data" >
<input type="hidden" name="idCiclo" id="idCiclo" value="<?php echo $idCiclo?>"/>
<input type="hidden" name="idPeriodo2" id="idPeriodo2"/>

<h1>CICLO: <?php echo '<a data-toggle="tooltip" title="CLAVE CICLO:'.$claveCiclo.' Pincha para más detalle" href="manageTabla.php?tabla=FCT_CICLOS&idSearch='.$idCiclo.'" target="_blank">'.$nombreCiclo.'</a>'?></h1>
<h1>CLAVE: <?php echo $claveCiclo?></h1>
<h2>TUTOR: <?php echo '<a data-toggle="tooltip" title="DNI:'.$aTutoresCole[0]['DNI'].' Pincha para más detalle" href="manageTabla.php?tabla=FCT_TUTORES_PROFES&idSearch='.$aTutoresCole[0]['ID'].'" target="_blank">'.$nombreTutorCole.'</a>'?></h2>


<div class="container-fluid">
<div class="form-group">
	<div class="col-sm-4">
	</div>
	<div class="col-sm-4 text-center">
	</div>
	<div class="col-sm-4">
	</div>
</div>

<span class="label label-danger" style="font-size:12px;">NUEVO:Se pueden seleccionar varios periodos, esto sólo tiene sentido para generar el anexo 2.2 de varios periodos, para otros anexos sólo dejar seleccionado 1 periodo</span>
<div class="row">
                <div class="col-sm-4">
                    <div class="form-group">
                        <select onchange="cambiarPeriodo()" class="form-control" id="idPeriodo" name="idPeriodo" multiple="multiple">
                        	<option value=""></option>;
                        	<?php
                        	$aPeriodosAux2 = ejecutarQuery($dbh,"SELECT * FROM FCT_PERIODOS WHERE ID IN (SELECT ID_FCT_PERIODO FROM FCT_PRACTICAS WHERE ID_FCT_ALUMNO IN (SELECT ID FROM FCT_ALUMNOS WHERE ID_FCT_CICLO=".$idCiclo.")) ORDER BY FECHA_TERMINACION DESC");
                        	foreach ($aPeriodosAux2 as $periAux) 
                        	{
$nombrePeriodoAux2 = $periAux['FECHA_INICIO']." / ".$periAux['FECHA_TERMINACION'].(($periAux['INFO']=="")?"":" (".$periAux['INFO'].")");

if (isset($_POST['idPeriFiltro']))
{
	$aPeriodos = explode(',', $_POST['idPeriFiltro']);
}
echo '<option '.(((isset($_POST['idPeriFiltro']))&&(in_array($periAux['ID'], $aPeriodos)))?" selected ":"").' value="'.$periAux['ID'].'">'.$nombrePeriodoAux2.'</option>';
                        	}
                        	
                        	?>
                            
                        </select>
                    </div>
                </div> 
            </div>
            <div class="row">
                	<div class="col-sm-4">
                    <div class="form-group">
                        <a onclick="generarDoc();"  data-toggle="tooltip" class="btn btn-warning btn-outline btn-wrap-text" title="Generar documentación del periodo" >Generar Documentación</a>
                </div>
            </div>


</div>
</div>

<div class="form-group">
	<div class="col-sm-4">
	</div>
	<div class="col-sm-4 text-center">
	</div>
	<div class="col-sm-4">
	</div>
</div>


<table ID="example2" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
  <!--Table head-->
  <thead>
    <tr>
<th>PERIODO</th>
<th>ALUMNO</th>
<th>EMPRESA</th>
<th>TUTOR_EMPRESA</th>
<th>DIRECCION_TRABAJO</th>
<th>HORARIOS</th>
<th>ENLACE_DOCS</th>

    

   </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>


<?php

foreach ($aPracticas as $practicaAux) 
{
	echo '<tr>';
		$aPeriodosAux = ejecutarQuery($dbh,"SELECT * FROM FCT_PERIODOS WHERE ID =".$practicaAux['ID_FCT_PERIODO']);
	  $nombrePeriodoAux = $aPeriodosAux[0]['FECHA_INICIO']." / ".$aPeriodosAux[0]['FECHA_TERMINACION'].(($aPeriodosAux[0]['INFO']=="")?"":" (".$aPeriodosAux[0]['INFO'].")");
	   echo '<td>'.'<a data-toggle="tooltip" title="HORAS/DIA:'.$aPeriodosAux[0]['HORAS_DIA'].' TOTAL HORAS:'.$aPeriodosAux[0]['TOTAL_HORAS'].' Pincha para más detalle" href="manageTabla.php?tabla=FCT_PERIODOS&idSearch='.$aPeriodosAux[0]['ID'].'" target="_blank">'.$nombrePeriodoAux.'</a></td>';
		$aAlumnosAux = ejecutarQuery($dbh,"SELECT * FROM FCT_ALUMNOS WHERE ID =".$practicaAux['ID_FCT_ALUMNO']);
	  $nombreAlumnoAux = $aAlumnosAux[0]['NOMBRE']." ".$aAlumnosAux[0]['APELLIDO1']." ".$aAlumnosAux[0]['APELLIDO2'];
	  echo '<td>'.'<a data-toggle="tooltip" title="CORREO:'.$aAlumnosAux[0]['CORREO'].' DNI:'.$aAlumnosAux[0]['DNI'].' Pincha para más detalle" href="manageTabla.php?tabla=FCT_ALUMNOS&idSearch='.$aAlumnosAux[0]['ID'].'" target="_blank">'.$nombreAlumnoAux.'</a></td>';
		$aEmpresasAux = ejecutarQuery($dbh,"SELECT * FROM FCT_EMPRESAS WHERE ID =".$practicaAux['ID_FCT_EMPRESA']);
	  
echo '<td>'.'<a data-toggle="tooltip" title="Nº CONVENIO:'.$aEmpresasAux[0]['N_CONVENIO'].' CONTACTO:'.$aEmpresasAux[0]['CONTACTO'].' Pincha para más detalle" href="manageTabla.php?tabla=FCT_EMPRESAS&idSearch='.$aEmpresasAux[0]['ID'].'" target="_blank">'.$aEmpresasAux[0]['NOMBRE'].'</a></td>';
		
	  echo '<td><a data-toggle="tooltip" title="Pincha para más detalle" href="manageTabla.php?tabla=FCT_PRACTICAS&idSearch='.$practicaAux['ID'].'" target="_blank">'.$practicaAux['NOMBRE_TUTOR_EMPRESA'].'('.$practicaAux['CONTACTO_TUTOR_EMPRESA'].')</a></td>';

	  echo '<td><a data-toggle="tooltip" title="Pincha para más detalle" href="manageTabla.php?tabla=FCT_PRACTICAS&idSearch='.$practicaAux['ID'].'" target="_blank">'.$practicaAux['DIRECCION_TRABAJO'].'('.$practicaAux['LOCALIDAD_TRABAJO'].')</a></td>';

	  echo '<td><a data-toggle="tooltip" title="Pincha para más detalle" href="manageTabla.php?tabla=FCT_PRACTICAS&idSearch='.$practicaAux['ID'].'" target="_blank">'.$practicaAux['HORARIOS'].'</a></td>';

$folderServidor="../docsFCT/".$claveCiclo;
$folderDrive = "https://drive.google.com/drive/folders/1jU6GD0c_H33gM_TFjgdmSUHRRE_iGlbV";
	  echo '<td align="center" >'.
'<a target="_blank" title="carpeta servidor" href="'.$folderServidor.'">&nbsp; <i class="fa fa-file"></i></a>'.
'<a target="_blank" title="Google drive" href="'.$folderDrive.'">&nbsp; <img src="img/drive.png" width="16" height="16"/></a>'
        .'</td>';
  echo '</tr>';
}

?>

  </tbody>


    <tr>
<th>PERIODO</th>
<th>ALUMNO</th>
<th>EMPRESA</th>
<th>TUTOR_EMPRESA</th>
<th>DIRECCION_TRABAJO</th>
<th>HORARIOS</th>
<th>ENLACE_DOCS</th>
   </tr>


     <tfoot>
     	    <tr>
<th>PERIODO</th>
<th>ALUMNO</th>
<th>EMPRESA</th>
<th>TUTOR_EMPRESA</th>
<th>DIRECCION_TRABAJO</th>
<th>HORARIOS</th>
<th>ENLACE_DOCS</th>
   </tr>
        </tfoot>  

</table>

</form>

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

	    $(document).ready(function() 
	    {
        	$('#idPeriodo').multiselect();
    	});



$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#example2 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder=" '+title+'" />' );
    } );
 
    // DataTable
    var table3 = $('#example2').DataTable({
    	    	"order": [[ 2, "asc" ]],
    "scrollX": true,
        initComplete: function () {
					

            // Apply the search
            this.api().columns().every( function () {
                var that = this;
 
                $( 'input', this.footer() ).on( 'keyup change clear', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
        }
    });	
} );


  function generarDoc()
  {

  	sSel11Value = document.getElementById('idPeriodo').value;
     
     if ((sSel11Value==''))
     {
       Swal.fire('EOOHHH!','Elige un periodo' ,'warning');;
     }
     else
     {
     		document.getElementById('idPeriodo2').value= getSelectValues(document.getElementById('idPeriodo'));
     		document.getElementById('form1').submit();  
     }
    
  }

    function managebuttonDash()
  { 
      document.getElementById("form2").submit();
  }
    function cambiarPeriodo()
  {  		
  		document.getElementById("idPeriFiltro").value = getSelectValues(document.getElementById('idPeriodo'));
  		//alert(getSelectValues(document.getElementById('idPeriodo')));
      document.getElementById("form3").submit();
  }

function getSelectValues(select) {
  var result = [];
  var options = select && select.options;
  var opt;

  for (var i=0, iLen=options.length; i<iLen; i++) {
    opt = options[i];

    if (opt.selected) {
      result.push(opt.value || opt.text);
    }
  }
  return result;
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