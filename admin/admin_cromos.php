<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";
//var_export($_POST);
try
  {
$sql = "SELECT username from admin;";
		$query = $dbh -> prepare($sql);
		$query->execute();
		$result=$query->fetch(PDO::FETCH_OBJ);

if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))
	{	
header('location:index.php');
}
else{
if (isset($_POST['filtro'])){
  $vectorAlumnos = buscarAlumnos($dbh, $_POST['filtro']);
}
$codigoEncriptado ='';
if (isset($_POST['alumnoCodSobre'])&&($_POST['alumnoCodSobre']!=''))
{
  $setId = getSetCromosIdFromAlumno($dbh,$_POST['alumnoCodSobre']);
  $cromoGenerado = getAndSetRandomCromo($dbh,$setId);
  if ($cromoGenerado!=NULL)
  {
  	$message = $cromoGenerado['ID'].",".$_POST['alumnoCodSobre'];
    //echo "message:".$message;
  	$codigoEncriptado = openssl_encrypt ($message,"AES-128-ECB","kgYYBOihH8/(ggG/)gKGB8/biLJLDJOIUD/(%&/UG(DF(/F%&(IGDF%(F)HFG=FD:_V:F_VBLVP?F=F)FKIF)))");
    //echo "codigo:".$codigoEncriptado;
  	
    // notificamos al alumno el código de su sobre

    mandarNotificacion($dbh,'Admin',$_POST['alumnoCodSobre'],' Código del sobre conseguido: '.$codigoEncriptado);
      
    // log asignación de cromo
    $message ="Asignado a ".$_POST['alumnoCodSobre']." el código de sobre ".$codigoEncriptado." y cromo:".var_export($cromoGenerado, true);
    mi_info_log($message);
 	  
  }
  else
  {
  	$codigoEncriptado ='ERROR: Cromos no disponibles!!';
  }

}
if (isset($_POST['alumnoTareaOk'])&&($_POST['alumnoTareaOk']!=''))
{
  modificarEstadoReto($dbh,$_POST['alumnoTareaOk'],$_POST['tareaidselect'],'corregido');
  modificarEstrellasConseguidasReto($dbh,$_POST['alumnoTareaOk'],$_POST['tareaidselect'],$_POST['estrellasconseguidas']);

  $msg=" Actualizada tarea para el alumno";
}

if(isset($_POST['submit']))
{	

	//var_dump($_POST);
	//$CORREO=$_POST['CORREO'];
	//$sql="UPDATE admin SET username=(:name), email=(:CORREO)";
	$sql="UPDATE ADMIN_CROMOS SET N_CROMOS_INI=(:n_cromos_iniciales), N_CROMOS_PROPIOS=(:n_cromos_propios), NUM_SLOTS= (:nslots) ,  PAREJA=(:valorpareja),  DOBLEPAREJA=(:valordoblepareja),  TRIO=(:valortrio), CUARTETO=(:valorcuarteto),  ESCALERA3=(:valorescalera3),  ESCALERA4=(:valorescalera4),ESCALERASIMPLE3=(:valorescalerasimple3),  ESCALERASIMPLE4=(:valorescalerasimple4),  ESCALERA3_ESTRELLAS=(:valorescalera3_estrellas),  ESCALERA4_ESTRELLAS=(:valorescalera4_estrellas) ";
	$query = $dbh->prepare($sql);
	$query-> bindParam(':n_cromos_iniciales', $_POST['n_cromos_iniciales'], PDO::PARAM_STR);
	$query-> bindParam(':n_cromos_propios', $_POST['n_cromos_propios'], PDO::PARAM_STR);
	$query-> bindParam(':nslots', $_POST['NUM_SLOTS'], PDO::PARAM_STR);

	$query-> bindParam(':valorpareja', $_POST['valorpareja'], PDO::PARAM_STR);
	$query-> bindParam(':valordoblepareja', $_POST['valordoblepareja'], PDO::PARAM_STR);
	$query-> bindParam(':valortrio', $_POST['valortrio'], PDO::PARAM_STR);
	$query-> bindParam(':valorcuarteto', $_POST['valorcuarteto'], PDO::PARAM_STR);
	$query-> bindParam(':valorescalerasimple3', $_POST['valorescalerasimple3'], PDO::PARAM_STR);
  $query-> bindParam(':valorescalerasimple4', $_POST['valorescalerasimple4'], PDO::PARAM_STR);
  $query-> bindParam(':valorescalera3', $_POST['valorescalera3'], PDO::PARAM_STR);
  $query-> bindParam(':valorescalera4', $_POST['valorescalera4'], PDO::PARAM_STR);
  $query-> bindParam(':valorescalera3_estrellas', $_POST['valorescalera3_estrellas'], PDO::PARAM_STR);
  $query-> bindParam(':valorescalera4_estrellas', $_POST['valorescalera4_estrellas'], PDO::PARAM_STR);
  $query->execute();
	$msg="Información actualizada correctamente";
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
	
	<title>Gestionar Cromos</title>

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
<script type="text/javascript">
function managebuttonGO(nombre,curso,correo) 
{
    document.getElementById("nombreGO").value=nombre;
    document.getElementById("cursoGO").value=curso;
    document.getElementById("correoGO").value=correo;
    document.getElementById("form2").action="admin_cromos.php";
    document.getElementById("form2").submit(); 

}
function managebuttonGO2(nombre,curso,correo) 
{  
       const { value: formValues } =  Swal.fire({
  title: '<span class="label label-primary">'+nombre+'</span>'+
        '<span class="label label-warning">Curso: '+curso+'</span>',
         showConfirmButton: false,
  html:
        '<h3><span class="label label-primary">[estado] Nombre Reto/Máximo estrellas:</span></h3>'+
        '<select  class="form-control" id="nombretarea" name="nombretarea">'+
        <?php 
        if (isset($_POST['nombreGO'])&&($_POST['nombreGO']!=''))
        {
            $tareasFA = getTareasFromAlumno($dbh,$_POST['correoGO']);
            
            //var_dump($tareasFA);
            foreach ($tareasFA as $tareai) {
              $ttareaa = getTareaFromID($dbh,$tareai['ID_TAREA']);
              echo "'<option value=\"".$tareai['ID_TAREA']."|".$tareai['ESTADO']."\">[".$tareai['ESTADO']."] ".$ttareaa['NOMBRE']."(".getAsignaturaFromAsignaturaID($dbh,$ttareaa['ID_ASIGNATURA'])['NOMBRE'].")/".$ttareaa['TOTAL_ESTRELLAS']."</option>'+";
            }
        }
        
        ?>
        '</select><br/>'+
        '<h3><span class="label label-primary">Estrellas conseguidas:</span></h3>'+
        '<select  class="form-control" id="estrellasconfirm" name="estrellasconfirm">'+
        <?php for ($i=0; $i < 100; $i++) { 
          echo "'<option value=\"".$i."\">".$i."</option>'+";
        }
        ?>
        '</select><br/>'+
        
        '<a onclick="genTareaOk(\''+correo+'\',document.getElementById(\'nombretarea\').value,document.getElementById(\'estrellasconfirm\').value)" class="btn btn-danger" >Tarea OK</a>'+
        '<a onclick="genCodigoSobre(\''+correo+'\')" class="btn btn-danger" >Sobre</a>'+
        '<a onclick="genTareaOKYSobre(\''+correo+'\',document.getElementById(\'nombretarea\').value,document.getElementById(\'estrellasconfirm\').value)" class="btn btn-danger" >Tarea OK + Sobre</a>'
        ,
        showCloseButton: true,
  focusConfirm: false,
  
})
}   

  function managebuttonB()
  {
    if (document.getElementById("filtro").value!='')
    {
      document.getElementById("form2").action="admin_cromos.php";
      document.getElementById("form2").submit(); 
    }
  }


  function genTareaOKYSobre(correo_alumno,tareaidselect,estrellasconseguidas)
  {
    var fields = tareaidselect.split('|');
    var tareaid = fields[0];
    var estado = fields[1];
    
    if (estado!='entregado')
    {
      Swal.fire({
          title: '¿Seguro que quieres corregirlo y darle sobre?',
          text: "No está en estado entregado",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, lo corrigo!'
        }).then((result) => {
          if (result.value) {
      document.getElementById("tareaidselect").value=tareaid;
    document.getElementById("estrellasconseguidas").value=estrellasconseguidas;
    document.getElementById("alumnoTareaOk").value=correo_alumno;
    document.getElementById("alumnoCodSobre").value=correo_alumno;
    document.getElementById("form2").action="admin_cromos.php";
    document.getElementById("form2").submit(); 
          }
        });
    }
    else
    {
      document.getElementById("tareaidselect").value=tareaid;
    document.getElementById("estrellasconseguidas").value=estrellasconseguidas;
    document.getElementById("alumnoTareaOk").value=correo_alumno;
    document.getElementById("alumnoCodSobre").value=correo_alumno;
    document.getElementById("form2").action="admin_cromos.php";
    document.getElementById("form2").submit(); 
    }
  }
  function genTareaOk(correo_alumno,tareaidselect,estrellasconseguidas)
  {
    var fields = tareaidselect.split('|');
    var tareaid = fields[0];
    var estado = fields[1];

    if (estado!='entregado')
    {
      Swal.fire({
          title: '¿Seguro que quieres corregirlo?',
          text: "No está en estado entregado",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, lo corrigo!'
        }).then((result) => {
          if (result.value) {
document.getElementById("tareaidselect").value=tareaid;
    document.getElementById("estrellasconseguidas").value=estrellasconseguidas;
    document.getElementById("alumnoTareaOk").value=correo_alumno;
    document.getElementById("form2").action="admin_cromos.php";
    document.getElementById("form2").submit(); 
          }
        });
    }
    else
    {
       document.getElementById("tareaidselect").value=tareaid;
      document.getElementById("estrellasconseguidas").value=estrellasconseguidas;
      document.getElementById("alumnoTareaOk").value=correo_alumno;
      document.getElementById("form2").action="admin_cromos.php";
      document.getElementById("form2").submit();      
    }
  }
  function genCodigoSobre(correo_alumno)
  {
    document.getElementById("alumnoCodSobre").value=correo_alumno;
    document.getElementById("form2").action="admin_cromos.php";
    document.getElementById("form2").submit(); 
  }
    function copyToClipboard(e) {
    var tempItem = document.createElement('input');

    tempItem.setAttribute('type','text');
    tempItem.setAttribute('display','none');
    
    let content = e;
    if (e instanceof HTMLElement) {
    		content = e.innerHTML;
    }
    
    tempItem.setAttribute('value',content);
    document.body.appendChild(tempItem);
    
    tempItem.select();
    document.execCommand('Copy');

    tempItem.parentElement.removeChild(tempItem);
}
</script>
</head>

<body <?php 
if (isset($_POST['correoGO'])&&($_POST['correoGO']!=''))
{
  echo ' onload = "managebuttonGO2(\''.$_POST['nombreGO'].'\',\''.$_POST['cursoGO'].'\',\''.$_POST['correoGO'].'\')"';
}
?>>
<?php
		$sql = "SELECT * from ADMIN_CROMOS LIMIT 1;";
		$query = $dbh -> prepare($sql);
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
						<h3 class="page-title">Gestión Cromos</h3>
						<div class="row">

                <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading">TAREAS Y CROMOS</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

  <form id="form2" method="post" action="admin_cromos.php">
      <input type='hidden' name='alumnoTareaOk' id='alumnoTareaOk'/>
      <input type='hidden' name='alumnoCodSobre' id='alumnoCodSobre'/>
      <input type='hidden' name='nombreGO' id='nombreGO'/>
      <input type='hidden' name='cursoGO' id='cursoGO'/>
      <input type='hidden' name='correoGO' id='correoGO'/>
      <input type='hidden' name='tareaidselect' id='tareaidselect'/>
      <input type='hidden' name='estrellasconseguidas' id='estrellasconseguidas'/>
    
    <br/>
    <div class="form-row">
    <div class="form-group col-md-4">
      <input type="text" class="form-control" id="filtro" name = "filtro" placeholder="nombre/apellido/curso" value ="<?php echo  (!isset($_POST['filtro']))?'':$_POST['filtro']?>">
    </div>
    <div class="form-group col-md-4">
      <a onclick="managebuttonB()"  class="btn btn-danger btn-outline btn-wrap-text">Buscar</a>
    </div>
  </div>
    <br/><br/><br/>
    <?php 
      if (isset($_POST['filtro'])){
        //var_dump($vectorAlumnos);
          echo '<br/>';

          foreach ($vectorAlumnos as $alumno)
          {
          echo '<div class="form-row">';
          echo '<div class="form-group col-md-6">';
          
              echo '<a id="a1" onclick="managebuttonGO(\''.$alumno['nCompleto'].'\',\''.getNombreCursoFromAlumno($dbh, $alumno['ID']).'\',\''.$alumno['CORREO'].'\')" class="label label-danger"> GO </a>';
              //echo '<a id="a1" onclick="genCodigoSobre(\''.$alumno['CORREO'].'\')" class="label label-danger">Generar código sobre</a>';
              echo '<span class="label label-primary">'.$alumno['nCompleto'].'</span>';
              echo '<span class="label label-primary">'."Curso: ".getNombreCursoFromAlumno($dbh, $alumno['ID']).'</span>';
          echo '</div>';
          echo '</div>';
              
          }
      }
    ?>

  </form>

<div class="form-group col-md-10">
      <div class="form-group col-md-4">
         <input type="text" class="form-control" id="filtroc" name = "filtroc" value="<?php echo $codigoEncriptado?>">
      </div>
      <div class="form-group col-md-4">
        <a onclick="copyToClipboard(document.getElementById('filtroc').value)" class="btn btn-danger btn-outline btn-wrap-text">Copiar código</a>
      </div>
   </div>                 </div>
                </div>



              <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading">Info general cromos</div>

                  <div class="panel-body">
<?php
$setsCromos = getSetsCromos($dbh);
foreach ($setsCromos as $seti) {
  $cursosi = getCursosFromIDSet($dbh,$seti['ID']);
  $comma="";
  $sCursosLista="";
  foreach ($cursosi as $ci) { 
  
    $sCursosLista=$sCursosLista.$comma.getCursoFromCursoID($dbh,$ci['ID_CURSO'])['NOMBRE'];
    $comma=",";
  }
?>

<label>Set: <?php echo $seti['NOMBRE']?> Cursos: <?php echo $sCursosLista?></label>
<div class="form-group">
<label class="col-sm-2 control-label">Número totales cromos</label>
<div class="col-sm-4">
<input type="text" name="nctm" class="form-control" readonly="readonly" value="<?php echo htmlentities(getNumeroCromosTotalesFromIDSet($dbh,$seti['ID']));?>">
</div>

<label class="col-sm-2 control-label">Número cromos disponibles</label>
<div class="col-sm-4">
<input type="text" name="ncd" class="form-control" readonly="readonly" value="<?php echo htmlentities(getNumeroCromosDisponiblesFromIDSet($dbh,$seti['ID']));?>">
</div>

</div>
<div class="form-group">
<label class="col-sm-2 control-label">Número cromos abiertos</label>
<div class="col-sm-4">
<input type="text" name="nca" class="form-control" readonly="readonly" value="<?php echo htmlentities(getNumeroCromosAbiertosFromIDSet($dbh,$seti['ID']));?>">
</div>
<label class="col-sm-2 control-label">Número cromos sin abrir</label>
<div class="col-sm-4">
<input type="text" name="ncsa" class="form-control" readonly="readonly" value="<?php echo htmlentities(getNumeroCromosSinAbrirFromIDSet($dbh,$seti['ID']));?>">
</div>

</div>

<?php }?>
</div>





                  </div>
                </div>              
              <div class="col-md-12">
                <div class="panel panel-default">
                  <div class="panel-heading">configuración cromos</div>
<form method="post" class="form-horizontal" enctype="multipart/form-data">
                  <div class="panel-body">

<div class="form-group">
<label class="col-sm-2 control-label">Número cromos iniciales a mercado</label>
<div class="col-sm-4">
<input type="text" name="n_cromos_iniciales" class="form-control" required value="<?php echo htmlentities($result->N_CROMOS_INI);?>">
</div>


<label class="col-sm-2 control-label">número slots para combinaciones albúm</label>
<div class="col-sm-4">
<input type="text" name="NUM_SLOTS" class="form-control" required value="<?php echo htmlentities($result->NUM_SLOTS);?>">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">Número cromos propios otorgados de inicio</label>
<div class="col-sm-4">
<input type="text" name="n_cromos_propios" class="form-control" required value="<?php echo htmlentities($result->N_CROMOS_PROPIOS);?>">
</div>


<label class="col-sm-2 control-label"></label>
<div class="col-sm-4">
<input type="text" name="ppp" class="form-control" value="">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">Valor Doble Pareja</label>
<div class="col-sm-4">
<input type="text" name="valordoblepareja" class="form-control" required value="<?php echo htmlentities($result->DOBLEPAREJA);?>">
</div>
<label class="col-sm-2 control-label">Valor  Trio</label>
<div class="col-sm-4">
<input type="text" name="valortrio" class="form-control" required value="<?php echo htmlentities($result->TRIO);?>">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">Valor escalera(3)(referencia)</label>
<div class="col-sm-4">
<input type="text" name="valorescalerasimple3" class="form-control" required value="<?php echo htmlentities($result->ESCALERASIMPLE3);?>">
</div>
<label class="col-sm-2 control-label">Valor escalera(4)(referencia)</label>
<div class="col-sm-4">
<input type="text" name="valorescalerasimple4" class="form-control" required value="<?php echo htmlentities($result->ESCALERASIMPLE4);?>">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">Valor escalera color(3)(referencia)</label>
<div class="col-sm-4">
<input type="text" name="valorescalera3" class="form-control" required value="<?php echo htmlentities($result->ESCALERA3);?>">
</div>
<label class="col-sm-2 control-label">Valor  escalera color(4)(referencia)</label>
<div class="col-sm-4">
<input type="text" name="valorescalera4" class="form-control" required value="<?php echo htmlentities($result->ESCALERA4);?>">
</div>
</div>
<div class="form-group">
<label class="col-sm-2 control-label">Valor Pareja</label>
<div class="col-sm-4">
<input type="text" name="valorpareja" class="form-control" required value="<?php echo htmlentities($result->PAREJA);?>">
</div>
<label class="col-sm-2 control-label">Valor Cuarteto</label>
<div class="col-sm-4">
<input type="text" name="valorcuarteto" class="form-control" required value="<?php echo htmlentities($result->CUARTETO);?>">
</div>

</div>
<div class="form-group">
<label class="col-sm-2 control-label">Valor escalera(3)(estrellas)</label>
<div class="col-sm-4">
<input type="text" name="valorescalera3_estrellas" class="form-control" required value="<?php echo htmlentities($result->ESCALERA3_ESTRELLAS);?>">
</div>
<label class="col-sm-2 control-label">Valor  escalera(4)(estrellas)</label>
<div class="col-sm-4">
<input type="text" name="valorescalera4_estrellas" class="form-control" required value="<?php echo htmlentities($result->ESCALERA4_ESTRELLAS);?>">
</div>
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
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
    <script type="text/javascript" src="https://unpkg.com/@zxing/library@latest"></script>
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
<?php } 


}
  catch (Exception $ex)
  {
      echo "Error:".$ex->getMessage();
  }  

  ?>