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

	//var_export($_POST);	
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
	
	<title>Lista Justa</title>

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

<body >
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h3 class="page-title">Lista para Justa de la clase <?php echo getCursoFromCursoID($dbh,$idCur)['NOMBRE']?>/<?php 
  echo getAsignaturasFromCurso($dbh,$idCur)[0]['NOMBRE']?></h3>
						<div class="row">
							<div class="col-md-12">
							
									
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">

<form target="_blank" action="vs.php" id="form3" name="form3" method="post">
  <input type="hidden" name="idc" id="idc" value="<?php echo $_POST['idc4']?>"/>					

							<div class="form-group col-md-4">
  <a onclick="hacerSubmit();" class="btn btn-info btn-outline btn-wrap-text">Ver Justa</a>
</div> 



											<table ID="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
										<th>#</th>
                                                <th>Imagen</th>
                                                <th>Nombre</th>
                                                <th>Apellido1</th>
                                                <th>Apellido2</th>
										</tr>
									</thead>
									
									<tbody>




<?php 


$aAlumnosCurso = getAlumnosFromCursoID($dbh,$idCur);

$cnt=1;

foreach($aAlumnosCurso as $alumnoI)
{				
	

	?>	
										<tr>

											<td><input type="checkbox" name="cCheckIds[]" value="<?php echo $alumnoI['ID']?>"></td>
											
											<td><img src="../images/<?php 
		$dbImage = htmlentities($alumnoI['image']);		
		if (($dbImage!="")&&(file_exists("../images/".$dbImage)))
		{   
			echo $dbImage;
		}
		else
		{
			echo "anonimous_profile.jpg";
		}

											?>" style="width:50px; border-radius:50%;"/></td>
                                            
                                            <td><?php echo htmlentities($alumnoI['NOMBRE']);?></td>
                                             <td><?php echo htmlentities($alumnoI['APELLIDO1']);?></td>
                                            <td><?php echo htmlentities($alumnoI['APELLIDO2']);?> 
                                            </td>
										</tr>
										<?php 

$cnt=$cnt+1; 
								}?>


										
									</tbody>
								</table>





						

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

	function hacerSubmit()
	{
   		if (checkTheBox())
   		{
   			document.getElementById("form3").submit(); 	
   		}	
	}
 function checkTheBox() {
    var flag = 0;
    for (var i = 0; i< document.form3["cCheckIds[]"].length; i++) {
      if(document.form3["cCheckIds[]"][i].checked){
        flag ++;
      }
    }
    if (flag != 2) {
    	Swal.fire('UFFF!',"Se debe seleccionar 2 elementos para la justa!" ,'error');
      return false;
    }
    return true;
  }
	</script>
	</form>	
	</div>
					</div>
				
			</div>



		
	</div>
	</div>
					</div>
				
			</div>



		
	</div>
</body>
</html>
<?php } ?>