<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$sql = "SELECT username from admin;";
		$query = $dbh -> prepare($sql);
		$query->execute();
		$result=$query->fetch(PDO::FETCH_OBJ);

if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))

	{	
header('location:index.php');
}
else{
require_once ("../UTILS/driveutils.php");
listarEventosCalendar();

$fi = new FilesystemIterator("../img/comics", FilesystemIterator::SKIP_DOTS);
$numeroComics = iterator_count($fi)-1;
$nDayOfYear = date('z') + 1;
$nombreComic = $nDayOfYear % $numeroComics;

$randomColorB4 = array("primary","secondary","success","danger","warning","info","dark")[rand(0,6)];
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
	
	<title>Admin Dashboard</title>

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
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <style type="text/css">
.scroller {
  height: 3em;
  line-height: 3em;
  position: relative;
  overflow: hidden;
 
}
<?php
	$nEventos = count($results->getItems());	

?>
.scroller > span {
  position: absolute;
  top: 0;
  animation: slide <?php echo(($nEventos/2)*5)?>s infinite;
  font-weight: bold;
  
}
@keyframes slide {

<?php
	$increPorcent = 100/$nEventos;
	for ($i=0;$i< $nEventos;$i++)
	{
	echo ($i*$increPorcent).'% ';
	echo ' { top: -'.(($i*3)).'em;} '."\r\n";
	}
?>


}
  </style>
</head>

<body>
<?php include('includes/header.php');?>

	<div class="ts-main-content">
<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">

				<?php

if (count($results->getItems()) == 0) {
  print "<h3>No hay Eventos</h3>";
} else {

echo '<div class="alert alert-primary scroller"><span>';
        
     
 $saltoBr = "";
 $cont=1;
foreach ($results->getItems() as $event)
 {
	$start = $event->start->dateTime;
	$month = date("m",strtotime($start));
	$day = date("d",strtotime($start));
	$hora = date("h",strtotime($start));
	$minutos = date("i",strtotime($start));
	if (empty($start)) {
      $start = $event->start->date;
    }
    $fin = $event->end->dateTime;
	$month2 = date("m",strtotime($fin));
	$day2 = date("d",strtotime($fin));
	$hora2 = date("h",strtotime($fin));
	$minutos2 = date("i",strtotime($fin));
    if (empty($fin)) {
      $fin = $event->end->date;
    }
    echo $saltoBr.$cont.".-".($event->getSummary().(($event->getDescription()=="")?"":("(".$event->getDescription().")")))." [".$day."/".$month."(".$hora.":".$minutos.")-".$day2."/".$month2."(".$hora2.":".$minutos2.")]";
    $saltoBr="<br/>";
    $cont++;

}
echo '</span><button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button></div>';

}
?>

				
				<div class='alert alert-<?php echo $randomColorB4?> alert-dismissible'>
  <button type="button" class="close" data-dismiss="alert">&times;</button> 
  <img align = "center" class="img-fluid mx-auto d-block" 
  src="../img/comics/<?php echo $nombreComic ?>.gif". alt="Chania">
</div>
				<div class="row">
					<div class="col-md-12">

						<h2 class="page-title">Dashboard</h2>
						
						<div class="row">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-3">
										<div class="panel panel-default">
											<div class="panel-body bk-primary text-light">
												<div class="stat-panel text-center">
<?php 
$sql ="SELECT ID from ALUMNOS WHERE ID <>-1";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$bg=$query->rowCount();
?>
													<div class="stat-panel-number h1 "><?php echo htmlentities($bg);?></div>
													<div class="stat-panel-title text-uppercase">TOTAL ALUMNOS</div>
												</div>
											</div>
											<a href="userlist.php" class="block-anchor panel-footer">Detalles <i class="fa fa-arrow-right"></i></a>
										</div>
									</div>
									<div class="col-md-3">
										<div class="panel panel-default">
											<div class="panel-body bk-success text-light">
												<div class="stat-panel text-center">

<?php 
$reciver = 'Admin';
$sql1 ="SELECT ID from feedback where reciver = (:reciver)";
$query1 = $dbh -> prepare($sql1);;
$query1-> bindParam(':reciver', $reciver, PDO::PARAM_STR);
$query1->execute();
$results1=$query1->fetchAll(PDO::FETCH_OBJ);
$regbd=$query1->rowCount();
?>
													<div class="stat-panel-number h1 "><?php echo htmlentities($regbd);?></div>
													<div class="stat-panel-title text-uppercase">Mensajes de Feedback</div>
												</div>
											</div>
											<a href="feedback.php" class="block-anchor panel-footer text-center">Detalles &nbsp; <i class="fa fa-arrow-right"></i></a>
										</div>
									</div>

													<div class="col-md-3">
										<div class="panel panel-default">
											<div class="panel-body bk-danger text-light">
												<div class="stat-panel text-center">

<?php 
$reciver = 'Admin';
$sql12 ="SELECT ID from notification where notireciver = (:reciver)";
$query12 = $dbh -> prepare($sql12);;
$query12-> bindParam(':reciver', $reciver, PDO::PARAM_STR);
$query12->execute();
$results12=$query12->fetchAll(PDO::FETCH_OBJ);
$regbd2=$query12->rowCount();
?>
													<div class="stat-panel-number h1 "><?php echo htmlentities($regbd2);?></div>
													<div class="stat-panel-title text-uppercase">Notificaciones</div>
												</div>
											</div>
											<a href="notification.php" class="block-anchor panel-footer text-center">Detalles &nbsp; <i class="fa fa-arrow-right"></i></a>
										</div>
									</div>
									<div class="col-md-3">
										<div class="panel panel-default">
											<div class="panel-body bk-info text-light">
												<div class="stat-panel text-center">
												<?php 
$sql6 ="SELECT ID from deleteduser ";
$query6 = $dbh -> prepare($sql6);;
$query6->execute();
$results6=$query6->fetchAll(PDO::FETCH_OBJ);
$query=$query6->rowCount();
?>
													<div class="stat-panel-number h1 "><?php echo htmlentities($query);?></div>
													<div class="stat-panel-title text-uppercase">ALUMNOS BORRADOS</div>
												</div>
											</div>
											<a href="deleteduser.php" class="block-anchor panel-footer text-center">Detalles &nbsp; <i class="fa fa-arrow-right"></i></a>
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
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
	
	<script>
		
	window.onload = function(){
    
		// Line chart from swirlData for dashReport
		var ctx = document.getElementById("dashReport").getContext("2d");
		window.myLine = new Chart(ctx).Line(swirlData, {
			responsive: true,
			scaleShowVerticalLines: false,
			scaleBeginAtZero : true,
			multiTooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %>",
		}); 
		
		// Pie Chart from doughutData
		var doctx = document.getElementById("chart-area3").getContext("2d");
		window.myDoughnut = new Chart(doctx).Pie(doughnutData, {responsive : true});

		// Dougnut Chart from doughnutData
		var doctx = document.getElementById("chart-area4").getContext("2d");
		window.myDoughnut = new Chart(doctx).Doughnut(doughnutData, {responsive : true});

	}
	</script>
</body>
</html>
<?php } ?>