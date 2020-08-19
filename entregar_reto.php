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

    /*
    if($filasModificadas>0)
    {

//echo "<script type='text/javascript'>Swal.fire('GUAY!','Reto entregado correctamente!' ,'success'); </script>";
        echo "<script type='text/javascript'>alert('Reto entregado correctamente!'); </script>";
    }
    else
    {
        echo "<script type='text/javascript'>alert('Error al entregar el reto.');</script>"; 
    }
  */  



?>



    <script type="text/javascript">

function init()
{
    <?php

    if(isset($_POST['sel11']))
    {
        $datosAT = getDatosAlumnoTarea($dbh,$_SESSION['alogin'],$idTarea);
        if ($datosAT['ESTADO']=='no activado')
        {
            echo "Swal.fire('UFFF!','Para poder entregar antes hay que activar el reto...' ,'warning');";
        }
        else
        {
            $filasModificadas = modificarEstadoReto($dbh,$_SESSION['alogin'],$idTarea,"entregado");
            $filasModificadas = modificarFechaEntregadoReto($dbh,$_SESSION['alogin'],$idTarea,date("Y-m-d H:i:s"));
            
            echo "Swal.fire('GUAY!','Reto entregado correctamente!' ,'success');";
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


						<h1 class="text-center text-bold mt-2x">Entregar Reto</h1>
                        <div class="hr-dashed"></div>
						<div class="well row pt-2x pb-3x bk-light text-center">
                         <form method="post" class="form-horizontal" enctype="multipart/form-data" name="regform" id="regform">
                                                         <div class="form-group">
    <input type='hidden' name='idt' id='idt' value='<?php echo $idTarea?>'/>           
                            <label class="col-sm-1 control-label">SUBIR FICHEROS<span style="color:red">*</span></label>
                            <div class="col-sm-5">

                                <select class="form-control" ID="sel11" name="sel11" required>
        <option></option>
        <?php
        $listaCursos= getAsignaturasConCurso($dbh);
        foreach ($listaCursos as $curso)
        {
            $pos = strrpos($curso, "--");
            $IDAs=substr($curso,$pos+2,strlen($curso));
            $nombre = substr($curso,0,$pos);
            $posAs= strrpos($nombre,"*");
            $nombre_curso = substr($nombre,0,$posAs);
            //echo 'idas:'.$IDAs;
            $curso = getCursoFromAsignaturaID($dbh,$IDAs)['ID'];
            echo "<option value='".$curso."'>".$nombre_curso."</option>";
        }
        ?>
    </select>



                            
                            </div>
                            <label class="col-sm-1 control-label">COMENTARIO<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="text" name="name" class="form-control" required>
                            </div>
                            </div>
 
La entrega valida será la última entrega.

								<br>

                                <button class="btn btn-primary" name="submit" type="submit">Entregar Reto</button>

                                </form>
							</div>
	

</body>
</html>