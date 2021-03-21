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
?>
    <?php
//var_export($_POST);
function quitar_tildes($cadena) {
$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
$texto = str_replace($no_permitidas, $permitidas ,$cadena);
return $texto;
}

if(isset($_POST['sel11']))
{
  $sel11=$_POST['sel11'];
  $name=quitar_tildes($_POST['name']);
  $descrip=$_POST['descrip'];
  $asignaturas = getAsignaturasFromCurso($dbh,$sel11);
  $IdAsignatura =$asignaturas[0]['ID'];
  modificarDesactivarFastests($dbh,$IdAsignatura);
  insertarFastest($dbh,$IdAsignatura,$name,$descrip);
  
    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {

        echo "<script type='text/javascript'>alert('Test rápido creado correctamente!');</script>";
    }
    else
    {
        echo "<script type='text/javascript'>alert('Error al crear el test rápido.');</script>";        
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
    <script type="text/javascript">

    

        
</script>
</head>

<body>
        <?php include('includes/header.php');?>
    <div class="ts-main-content">
    <?php include('includes/leftbar.php');?>
        <div class="content-wrapper">


						<h1 class="text-center text-bold mt-2x">Crear Test Rápido</h1>
                        <div class="hr-dashed"></div>
						<div class="well row pt-2x pb-3x bk-light text-center">
                         <form method="post" class="form-horizontal" enctype="multipart/form-data" name="regform" id="regform">
                                                         <div class="form-group">
                            
                            <label class="col-sm-1 control-label">CURSO<span style="color:red">*</span></label>
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
                            <label class="col-sm-1 control-label">NOMBRE<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="text" placeholder="(se quitarán las tildes)" name="name" value="FASTEST_CLASE_<?php echo count (getFastestsComoClase($dbh))?>" class="form-control" required>
                            </div>
                            </div>
                            <div class="form-group">
                            

                            <label class="col-sm-1 control-label">DESCRIPCIÓN<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="text" maxlength = "499" name="descrip" class="form-control" value="TEST RÁPIDO PARA ..." required>
                            </div>
                            </div>

 


								<br>

                                <button class="btn btn-primary" name="submit" type="submit">Crear Test Rápido</button>

                                </form>
							</div>

        </div>
    </div>
	

</body>
</html>