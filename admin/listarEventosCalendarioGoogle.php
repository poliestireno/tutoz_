

<?php
require_once ("../UTILS/driveutils.php");
listarEventosCalendar();
?>
<!DOCTYPE html>
<html>


<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">
	<meta name="theme-color" content="#3e454c">
	
	<title>GGG</title>

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
	<style>

		
.scroller {
  height: 3em;
  line-height: 3em;
  position: relative;
  overflow: hidden;
 
}
<?php
	$nEventos = count($results->getItems())	;
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



<!--div class="alert alert-primary scroller">
        <span>
          a1<br/>
          a2<br/>
          a3<br/>
          a4<br/>
          a5<br/>
          a6<br/>
          a7<br/>
          a8<br/>
          a9<br/>
          a10<br/>
          a11<br/>
          a12<br/>
          a13<br/>
          a14<br/>
          a15<br/>
          a16<br/>
          a17<br/>
          a18<br/>
          a19<br/>

        </span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
      </div-->
<?php
echo 'Número de eventos:';
var_export(count($results->getItems()));

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
    echo $saltoBr.$cont.".-".($event->getSummary().(($event->getDescription()=="")?"":("(".$event->getDescription().")")))."[".$day."/".$month."(".$hora.":".$minutos.")-".$day2."/".$month2."(".$hora2.":".$minutos2.")]";
    $saltoBr="<br/>";
    $cont++;

}

echo '</span><button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button></div>';

}
?>
</body>
</html>