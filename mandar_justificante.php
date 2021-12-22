<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
$msg="";



if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{	
	header('location:index.php');
}
else{
	
$alumno = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);
//var_export($_SESSION['alogin']);

if(isset($_POST['esDelete'])&&($_POST['esDelete']!=0))
{
	
$justiAux = getJustificanteFromId($dbh,$_POST['esDelete']);

	if (file_exists($justiAux['ENLACE']))
	{
    unlink($justiAux['ENLACE']);  
	}
	borrarJustificanteFromId($dbh,$_POST['esDelete']);
	$msg="justificante borrado correctamente...";
}
if(isset($_POST['esMandar'])&&($_POST['esMandar']==1))
{	
	
	
	//var_export($alumno);
	$folder="justificantes/".getAsignaturasFromCurso($dbh,$alumno['ID_CURSO'])[0]['NOMBRE'];
  //var_export($folder);
  if (!file_exists($folder)) {
      mkdir($folder, 0777,true);
      file_put_contents($folder.'/default.php', 'ondevasmaestro...');
  }

  if ($_FILES['archivo1']['name']!='')
  {
      $bHayArchivo=true;
      $file = $_FILES['archivo1']['name'];
      $ok=false;
      $enlace = $folder."/".$alumno['NOMBRE'].'_'.$alumno['APELLIDO1'].'_'.$alumno['APELLIDO2'].'__'.date("Y-m-d H:i:s").'_'.$file;
      if(move_uploaded_file($_FILES['archivo1']['tmp_name'],$enlace))
      {
          $ok=true;
      }
  }


	insertJustificante($dbh,$_POST['diadesde'],$_POST['diahasta'],$alumno['ID'],$_POST['textComent'],$enlace);
	$lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {
			$msg="justificante mandado correctamente, ¡gracias!";
    }
    else
    {
			$msg="Error al mandar el justificante, intentalo más tarde...";
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
	
	<title>Mandar Justificante</title>

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
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">MANDAR JUSTIFICANTE</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" id="form1" class="form-horizontal" enctype="multipart/form-data" >
<input type="hidden" name="esDelete" id="esDelete" value="0">
<input type="hidden" name="esMandar" id="esMandar" value="0">
<div class="form-group">
	<div class="col-sm-4">
	</div>
	<div class="col-sm-4 text-center">
		<img src="" style="width:200px; border-radius:50%; margin:10px;">
	</div>
	<div class="col-sm-4">
	</div>
</div>



<div class="form-group">
<label class="col-sm-1 control-label">DÍA DESDE</label>
<div class='col-sm-5'>
    <input type='text' name="diadesde" class="form-control" id='datetimepicker4' />
</div>
<label class="col-sm-1 control-label">DÍA HASTA</label>
<div class='col-sm-5'>
    <input type='text' name="diahasta" class="form-control" id='datetimepicker42' />
</div>

<script type="text/javascript">
                                    $(function () {
                        var now = new Date();
                var dateNow1Week = now.setDate(now.getDate());
                                        $('#datetimepicker4').datetimepicker({
                                            format: 'YYYY-MM-DD',defaultDate:moment(dateNow1Week).hours(23).minutes(59).seconds(59).milliseconds(0)
                                        });                                       
                                    });
                                    $(function () {
                        var now = new Date();
                var dateNow1Week = now.setDate(now.getDate());
                                        $('#datetimepicker42').datetimepicker({
                                            format: 'YYYY-MM-DD',defaultDate:moment(dateNow1Week).hours(23).minutes(59).seconds(59).milliseconds(0)
                                        });                                       
                                    });

                                </script>  
</div>
<label class="col-sm-12">(Si es para un solo día poner ese día en ambos campos)</label>
<div class="form-group">
	<div class="col-sm-4">
	</div>
	<div class="col-sm-4 text-center">
		<img src="" style="width:200px; border-radius:50%; margin:10px;">
	</div>
	<div class="col-sm-4">
	</div>
</div>
<div class="form-group">
	<label class="col-sm-7">SUBIR FICHERO (si son varios agruparlos en uno solo)</label>
	<div class="col-sm-5">
		<input type="file" id="archivo1" name="archivo1" class="form-control">                          
	</div>
</div> 
<div class="form-group">
	<label class="col-sm-12">Comentario</label>

<div class='col-sm-12'>
        <textarea maxlength = "999" required id="textComent" name="textComent" class="form-control"></textarea>  
</div>
</div>
<div class="form-group">
	<div class="col-sm-12">
<a onclick="manageMandar()" class="btn btn-danger" >Mandar Justificante</a>
	</div>
</div>

</form>
									</div>
								</div>

<div class="panel panel-default">
									<div class="panel-heading">MIS JUSTIFICANTES</div>



<table ID="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
  <!--Table head-->
  <thead>
    <tr>
      <th>Fecha desde</th>
      <th>Fecha hasta</th>
      <th>Comentario</th>
      <th>Archivo</th>         
      <th>Borrar</th>         
   </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
<?php


function diferenciaDias($date1,$date2)
{
	$startTimeStamp = strtotime($date1);
$endTimeStamp = strtotime($date2);

$timeDiff = abs($endTimeStamp - $startTimeStamp);

$numberDays = $timeDiff/86400;  // 86400 seconds in one day

// and you might want to convert to integer
$diaN = intval($numberDays)+1;

return "(".$diaN.(($diaN==1)?" día":" días").")";

}



$justifcantesAlumno = getJustificantesFromAlumno($dbh,$alumno['ID']);
foreach ($justifcantesAlumno as $justifI) 
{
	echo '<tr class="table-info">';

        echo '<td>'.$justifI['DIA_DESDE'].'</td>';
        echo '<td>'.$justifI['DIA_HASTA'].' '.diferenciaDias($justifI['DIA_DESDE'],$justifI['DIA_HASTA'])
.'</td>';
        echo '<td align="center" data-toggle="tooltip" title="'.$justifI['COMENTARIO'].'">&nbsp; '.((strlen($justifI['COMENTARIO'])<50)?$justifI['COMENTARIO']:'<i class="fa fa-comment"></i>').'</td>';
        echo '<td align="center" >'.
'<a target="_blank" href="'.$justifI['ENLACE'].'">&nbsp; <i class="fa fa-file"></i></a>'
        .'</td>';
        echo '<td onclick="manageDelete('.$justifI['ID'].')" align="center" >&nbsp; <i class="fa fa-trash"></i></td>';

  echo '</tr>';
}
?>
  </tbody>
  <!--Table body-->


</table>



							</div>
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
					}, 3000);
					});

	function manageDelete(delId)
	{
				Swal.fire({
          title: '¿Seguro que quieres borrar el justificante?',
          text: "¿Seguro, Seguro, Seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, lo borro para siempre!'
        }).then((result) => {
          if (result.value) {
    document.getElementById("esDelete").value=delId;
    document.getElementById("form1").action="mandar_justificante.php";
    document.getElementById("form1").submit(); 
          }
        });
	}
	function manageMandar()
	{

var pre = document.getElementById("datetimepicker4").value;
var res = document.getElementById("datetimepicker42").value;
var res3 = document.getElementById("archivo1").value;

	if (pre == '' || res == '' || res3 == '') 
	{
		Swal.fire('UFFF!',"Se deben rellenar los campos de los dos días y el archivo justificante" ,'error');
		return false;
	}
	else
	{

		Swal.fire({
          title: '¿Seguro que quieres mandar este justificante?',
          text: "¿Seguro, Seguro, Seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, lo mando!'
        }).then((result) => {
          if (result.value) {
    document.getElementById("esMandar").value=1;
   document.getElementById("form1").action="mandar_justificante.php";
    document.getElementById("form1").submit(); 
          }
        });
	}
}

	$('#zctb').DataTable( {
    "order": [[ 0, "desc" ]]
} );
	</script>
</body>
</html>
<?php } ?>