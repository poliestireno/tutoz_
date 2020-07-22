<?php
include('includes/config.php');
require_once("UTILS/dbutils.php");
if(isset($_POST['submit']))
{

$CORREO=$_POST['CORREO'];
if (!(existeCorreo($dbh,$CORREO)))
{
    $file = $_FILES['image']['name'];
    $file_loc = $_FILES['image']['tmp_name'];
    $folder="images/"; 
    $new_file_name = strtolower($file);
    $final_file=str_replace(' ','-',$new_file_name);
    $name=$_POST['name'];
    $password=md5($_POST['password']);
    $curso=$_POST['sel11'];
    $APELLIDO1no=$_POST['APELLIDO1no'];
    $APELLIDO2=$_POST['APELLIDO2'];
    $gender=$_POST['gender'];
    //var_dump($_POST);
    if(move_uploaded_file($file_loc,$folder.$final_file))
    {
    	$image=$final_file;
    }
    $notitype='Cuenta creada: '.$_POST['name']." ".$_POST['APELLIDO1no']." ".$_POST['APELLIDO2'];

    // se notifica al admin el registro de nuevo usuario
    mandarNotificacion($dbh,$CORREO,'Admin',$notitype);

    // se inserta su bot inicial
    insertarBot($dbh,"HOLA","",0,"",1,2,9,0,0,0);
    $lastInsertIdBot = $dbh->lastInsertId();

    $sql ="INSERT INTO ALUMNOS(NOMBRE,CORREO, password, gender, APELLIDO1, APELLIDO2, image,ORDEN_ALBUM,ORDEN_COMBOS,ORDEN_CREADORES,ORDEN_REFERENCIAS_TOTAL,NUMERO_NIVEL, ID_MIBOT,ID_CURSO) VALUES(:name, :CORREO, :password, :gender, :APELLIDO1no, :APELLIDO2, :image,'','','','',1, :IDMiBot,:IDCurso)";
    $query= $dbh -> prepare($sql);
    $query-> bindParam(':name', $name, PDO::PARAM_STR);
    $query-> bindParam(':CORREO', $CORREO, PDO::PARAM_STR);
    $query-> bindParam(':gender', $gender, PDO::PARAM_STR);
    $query-> bindParam(':password', $password, PDO::PARAM_STR);
    $query-> bindParam(':APELLIDO1no', $APELLIDO1no, PDO::PARAM_STR);
    $query-> bindParam(':APELLIDO2', $APELLIDO2, PDO::PARAM_STR);
    $query-> bindParam(':image', $image, PDO::PARAM_STR);
    $query-> bindParam(':IDMiBot', $lastInsertIdBot, PDO::PARAM_STR);
    $query-> bindParam(':IDCurso', $curso, PDO::PARAM_STR);
    $query->execute();
    $lastInsertId = $dbh->lastInsertId();

    // insertamos cromos del alumno y le damos los cromos iniciales

    $nCromosIni = getAdminCromos($dbh)['N_CROMOS_INI'];
    $setId = getSetCromosIdFromAlumno($dbh,$CORREO);
    $nCromosPropios = getAdminCromos($dbh)['N_CROMOS_PROPIOS'];
    $contPropios=0;

    $aRandomPropios = array();
    for ($i=0; $i < $nCromosIni; $i++) { 
        $aRandomPropios[$i]=$i;
    }
    shuffle($aRandomPropios);
    $aRandomPropios2 = array();
    for ($i=0; $i < $nCromosPropios; $i++) { 
        $aRandomPropios2[$i]=$aRandomPropios[$i];
    }
    
    for ($i=0; $i < $nCromosIni; $i++) 
    { 
        if (in_array($i, $aRandomPropios2)) 
        {
            $ID_POSEEDOR=$lastInsertId;
            $GENERADO=1;
        }
        else
        {
            $ID_POSEEDOR=NULL;
            $GENERADO=0;
        }

        $ID_CREADOR=$lastInsertId;        
        $name="REEMPLAZAR_NOMBRE";
        $color="White";
        $mana_w=1;
        $picture="";
        $cardtype="";
        $rarity="Common";
        $cardtext="";
        $power=$i+1;
        $toughness=$nCromosIni;
        $artist="REEMPLAZAR_ARTISTA";
        $bottom=$_POST['name']. " " . $APELLIDO1no . " " .$APELLIDO2;
        insertarCromo($dbh,$ID_CREADOR,$ID_POSEEDOR,$GENERADO,$setId, $name, $color, $mana_w, $picture, $cardtype, $rarity, $cardtext, $power, $toughness, $artist, $bottom);
    }
    

    // notificamos los cromos otorgados de inicio al alumno

    $notitype='Empiezas tu álbum con '.$nCromosPropios.' cromos tuyos';
    mandarNotificacion($dbh,'Admin',$CORREO,$notitype);

    // notificamos números de cromos al alumno

    $notitype='Creados '.$nCromosIni.' cromos tuyos que salen a mercado';
    mandarNotificacion($dbh,'Admin',$CORREO,$notitype);
    
    $lastInsertId = $dbh->lastInsertId();
    if($lastInsertId)
    {
    echo "<script type='text/javascript'>alert('Te has registrado correctamente!');</script>";
    echo "<script type='text/javascript'> document.location = 'index.php'; </script>";
    }
    else 
    {
    $error="Something went wrong. Please try again";
    }

    }
    else
{
    echo "<script type='text/javascript'>alert('El correo ya existe en el sistema, registrate con otro');</script>";
    echo "<script type='text/javascript'> document.location = 'register.php'; </script>";

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
    <script type="text/javascript">

	function valIDate()
        {
            var extensions = new Array("jpg","jpeg");
            var image_file = document.regform.image.value;
            var image_length = document.regform.image.value.length;
            var pos = image_file.lastIndexOf('.') + 1;
            var ext = image_file.substring(pos, image_length);
            var final_ext = ext.toLowerCase();
            for (i = 0; i < extensions.length; i++)
            {
                if(extensions[i] == final_ext)
                {
                return true;
                
                }
            }
            alert("Extensión del fichero de la imagen no valida (Utilizar jpg o jpeg)");
            return false;
        }
        
</script>
</head>

<body>
	<div class="login-page bk-img">
		<div class="form-content">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<h1 class="text-center text-bold mt-2x">Registro</h1>
                        <div class="hr-dashed"></div>
						<div class="well row pt-2x pb-3x bk-light text-center">
                         <form method="post" class="form-horizontal" enctype="multipart/form-data" name="regform" onSubmit="return valIDate();">
                            <div class="form-group">
                            <label class="col-sm-1 control-label">NOMBRE<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="text" name="name" class="form-control" required>
                            </div>
                            <label class="col-sm-1 control-label">CORREO<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="email" name="CORREO" class="form-control" required>
                            </div>
                            </div>

                            <div class="form-group">
                            
                            <label class="col-sm-1 control-label">APELLIDO1<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="text" name="APELLIDO1no" class="form-control" required>
                            </div>
                           
                             

                            <label class="col-sm-1 control-label">CONTRASEÑA<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="password" name="password" class="form-control" ID="password" required >
                            </div>
                            </div>

                             <div class="form-group">
                           
                            <label class="col-sm-1 control-label">APELLIDO2<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <input type="text" name="APELLIDO2" class="form-control" required>
                            </div>

                            <label class="col-sm-1 control-label">GENERO<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                                <select class="form-control" ID="gender1" name="gender" required>
        <option></option>
        <option value='m'>MASCULINO</option>
        <option value='f' >FEMENINO</option>
    </select>
                            
                            </div>


                            </div>

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
                            <label class="col-sm-1 control-label">IMAGEN<span style="color:red">*</span></label>
                            <div class="col-sm-5">
                            <div><input type="file" name="image" class="form-control"></div>
                            </div>
                            </div>

								<br>
                                <button class="btn btn-primary" name="submit" type="submit">Registrar</button>
                                </form>
                                <br>
                                <br>
								<p>¿Ya tienes cuenta? <a href="index.php" >Entra aquí</a></p>
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

</body>
</html>