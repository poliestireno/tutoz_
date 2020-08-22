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
//var_export($_POST);
$idTarea = (isset($_GET['idt']))?$_GET['idt']:$_POST['idt'];
$ok=false;
$nArchivos=0;
$nombreReto = getTareaFromID($dbh,$idTarea)['NOMBRE'];
if(isset($_POST['comentt']))
{
    $datosAT = getDatosAlumnoTarea($dbh,$_SESSION['alogin'],$idTarea);
    $alumno = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);
    if ($datosAT['ESTADO']!='no activado')
    {
        $folder="retos/".getAsignaturasFromCurso($dbh,$alumno['ID_CURSO'])[0]['NOMBRE']."/".$nombreReto;
        //var_export($folder);
        if (!file_exists($folder)) {
            mkdir($folder, 0777,true);
        }

        for ($i=1; $i < 6; $i++) 
        { 
            if ($_FILES['archivo'.$i]['name']!='')
            {
                $file = $_FILES['archivo'.$i]['name'];
                if(move_uploaded_file($_FILES['archivo'.$i]['tmp_name'],$folder."/".$alumno['NOMBRE'].'_'.$alumno['APELLIDO1'].'_'.$alumno['APELLIDO2'].'__'.$file))
                {
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
        if ($ok)
        {
modificarEstadoReto($dbh,$_SESSION['alogin'],$idTarea,"entregado");
modificarFechaEntregadoReto($dbh,$_SESSION['alogin'],$idTarea,date("Y-m-d H:i:s"));
modificarComentarioReto($dbh,$_SESSION['alogin'],$idTarea,$_POST['comentt']);
modificarOtrosReto($dbh,$_SESSION['alogin'],$idTarea,"Archivos subidos:".$nArchivos);

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
            echo "Swal.fire('GUAY!','Reto entregado correctamente!(Archivos subidos: ".$nArchivos.")' ,'success');";
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


						<h1 class="text-center text-bold mt-2x">Entregar Reto <?php echo $nombreReto?></h1>
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
 
La entrega valida será la última realizada.

								<br>

                                <button class="btn btn-primary" name="submit" type="submit">Entregar Reto</button>

                                </form>
							</div>
	

</body>
</html>