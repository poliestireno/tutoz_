<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$sql = "SELECT username from admin;";
		$query = $dbh -> prepare($sql);
		$query->execute();
		$result=$query->fetch(PDO::FETCH_OBJ);
$msg="";
if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))

	{	
header('location:index.php');
}
else{

if(isset($_GET['edit']))
	{
		$editID=$_GET['edit'];
	}

//var_dump($_POST);
	
if(isset($_POST['submit']))
  {
	$file = $_FILES['image']['name'];
	$file_loc = $_FILES['image']['tmp_name'];
	$folder="../images/";
	$new_file_name = strtolower($file);
	$final_file=str_replace(' ','-',$new_file_name);
	
	$name=$_POST['name'];
	$CORREO=$_POST['CORREO'];
	$curso=$_POST['sel11'];
	//$pos = strrpos($curso, "--");
	//$IDAs=substr($curso,$pos+2,strlen($curso));
	//$curso = getCursoFromAsignaturaID($dbh,$IDAs)['ID'];
	$APELLIDO1no=$_POST['APELLIDO1no'];
	$APELLIDO2=$_POST['APELLIDO2'];
	$IDedit=$_POST['IDedit'];
	$image=$_POST['image'];

	if(move_uploaded_file($file_loc,$folder.$final_file))
		{
			$image=$final_file;
		}
		
	$sql="UPDATE ALUMNOS SET NOMBRE=(:name), CORREO=(:CORREO), ID_CURSO=(:curso), APELLIDO1=(:APELLIDO1no), APELLIDO2=(:APELLIDO2), Image=(:image) WHERE ID=(:IDedit)";
	$query = $dbh->prepare($sql);
	$query-> bindParam(':name', $name, PDO::PARAM_STR);
	$query-> bindParam(':CORREO', $CORREO, PDO::PARAM_STR);
	$query-> bindParam(':curso', $curso, PDO::PARAM_STR);
	$query-> bindParam(':APELLIDO1no', $APELLIDO1no, PDO::PARAM_STR);
	$query-> bindParam(':APELLIDO2', $APELLIDO2, PDO::PARAM_STR);
	$query-> bindParam(':image', $image, PDO::PARAM_STR);
	$query-> bindParam(':IDedit', $IDedit, PDO::PARAM_STR);
	$query->execute();
	$msg="Information Updated Successfully";
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
	
	<title>Edit User</title>

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
<?php
		$sql = "SELECT * from ALUMNOS where ID = :editID";
		$query = $dbh -> prepare($sql);
		$query->bindParam(':editID',$editID,PDO::PARAM_INT);
		$query->execute();
		$result=$query->fetch(PDO::FETCH_OBJ);
		$cnt=1;	
?>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<h3 class="page-title">Edit User : <?php echo htmlentities($result->NOMBRE); ?></h3>
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">Edit Info</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong>:<?php echo htmlentities($msg); ?> </div><?php }?>

									<div class="panel-body">
<form method="post" class="form-horizontal" enctype="multipart/form-data" name="imgform">
<div class="form-group">
<label class="col-sm-2 control-label">Name<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="name" class="form-control" required value="<?php echo htmlentities($result->NOMBRE);?>">
</div>
<label class="col-sm-2 control-label">CORREO<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="CORREO" name="CORREO" class="form-control" required value="<?php echo htmlentities($result->CORREO);?>">
</div>
</div>

<div class="form-group">


<label class="col-sm-2 control-label">APELLIDO1<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="APELLIDO1no" class="form-control" required value="<?php echo htmlentities($result->APELLIDO1);?>">
</div>

<label class="col-sm-2 control-label">APELLIDO2<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="text" name="APELLIDO2" class="form-control" required value="<?php echo htmlentities($result->APELLIDO2);?>">
</div>
</div>


<div class="form-group">
<label class="col-sm-2 control-label">Image<span style="color:red">*</span></label>
<div class="col-sm-4">
<input type="file" name="image" class="form-control">
</div>

<label class="col-sm-2 control-label">Curso<span style="color:red">*</span></label>
<div class="col-sm-4">
<select class="form-control" ID="sel11" name="sel11">
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


	$curso = getCursoFromAsignaturaID($dbh,$IDAs)['ID'];


          if($result->ID_CURSO==$curso)
            echo "<option value='".$curso."' selected='selected'>".$nombre_curso."</option>";
          else
            echo "<option value='".$curso."'>".$nombre_curso."</option>";
        }
        ?>
    </select>
</div>
</div>

<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2">
		<img src="../images/<?php echo htmlentities($result->image);?>" width="150px"/>
		<input type="hIDden" name="image" value="<?php echo htmlentities($result->image);?>" >
		<input type="hIDden" name="IDedit" value="<?php echo htmlentities($result->ID);?>" >
</div>
</div>


<div class="form-group">
	<div class="col-sm-8 col-sm-offset-2">
		<button class="btn btn-primary" name="submit" type="submit">Save Changes</button>
	</div>
</div>

</form>
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
	<script type="text/javascript">
				 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 3000);
					});
	</script>

</body>
</html>
<?php } ?>