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
  $totalestrellas=$_POST['totalestrellas'];
  $descrip=$_POST['descrip'];
  $selSitios=$_POST['selSitios'];
  $visible=$_POST['visible'];
  $fechalimite=$_POST['fechalimite'];
  $linkdocumento=$_POST['linkdocumento'];
  $posx=$_POST['posx'];
  $posy=$_POST['posy'];
  $haySitio=true;
  $sExamen = $_POST['sexamen'];
  if ($selSitios=='')
  {
    $selSitios=NULL;
    $posx=NULL;
    $posy=NULL;
  }
  else if ($posx=='' || $posy=='')
  {
    // se genera lugar aleatorio para el evento del reto
    $filaSitio = getSitioFromID($dbh,$selSitios);
    $posx = rand($filaSitio['INI_X'],$filaSitio['MAX_X']);
    $posy = rand($filaSitio['INI_Y'],$filaSitio['MAX_Y']);
    $cont = 1000;
    while (existeLugar($dbh,$filaSitio['ID'],$posx,$posy)) 
    {
        $posx = rand($filaSitio['INI_X'],$filaSitio['MAX_X']);
        $posy = rand($filaSitio['INI_Y'],$filaSitio['MAX_Y']);
        $cont--;
        if ($cont == 0)
        {
            $haySitio=false;
            break;
        }
    }
  }
  if ($linkdocumento=='')
  {
    $linkdocumento=NULL;
  }
  if ($fechalimite=='')
  {
    $fechalimite=NULL;
  }

if ($haySitio)
{
  $asignaturas = getAsignaturasFromCurso($dbh,$sel11);
  
  insertarReto($dbh,$asignaturas[0]['ID'],$name,$totalestrellas,$descrip,$selSitios,$posx,$posy,$linkdocumento,$fechalimite,$visible,$sExamen);
  
    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {
        $curso = getCursoFromCursoID($dbh,$sel11);
        $aAlumnosCurso = getAlumnosGradoNivel($dbh,$curso['GRADO'],$curso['NIVEL']);
        foreach ($aAlumnosCurso as $alumno) 
        {
            insertarAlumnoTarea($dbh,$alumno['ID'],$lastInsertId,"no activado", NULL, NULL);
        }
        // notificación general de creación de reto a la clase
        $clase = getAsignaturasFromCurso($dbh,$curso['ID'])[0]['NOMBRE'];
        $mensaje = "Se ha creado un nuevo reto de " .$totalestrellas. " estrellas llamado ".$name.", ¡A por él!";
        
        mandarNotificacion($dbh,'Admin',$clase,$mensaje);
        echo "<script type='text/javascript'>alert('Reto creado correctamente!');</script>";
    }
    else
    {
        echo "<script type='text/javascript'>alert('Error al crear el reto.');</script>";        
    }

}
else
{
    echo "<script type='text/javascript'>alert('Error, no hay hueco en ese sitio, prueba con otro');</script>";        
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

<body>
        <?php include('includes/header.php');?>
    <div class="ts-main-content">
    <?php include('includes/leftbar.php');?>
        <div class="content-wrapper">


						<h1 class="text-center text-bold mt-2x">Crear Reto</h1>
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
                            <input type="text" placeholder="(se quitarán las tildes)" name="name" class="form-control" required>
                            </div>
                            </div>
                            <div class="form-group">
                            


                            <label class="col-sm-1 control-label">TOTAL ESTRELLAS<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="text" name="totalestrellas" class="form-control" required>
                            </div>

                            <label class="col-sm-1 control-label">DESCRIPCIÓN<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="text" maxlength = "499" name="descrip" class="form-control" required>
                            </div>
                            </div>

                            <div class="form-group">
                            
                            <label class="col-sm-1 control-label">LINK DOCUMENTO</label>
                            <div class="col-sm-5">
                            <input type="text" name="linkdocumento" class="form-control">
                            </div>
                            <label class="col-sm-1 control-label">FECHA LÍMITE</label>
                                <div class='col-sm-5'>
                                    <input type='text' name="fechalimite" class="form-control" id='datetimepicker4' />
                                </div>
                                <script type="text/javascript">
                                    $(function () {
                        var now = new Date();
                var dateNow1Week = now.setDate(now.getDate() +7 );
                                        $('#datetimepicker4').datetimepicker({
                                            format: 'YYYY-MM-DD HH:mm:ss',defaultDate:moment(dateNow1Week).hours(23).minutes(59).seconds(59).milliseconds(0)
                                        });                                       
                                    });

                                </script>                            
                            </div>
        <div class="form-group">
                            
                                                        <label class="col-sm-1 control-label">SITIO<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                                
                                <select class="form-control" ID="sel11" name="selSitios"> 
        <?php
        $listaSitios= getSitiosVisibles($dbh); 
        foreach ($listaSitios as $sitio)
        {
            $pos = strrpos($sitio, "--");
            $IDAs=substr($sitio,$pos+2,strlen($sitio));
            $nombre = substr($sitio,0,$pos);
            //$posAs= strrpos($nombre,"*");
            //$nombre_sitio = substr($nombre,0,$posAs);
            //echo 'idas:'.$IDAs;
            echo "<option value='".$IDAs."'>".$nombre."</option>";
        }
        ?>
    </select>
                
                            </div>
                                                        <label class="col-sm-1 control-label">VISIBLE<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                                
                                <select class="form-control" ID="sel11" name="visible">
        <option value="1">VISIBLE</option>
        <option value="0">INVISIBLE</option>
    </select>
                
                            </div>

                         
                             

                            
                            </div>

                             <div class="form-group">
                           
                            <label class="col-sm-1 control-label">POSICIÓN X</label>
                            <div class="col-sm-5">
                            <input type="text" name="posx" class="form-control">
                            </div>
                            <label class="col-sm-1 control-label">POSICIÓN Y</label>
                            <div class="col-sm-5">
                            <input type="text" name="posy" class="form-control">
                            </div>



                            </div>

  <p> Si POSICIÓN X o POSICIÓN Y se deja vacío genera un lugar aletorio (x,y).</p>
  <p> Para modificar el sprite/icono/imagen tocar directamente en DB. Por dedecto es: !Flame, 7</p>

                             <div class="form-group">
                           
<label class="col-sm-1 control-label">EXAMEN<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                                
<select class="form-control" ID="sexamen" name="sexamen">
    <option value="0" selected="selected">NO</option>
    <option value="1">SI</option>
</select>
                
                            </div>



                            </div>

								<br>

                                <button class="btn btn-primary" name="submit" type="submit">Crear Reto</button>

                                </form>
							</div>

        </div>
    </div>
	

</body>
</html>