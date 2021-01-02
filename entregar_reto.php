<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{   
    header('location:index.php');
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

	
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
	<link rel="stylesheet" href="css/bootstrap-social.css">
	<link rel="stylesheet" href="css/bootstrap-select.css">
	<link rel="stylesheet" href="css/fileinput.min.css">
	<link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
	<link rel="stylesheet" href="css/style.css">
        <!-- Loading Scripts -->
        <script src="js/jquery.min.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>

    <script src="js/bootstrap-select.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <script src="js/dataTables.bootstrap.min.js"></script>
    <script src="js/Chart.min.js"></script>
    <script src="js/fileinput.js"></script>
    <script src="js/chartData.js"></script>
    <script src="js/main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>



    <?php

function getDiferenciaDates($date2,$date1)
{
    // Formulate the Difference between two dates
    $date1 = strtotime($date1);  
    $date2 = strtotime($date2);  
    $diff = abs($date2 - $date1);  
// To get the year divide the resultant date into 
// total seconds in a year (365*60*60*24) 
$years = floor($diff / (365*60*60*24));  
// To get the month, subtract it with years and 
// divide the resultant date into 
// total seconds in a month (30*60*60*24) 
$months = floor(($diff - $years * 365*60*60*24) 
                               / (30*60*60*24));  
// To get the day, subtract it with years and  
// months and divide the resultant date into 
// total seconds in a days (60*60*24) 
$days = floor(($diff - $years * 365*60*60*24 -  
             $months*30*60*60*24)/ (60*60*24)); 
// To get the hour, subtract it with years,  
// months & seconds and divide the resultant 
// date into total seconds in a hours (60*60) 
$hours = floor(($diff - $years * 365*60*60*24  
       - $months*30*60*60*24 - $days*60*60*24) 
                                   / (60*60));  
// To get the minutes, subtract it with years, 
// months, seconds and hours and divide the  
// resultant date into total seconds i.e. 60 
$minutes = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24  
                          - $hours*60*60)/ 60);  
// To get the minutes, subtract it with years, 
// months, seconds, hours and minutes  
$seconds = floor(($diff - $years * 365*60*60*24  
         - $months*30*60*60*24 - $days*60*60*24 
                - $hours*60*60 - $minutes*60));   
// Print the result 
//printf("%d years, %d months, %d days, %d hours, "
//     . "%d minutes, %d seconds", $years, $months, 
//             $days, $hours, $minutes, $seconds);
$sDife = "";
if ($years>0)
{
   $sDife .= $years." años ";
}
if ($months>0)
{
   $sDife .= $months." meses ";
}
if ($days>0)
{
   $sDife .= $days." días ";
}
if ($hours>0)
{
   $sDife .= $hours." horas ";
}
if ($minutes>0)
{
   $sDife .= $minutes." minutos ";
}
if ($seconds>0)
{
   $sDife .= $seconds." segundos";
}

return $sDife;
}

//var_export($_POST);

$idTarea = (isset($_GET['idt']))?$_GET['idt']:$_POST['idt'];
if (isset($_GET['act']))
{
    modificarEstadoReto($dbh,$_SESSION['alogin'],$idTarea,"activado");   
}
$datosAT = getDatosAlumnoTarea($dbh,$_SESSION['alogin'],$idTarea);
$ok=false;
$nArchivos=0;
$numeroEntregas=$datosAT['NUMERO_ENTREGAS']+1;
$filaTarea = getTareaFromID($dbh,$idTarea);
$dateActual = date("Y-m-d H:i:s");
$entregaFueraTiempo= ($dateActual>$filaTarea['FECHA_LIMITE']);
$sDiferenciaFueraTiempo = "";
if ($entregaFueraTiempo)
{
    $sDiferenciaFueraTiempo = getDiferenciaDates($dateActual,$filaTarea['FECHA_LIMITE']);
}
$nombreReto = $filaTarea['NOMBRE'];
$alumno = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);
if(isset($_POST['comentt']))
{    
    
    if ($datosAT['ESTADO']!='no activado')
    {
        $folder="retos/".getAsignaturasFromCurso($dbh,$alumno['ID_CURSO'])[0]['NOMBRE']."/".$nombreReto;
        //var_export($folder);
        if (!file_exists($folder)) {
            mkdir($folder, 0777,true);
            file_put_contents($folder.'/default.php', 'ondevasmaestro...');
        }
        $sListaArchivos = "";
        $comma="";
        $bHayArchivo=false;

        for ($i=1; $i < 6; $i++) 
        { 
            if ($_FILES['archivo'.$i]['name']!='')
            {
                $bHayArchivo=true;
                $file = $_FILES['archivo'.$i]['name'];
                if(move_uploaded_file($_FILES['archivo'.$i]['tmp_name'],$folder."/".$alumno['NOMBRE'].'_'.$alumno['APELLIDO1'].'_'.$alumno['APELLIDO2']."_".(($numeroEntregas<10)?"0":"").$numeroEntregas.'__'.date("Y-m-d H:i:s").'_'.$file))
                {
                    $sListaArchivos .= $comma . $file;
                    $comma="\\n";
                    $ok=true;
                    $nArchivos++;
                }
                else
                {
                   $ok=false;
                   break;
                }
            }
        }
        if (!$bHayArchivo)
        {
           $ok=true; 
        }
        if ($ok)
        {
        file_put_contents($folder."/".$alumno['NOMBRE'].'_'.$alumno['APELLIDO1'].'_'.$alumno['APELLIDO2']."_".(($numeroEntregas<10)?"0":"").$numeroEntregas.'__'.date("Y-m-d H:i:s").'_comentario.txt',$_POST['comentt']);
modificarEstadoReto($dbh,$_SESSION['alogin'],$idTarea,"entregado");
modificarFechaEntregadoReto($dbh,$_SESSION['alogin'],$idTarea,date("Y-m-d H:i:s"));
modificarComentarioReto($dbh,$_SESSION['alogin'],$idTarea,$_POST['comentt']);
modificarOtrosReto($dbh,$_SESSION['alogin'],$idTarea,"Archivos subidos:".$nArchivos);
modificarNumeroEntregasReto($dbh,$_SESSION['alogin'],$idTarea,$numeroEntregas);
$datosAT = getDatosAlumnoTarea($dbh,$_SESSION['alogin'],$idTarea);
$numeroEntregas=$datosAT['NUMERO_ENTREGAS']+1;
        }
        
    }
} 



?>



    <script type="text/javascript">

function init()
{
    <?php

    if(isset($_POST['comentt']))
    {
        $datosAT = getDatosAlumnoTarea($dbh,$_SESSION['alogin'],$idTarea);
        if ($datosAT['ESTADO']=='no activado')
        {
            echo "Swal.fire('UFFF!','Para poder entregar antes hay que activar el reto...' ,'warning');";
        }
        else if (!$ok)
        {           
            echo "Swal.fire('UFFF!','Hay problemas para la entrega...' ,'warning');";
        }
        else 
        {     
           if (!$bHayArchivo)
        {
            echo 'var str="<h3>¡Reto entregado!</h3>";';
         }
         else
        {
        echo 'var str="<h3>¡Reto entregado!</h3> Archivos subidos:'.$nArchivos.'"+"\n"+"'.$sListaArchivos.'";';           
        }
echo "Swal.fire({
  html: '<pre>' + str + '</pre>',
  customClass: {
    popup: 'format-pre'
  }
});";


        //   echo "Swal.fire('GUAY!','Reto entregado correctamente! Archivos subidos: ".$nArchivos."(".$sListaArchivos.")' ,'success');";
        }
        
    }
    ?>
}
    function submitOk()
    {
  
        Swal.fire({
        title: 'Datos correctos',
        text: '¿Son los datos correctos?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí'
      }).then((result) => {
        if (result.value) {
          document.getElementById("regform").submit();
        }
      })

    }

        
</script>
</head>

<body onload="init()">


						<h2 class="text-center text-bold mt-2x">Entregar Reto <?php echo $nombreReto?></h2>
                        <h4 class="text-center text-bold mt-2x"><?php echo " [Fecha límite de entrega: ".$filaTarea['FECHA_LIMITE']."]"?><br/>[Tamaño límite de la entrega: 20MB]<br/>(Vas a hacer la entrega número <?php echo $numeroEntregas.(($entregaFueraTiempo)?"<b style='color:red'> [entrega fuera de tiempo por ".$sDiferenciaFueraTiempo."] </b>":"")?>)</h4>
                        <div class="hr-dashed"></div>
						<div class="well row pt-2x pb-3x bk-light text-center">
                         <form method="post" class="form-horizontal" enctype="multipart/form-data" name="regform" id="regform">
                                                         <div class="form-group">
    <input type='hidden' name='idt' id='idt' value='<?php echo $idTarea?>'/>           
                            <label class="col-sm-1 control-label">SUBIR FICHEROS</label>
                            <div class="col-sm-5">

<input type="file" name="archivo1" class="form-control">
<input type="file" name="archivo2" class="form-control">
<input type="file" name="archivo3" class="form-control">
<input type="file" name="archivo4" class="form-control">
<input type="file" name="archivo5" class="form-control">



                            
                            </div>
                            <label class="col-sm-1 control-label">COMENTARIO<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="text" name="comentt" class="form-control" maxlength = "499" required>
                            </div>
                            </div>

								<br>

                                <button class="btn btn-primary" name="submit" type="submit">Entregar Reto</button>

                                </form>
							</div>

<?php

$patronPreFix= $alumno['NOMBRE'].'_'.$alumno['APELLIDO1'].'_'.$alumno['APELLIDO2'];

$folder="retos/".getAsignaturasFromCurso($dbh,$alumno['ID_CURSO'])[0]['NOMBRE']."/".$nombreReto;
$files = glob($folder."/".$patronPreFix."*");
rsort($files);
$numEntregasInterador = $numeroEntregas-1;
if ($numEntregasInterador>0)
{
    $postIniFecha = strpos($files[0], "__")+2;
    $time = strtotime(substr($files[0], $postIniFecha, 19));
        $newformat = date('Y-m-d H:i:s',$time);
        $entregaFueraTiempo2= ($newformat>$filaTarea['FECHA_LIMITE']);
$sDiferenciaFueraTiempo2 = "";
if ($entregaFueraTiempo2)
{
    $sDiferenciaFueraTiempo2 = getDiferenciaDates($newformat,$filaTarea['FECHA_LIMITE']);
}
    echo "<b style='font-size:2vw'>ENTREGA VALIDA</b><br/>";
    echo "Entrega ".$numEntregasInterador."ª (última)".(($entregaFueraTiempo2)?"<b style='color:red'> [entrega fuera de tiempo por ".$sDiferenciaFueraTiempo2."] </b>":"")." <br/>";
}

$entregasAnteriores=false;
if ($numEntregasInterador<10)
{
    $numEntregasInterador="0".$numEntregasInterador;
}
$patronEntregas="_".$numEntregasInterador."__";
foreach ($files as $ficheroI) 
{
    
    $contienePatronF = strpos($ficheroI, $patronEntregas);
    if (!$contienePatronF)
    {
        $numEntregasInterador--;
        $textoNumEntregasInterador = $numEntregasInterador;
        if ($numEntregasInterador<10)
        {
            $numEntregasInterador="0".$numEntregasInterador;
        }
        $patronEntregas="_".$numEntregasInterador."__";
        $postIniFecha = strpos($ficheroI, "__")+2;
       
        $time = strtotime(substr($ficheroI, $postIniFecha, 19));
        $newformat = date('Y-m-d H:i:s',$time);
        $entregaFueraTiempo2= ($newformat>$filaTarea['FECHA_LIMITE']);
        $sDiferenciaFueraTiempo2 = "";
        if ($entregaFueraTiempo2)
        {
            $sDiferenciaFueraTiempo2 = getDiferenciaDates($newformat,$filaTarea['FECHA_LIMITE']);
        }
        if (!$entregasAnteriores)
        {
            echo "<b style='font-size:2vw' >ENTREGAS ANTERIORES</b><br/>";
            $entregasAnteriores=true;
        }
        //var_export($newformat);
        echo "Entrega ".$textoNumEntregasInterador."ª".(($entregaFueraTiempo2)?"<b style='color:red'> [entrega fuera de tiempo por ".$sDiferenciaFueraTiempo2."] </b>":"")." <br/>";
    }

    echo '<a href="'.$ficheroI.'" target="_blank" rel="noopener">'.basename($ficheroI).'</a><br/>';
  
}
//var_export($files);
?>
</body>
</html>