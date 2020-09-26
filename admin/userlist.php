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
if(isset($_GET['del']) && isset($_GET['name']))
{
$ID=$_GET['del'];
$name=$_GET['name'];

$alumno = getAlumnoFromID($dbh,$ID);
borrarCromosNoPoseidosFromIdCreador($dbh,$ID);
borrarBonosFromAlumnoId($dbh,$ID);
borrarAlumnoFromId($dbh,$ID);
if (($alumno['ID_MIBOT']!=NULL)&&($alumno['ID_MIBOT']!=""))
{
	borrarBotFromId($dbh,$alumno['ID_MIBOT']);	
}

if (($alumno['ID_MIACTOR']!=NULL)&&($alumno['ID_MIACTOR']!=""))
{
	borrarActorFromId($dbh,$alumno['ID_MIACTOR']);
}
borrarFaltasAlumno($dbh,$ID);
borrarEstrellasAlumno($dbh,$ID);
borrarAlumnoTarea($dbh,$ID);
borrarNotificacionReceiver($dbh,$alumno['CORREO']);

$sql2 = "insert into deleteduser (email) values (:name)";
$query2 = $dbh->prepare($sql2);
$query2 -> bindParam(':name',$name, PDO::PARAM_STR);
$query2 -> execute();

$msg="Datos borrados correctamente";
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
	
	<title>Administrar Alumnos</title>

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
	<?php include('includes/header.php');?>

	<div class="ts-main-content">
		<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">Administrar Alumnos</h2>

							
							<div class="panel-body">
							<?php if($msg){?><div class="succWrap" ID="msgshow"><?php echo htmlentities($msg); ?> </div><?php }?>
							</div>

					</div>
				</div>

			</div>
											<table ID="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
									<thead>
										<tr>
										<th>#</th>
												<th>Imagen</th>
                                                <th>Nombre</th>
                                                <th>Apellido1</th>
                                                <th>Apellido2</th>
                                                <th>Curso</th>
                                                <th>Nivel</th>
                                                <th>Calas</th>
                                                <th>Correo</th>
                                                <th>Último login</th>
												<th>Acción</th>	
										</tr>
									</thead>
									
									<tbody>




<?php 

$sql = "SELECT * from  ALUMNOS WHERE ID <>-1";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{				


	?>	
										<tr>
											<td><?php echo htmlentities($cnt);?></td>
											<td><img src="../images/<?php echo htmlentities($result->image);?>" style="width:50px; border-radius:50%;"/></td>
                                            <td><?php echo htmlentities($result->NOMBRE);?></td>
                                             <td><?php echo htmlentities($result->APELLIDO1);?></td>
                                            <td><?php echo htmlentities($result->APELLIDO2);?> 
                                            </td>
                                            
 <td><a  data-toggle="tooltip" title="Ver Ranking en otra ventana" href="admin_ranking.php?idc=<?php echo $result->ID_CURSO?>" target=”_blank”><?php echo htmlentities(getNombreCursoFromID($dbh,$result->ID_CURSO));?></a></td>
 <td><?php echo htmlentities($result->NUMERO_NIVEL);?></td>
 <td><?php echo htmlentities(getMiActorFromAlumnoID($dbh,$result->ID)['CALAS']);?></td>
 <td><?php echo htmlentities($result->CORREO);?></td>
 <td><?php echo htmlentities($result->ULTIMA_FECHA_LOGIN);?></td>
<td>
<a href="dar_calas.php?a=<?php echo $result->ID.'&c='.$result->ID_CURSO;?>" onclick="return confirm('¿Quieres modificar calas?');">&nbsp; <i class="fa fa-usd" style="color:orange"></i></a>&nbsp;&nbsp;
<a href="dar_bono.php?a=<?php echo $result->ID.'&c='.$result->ID_CURSO;?>" onclick="return confirm('¿Quieres dar bono?');">&nbsp; <i class="fa fa-star"></i></a>&nbsp;&nbsp;
<a href="enviar_notificacion.php?a=<?php echo $result->ID.'&c='.$result->ID_CURSO;?>" onclick="return confirm('¿Quieres enviar notificación?');">&nbsp; <i class="fa fa-envelope"></i></a>&nbsp;&nbsp;
<a href="edit-user.php?edit=<?php echo $result->ID;?>" onclick="return confirm('¿Quieres editar?');">&nbsp; <i class="fa fa-pencil"></i></a>&nbsp;&nbsp;
<a href="userlist.php?del=<?php echo $result->ID;?>&name=<?php echo htmlentities($result->CORREO);?>" onclick="return confirm('¿Quieres borrarlo?');"><i class="fa fa-trash" style="color:red"></i></a>&nbsp;&nbsp;
</td>
										</tr>
										<?php $cnt=$cnt+1; }} ?>
										
									</tbody>
								</table>

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

		$('#zctb').DataTable( {
    scrollY: 300,
    paging: false
} );
				 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 3000);
					});
		</script>
		
</body>
</html>
<?php } ?>
