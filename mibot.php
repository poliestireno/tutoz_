<?php
session_start();
//error_reporting(0);
include('includes/config.php');
require_once("UTILS/dbutils.php");
$msg="";
if((!isset($_SESSION['alogin']))||(strlen($_SESSION['alogin'])==0))
{	
	header('location:index.php');
}
else{
//var_export($_POST);
//var_export($_SESSION);
if(isset($_POST['submit']))
{	
	$porcentajesPPT="33,33,33|33,33,33|33,33,33";
	if (isset($_POST['slider1-valh']))
	{
		$porcentajesPPT=$_POST['slider1-valh'].",".$_POST['slider2-valh'].",".$_POST['slider3-valh']."|".$_POST['slider1-valh2'].",".$_POST['slider2-valh2'].",".$_POST['slider3-valh2']."|".$_POST['slider1-valh3'].",".$_POST['slider2-valh3'].",".$_POST['slider3-valh3'];
	}

	$pperson = (!isset($_POST['personaje']))?"0":$_POST['personaje'];
	// que no modifique personaje
	if ($pperson==0)
	{
		$pperson=-1;
	}
	$haySitio=true;
	if (isset($_POST['localizacion']))
	{
		// se genera lugar aleatorio para el sitio dado
	    $filaSitio = getSitioFromID($dbh,$_POST['localizacion']);
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
	else
	{
		$haySitio=false;
	}
    // si no hay lugar posible en el sitio dado se pone en exteriores
    if (!$haySitio)
    {
	    $filaSitio = getSitioFromMapID($dbh,9);
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
            	//$haySitio=false; siempre tiene que haber sitio en exteriores
	        	break;
	        }
	    }
    }


	modificarBot($dbh,$_SESSION['alogin'],
		(!isset($_POST['saludo']))?"":$_POST['saludo'],
		(!isset($_POST['palabra_clave']))?"":$_POST['palabra_clave'],
		(!isset($_POST['movilidad']))?"1":$_POST['movilidad'],
		(!isset($_POST['velocidad']))?"3":$_POST['velocidad'],
		(!isset($_POST['checkFantasma']))?"0":"1",
		(!isset($_POST['checkSaltando']))?"0":"1",
		$pperson,
		$porcentajesPPT,
		(!isset($_POST['sPostura1']))?"0":$_POST['sPostura1'],
		(!isset($_POST['sPostura2']))?"0":$_POST['sPostura2'],
		(!isset($_POST['sPostura3']))?"0":$_POST['sPostura3'],
		$filaSitio['ID_MAP'],
		$posx,
		$posy
		
	);
	$msg=" Información actualizada correctamente";
	
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
	
	<title>Editar Mi bot</title>

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
	<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-lightness/jquery-ui.css">

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
.funkyradio div {
  clear: both;
  overflow: hidden;
}

.funkyradio label {
  width: 100%;
  border-radius: 3px;
  border: 1px solid #D1D3D4;
  font-weight: normal;
}

.funkyradio input[type="radio"]:empty,
.funkyradio input[type="checkbox"]:empty {
  display: none;
}

.funkyradio input[type="radio"]:empty ~ label,
.funkyradio input[type="checkbox"]:empty ~ label {
  position: relative;
  line-height: 2.5em;
  text-indent: 3.25em;
  margin-top: 2em;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
}

.funkyradio input[type="radio"]:empty ~ label:before,
.funkyradio input[type="checkbox"]:empty ~ label:before {
  position: absolute;
  display: block;
  top: 0;
  bottom: 0;
  left: 0;
  content: '';
  width: 2.5em;
  background: #D1D3D4;
  border-radius: 3px 0 0 3px;
}

.funkyradio input[type="radio"]:hover:not(:checked) ~ label,
.funkyradio input[type="checkbox"]:hover:not(:checked) ~ label {
  color: #888;
}

.funkyradio input[type="radio"]:hover:not(:checked) ~ label:before,
.funkyradio input[type="checkbox"]:hover:not(:checked) ~ label:before {
  content: '\2714';
  text-indent: .9em;
  color: #C2C2C2;
}

.funkyradio input[type="radio"]:checked ~ label,
.funkyradio input[type="checkbox"]:checked ~ label {
  color: #777;
}

.funkyradio input[type="radio"]:checked ~ label:before,
.funkyradio input[type="checkbox"]:checked ~ label:before {
  content: '\2714';
  text-indent: .9em;
  color: #333;
  background-color: #ccc;
}

.funkyradio input[type="radio"]:focus ~ label:before,
.funkyradio input[type="checkbox"]:focus ~ label:before {
  box-shadow: 0 0 0 3px #999;
}

.funkyradio-default input[type="radio"]:checked ~ label:before,
.funkyradio-default input[type="checkbox"]:checked ~ label:before {
  color: #333;
  background-color: #ccc;
}

.funkyradio-primary input[type="radio"]:checked ~ label:before,
.funkyradio-primary input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #337ab7;
}

.funkyradio-success input[type="radio"]:checked ~ label:before,
.funkyradio-success input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #5cb85c;
}

.funkyradio-danger input[type="radio"]:checked ~ label:before,
.funkyradio-danger input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #d9534f;
}

.funkyradio-warning input[type="radio"]:checked ~ label:before,
.funkyradio-warning input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #f0ad4e;
}

.funkyradio-info input[type="radio"]:checked ~ label:before,
.funkyradio-info input[type="checkbox"]:checked ~ label:before {
  color: #fff;
  background-color: #5bc0de;
}

.slider { width: 200px; }
.slider2 { width: 200px; }
.slider3 { width: 200px; }
.sliderVal {height: 30px; display: inline-block;}
.sliderVal2 {height: 30px; display: inline-block;}
.sliderVal3 {height: 30px; display: inline-block;}

</style>


</head>

<body onload="cargaInicial()">
<?php
		$CORREO = $_SESSION['alogin'];

		$bot = getBot($dbh,$CORREO);
		$alumnoo = getAlumnoFromCorreo($dbh,$CORREO);
		//$bot['SALUDO']="HALLO";
		//$bot['PALABRA_CLAVE']="NAJAS";

?>
	<?php include('includes/header.php');?>
	<div class="ts-main-content">
	<?php include('includes/leftbar.php');?>
		<div class="content-wrapper">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-12">
								<div class="panel panel-default">
									<div class="panel-heading">NANOPROGRAMACIÓN DE TU BOT</div>
<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>

<div class="panel-body">
<form id="form2" method="post" class="form-horizontal" enctype="multipart/form-data">

<input type='hidden' name='slider1-valh' id='slider1-valh'/>
<input type='hidden' name='slider2-valh' id='slider2-valh'/>
<input type='hidden' name='slider3-valh' id='slider3-valh'/>
  
<input type='hidden' name='slider1-valh2' id='slider1-valh2'/>
<input type='hidden' name='slider2-valh2' id='slider2-valh2'/>
<input type='hidden' name='slider3-valh2' id='slider3-valh2'/>
  
<input type='hidden' name='slider1-valh3' id='slider1-valh3'/>
<input type='hidden' name='slider2-valh3' id='slider2-valh3'/>
<input type='hidden' name='slider3-valh3' id='slider3-valh3'/>
  


<?php
$getPropsAlummo =  getPropsVisiblesbot($dbh,$_SESSION['alogin']);
/*$getPropsAlummo['saludo']=1;
$getPropsAlummo['palabra_clave']=1;
$getPropsAlummo['movilidad']=1;
$getPropsAlummo['velocidad_1']=1;
$getPropsAlummo['velocidad_2']=1;
$getPropsAlummo['saltando']=1;
$getPropsAlummo['fantasma']=1;
$getPropsAlummo['localizacion']=1;
$getPropsAlummo['personajes']=1;
$getPropsAlummo['ppt1']=1;*/
?>
<div class="form-group">
	<?php if ($getPropsAlummo['saludo']==1) {?>
	<label class="col-sm-2 control-label">Saludo</label>
	<div class="col-sm-4">
	<input type="text" maxlength = "50" name="saludo" class="form-control"  value="<?php echo htmlentities($bot['SALUDO']);?>">
	</div>
	<?php } if ($getPropsAlummo['palabra_clave']==1) {?>
	<label class="col-sm-2 control-label">Palabra Clave</label>
	<div class="col-sm-4">
	<input type="text" name="palabra_clave" maxlength = "50" class="form-control"  value="<?php echo htmlentities($bot['PALABRA_CLAVE']);?>">
	</div>
	<?php } ?>
</div>
<div class="form-group">
	<?php if ($getPropsAlummo['movilidad']==1) {?>
	<label class="col-sm-2 control-label">Modo Movilidad</label>
	<div class="col-sm-4">
	<select name="movilidad" class="form-control">
		<option value="0" <?php echo (($bot['MOVILIDAD']=="0")?" selected='selected' ":"")?>>
		Parado
		</option>
		<option value="1" <?php echo (($bot['MOVILIDAD']=="1")?" selected='selected' ":"")?>>
		Aleatorio
		</option>
		<option value="2" <?php echo (($bot['MOVILIDAD']=="2")?" selected='selected' ":"")?>>
		Zombie
		</option>
		<option value="3" <?php echo (($bot['MOVILIDAD']=="3")?" selected='selected' ":"")?>>
		Miedo
		</option>
	</select>
	</div><?php } ?>
	<?php if (($getPropsAlummo['velocidad_1']==1)||($getPropsAlummo['velocidad_2']==1)) {?>
	<label class="col-sm-2 control-label">Velocidad</label>
	<div class="col-sm-4">
	<select name="velocidad" class="form-control">
		<option value="1" <?php echo (($bot['VELOCIDAD']=="1")?" selected='selected' ":"")?>>
		Caracol
		</option>
		<option value="2" <?php echo (($bot['VELOCIDAD']=="2")?" selected='selected' ":"")?>>
		Tortuga
		</option>
		<option value="3" <?php echo (($bot['VELOCIDAD']=="3")?" selected='selected' ":"")?>>
		Burro
		</option>
		<option value="4" <?php echo (($bot['VELOCIDAD']=="4")?" selected='selected' ":"")?>>
		Humano
		</option>
<?php if ($getPropsAlummo['velocidad_2']==1) {?>
		<option value="5" <?php echo (($bot['VELOCIDAD']=="5")?" selected='selected' ":"")?>>
		Usain Bolt
		</option>
		<option value="6" <?php echo (($bot['VELOCIDAD']=="6")?" selected='selected' ":"")?>>
		Supersónico
		</option>
<?php } ?>
	</select>
	</div><?php } ?>
</div>

<div class="form-group">
	<?php if ($getPropsAlummo['localizacion']==1) {?>


	<label class="col-sm-2 control-label">Localización</label>
	<div class="col-sm-4">
	<select class="form-control" ID="sel11" name="localizacion">
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
            echo "<option ".(($IDAs==getSitioFromMapID($dbh,$bot['ID_MAPA_INICIO'])['ID'])?" selected=selected ":"")."value='".$IDAs."'>".$nombre."</option>";
        }
        ?>
    </select>
	</div>










<?php } ?>
</div>
<div class="form-group">
	<?php if ($getPropsAlummo['saltando']==1) {?>
	<label class="col-sm-2 control-label"></label>
		
		<div class="col-sm-4 funkyradio">
			<div class="funkyradio-success">
	            <input type="checkbox" name="checkSaltando" id="radio1" <?php echo (($bot['SALTANDO']=="1")?" checked='checked' ":"")?>/>
	            <label for="radio1">Ir saltando</label>
	        	</div>
    	</div>


	<?php } if ($getPropsAlummo['fantasma']==1) {?>
		<label class="col-sm-2 control-label"></label>
			<div class="col-sm-4 funkyradio">
			<div class="funkyradio-success">
	            <input type="checkbox" name="checkFantasma" id="radio12" <?php echo (($bot['FANTASMA']=="1")?" checked='checked' ":"")?>/>
	            <label for="radio12">Modo fantasma</label>
	        	</div>
    	</div>
	<?php } ?>
</div>
<div class="form-group">
	<?php if ($getPropsAlummo['personajes']==1) {?>
	<label class="col-sm-2 control-label">Personajes disponibles</label>
	<div class="col-sm-4">	
	<?php 
	if ($alumnoo['gender']=='f')
		echo '<img src="img/chicasReferencia01.png">';		
	else 	
		echo '<img src="img/chicosReferencia01.png">';
	?>									
	</div><?php } ?>
	<?php if ($getPropsAlummo['personajes']==1) {?>
	<label class="col-sm-2 control-label">Elegir personaje</label>
	<div class="col-sm-4">
	<select name="personaje" class="form-control">
		<option value="0" <?php echo (($bot['PERSONAJE']=="0")?" selected='selected' ":"")?>>
		</option>		
		<option value="1" <?php echo (($bot['PERSONAJE']=="1")?" selected='selected' ":"")?>>
		1
		</option>
		<option value="2" <?php echo (($bot['PERSONAJE']=="2")?" selected='selected' ":"")?>>
		2
		</option>
		<option value="3" <?php echo (($bot['PERSONAJE']=="3")?" selected='selected' ":"")?>>
		3
		</option>
		<option value="4" <?php echo (($bot['PERSONAJE']=="4")?" selected='selected' ":"")?>>
		4
		</option>
		<option value="5" <?php echo (($bot['PERSONAJE']=="5")?" selected='selected' ":"")?>>
		5
		</option>
		<option value="6" <?php echo (($bot['PERSONAJE']=="6")?" selected='selected' ":"")?>>
		6
		</option>
		<option value="7" <?php echo (($bot['PERSONAJE']=="7")?" selected='selected' ":"")?>>
		7
		</option>
		<option value="8" <?php echo (($bot['PERSONAJE']=="8")?" selected='selected' ":"")?>>
		8
		</option>
		<option value="9" <?php echo (($bot['PERSONAJE']=="9")?" selected='selected' ":"")?>>
		9
		</option>
		<option value="10" <?php echo (($bot['PERSONAJE']=="10")?" selected='selected' ":"")?>>
		10
		</option>
	</select>
	</div>
<?php } ?>
</div>

<div class="form-group">
	<?php if ($getPropsAlummo['ppt1']==1) {?>
	<h3>Estrategia Piedra Papel Tijera</h3>
	<label class="col-sm-2 control-label">Reglas postura 1</label>
	<div class="col-sm-4">
	<select name="sPostura1" class="form-control">
		<option value="0" <?php echo (($bot['POSTURA1']=="0")?" selected='selected' ":"")?>>
			Porcentajes
		</option>
	</select>
	</div>

	<?php } if ($getPropsAlummo['ppt1']==1) {?>
	<label class="col-sm-2 control-label">Porcentajes</label>
	<div class="col-sm-4">
PIEDRA:<div id="slider1" class="slider"></div>
<div id="slider1-val" name="slider1-val" class="sliderVal"></div>%

<div>PAPEL:</div><div id="slider2" class="slider"></div>
<div id="slider2-val" name="slider2-val" class="sliderVal"></div>%

<div>TIJERA:</div><div id="slider3" class="slider"></div>
<div id="slider3-val" name="slider3-val" class="sliderVal"></div>%
	</div>
	<?php } ?>
</div>
<div class="form-group">
	<?php if ($getPropsAlummo['ppt1']==1) {?>
	<label class="col-sm-2 control-label">Reglas postura 2</label>
	<div class="col-sm-4">
	<select name="sPostura2" class="form-control">
		<option value="0" <?php echo (($bot['POSTURA2']=="0")?" selected='selected' ":"")?>>
			Porcentajes
		</option>		
		<option value="1" <?php echo (($bot['POSTURA2']=="1")?" selected='selected' ":"")?>>
		Si en la anterior gané repito postura
		</option>
		<option value="2" <?php echo (($bot['POSTURA2']=="2")?" selected='selected' ":"")?>>
		Si en la anterior gané no repito postura
		</option>
		<option value="3" <?php echo (($bot['POSTURA2']=="3")?" selected='selected' ":"")?>>
		Si en la anterior empaté repito postura
		</option>
		<option value="4" <?php echo (($bot['POSTURA2']=="4")?" selected='selected' ":"")?>>
		Si en la anterior empaté no repito postura
		</option>
		<option value="5" <?php echo (($bot['POSTURA2']=="5")?" selected='selected' ":"")?>>
		Si en la anterior perdí repito postura
		</option>
		<option value="6" <?php echo (($bot['POSTURA2']=="6")?" selected='selected' ":"")?>>
		Si en la anterior perdí no repito postura
		</option>
		<option value="7" <?php echo (($bot['POSTURA2']=="7")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó piedra ahora saco piedra
		</option>
		<option value="8" <?php echo (($bot['POSTURA2']=="8")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó piedra ahora saco papel 
		</option>
		<option value="9" <?php echo (($bot['POSTURA2']=="9")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó piedra ahora saco tijera 
		</option>
		<option value="10" <?php echo (($bot['POSTURA2']=="10")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó papel ahora saco piedra
		</option>
		<option value="11" <?php echo (($bot['POSTURA2']=="11")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó papel ahora saco papel 
		</option>
		<option value="12" <?php echo (($bot['POSTURA2']=="12")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó papel ahora saco tijera 
		</option>
		<option value="13" <?php echo (($bot['POSTURA2']=="13")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó tijera ahora saco piedra
		</option>
		<option value="14" <?php echo (($bot['POSTURA2']=="14")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó tijera ahora saco papel 
		</option>
		<option value="15" <?php echo (($bot['POSTURA2']=="15")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó tijera ahora saco tijera 
		</option>			
	</select>
	<b>(si no se cumple la regla elegida se aplican los porcentajes)</b>
	</div>		
	<?php } if ($getPropsAlummo['ppt1']==1) {?>
	<label class="col-sm-2 control-label">Porcentajes</label>
	<div class="col-sm-4">
PIEDRA:<div id="slider12" class="slider2"></div>
<div id="slider1-val2" name="slider1-val2" class="sliderVal2"></div>%

<div>PAPEL:</div><div id="slider22" class="slider2"></div>
<div id="slider2-val2" name="slider2-val2" class="sliderVal2"></div>%

<div>TIJERA:</div><div id="slider32" class="slider2"></div>
<div id="slider3-val2" name="slider3-val2" class="sliderVal2"></div>%
	</div>

	<?php } ?>
</div>

<div class="form-group">
	<?php if ($getPropsAlummo['ppt1']==1) {?>
	<label class="col-sm-2 control-label">Reglas postura 3</label>
	<div class="col-sm-4">
	<select name="sPostura3" class="form-control">
		<option value="0" <?php echo (($bot['POSTURA3']=="0")?" selected='selected' ":"")?>>
			Porcentajes
		</option>		
		<option value="1" <?php echo (($bot['POSTURA3']=="1")?" selected='selected' ":"")?>>
		Si en la anterior gané repito postura
		</option>
		<option value="2" <?php echo (($bot['POSTURA3']=="2")?" selected='selected' ":"")?>>
		Si en la anterior gané no repito postura
		</option>
		<option value="3" <?php echo (($bot['POSTURA3']=="3")?" selected='selected' ":"")?>>
		Si en la anterior empaté repito postura
		</option>
		<option value="4" <?php echo (($bot['POSTURA3']=="4")?" selected='selected' ":"")?>>
		Si en la anterior empaté no repito postura
		</option>
		<option value="5" <?php echo (($bot['POSTURA3']=="5")?" selected='selected' ":"")?>>
		Si en la anterior perdí repito postura
		</option>
		<option value="6" <?php echo (($bot['POSTURA3']=="6")?" selected='selected' ":"")?>>
		Si en la anterior perdí no repito postura
		</option>
		<option value="7" <?php echo (($bot['POSTURA3']=="7")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó piedra ahora saco piedra
		</option>
		<option value="8" <?php echo (($bot['POSTURA3']=="8")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó piedra ahora saco papel 
		</option>
		<option value="9" <?php echo (($bot['POSTURA3']=="9")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó piedra ahora saco tijera 
		</option>
		<option value="10" <?php echo (($bot['POSTURA3']=="10")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó papel ahora saco piedra
		</option>
		<option value="11" <?php echo (($bot['POSTURA3']=="11")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó papel ahora saco papel 
		</option>
		<option value="12" <?php echo (($bot['POSTURA3']=="12")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó papel ahora saco tijera 
		</option>
		<option value="13" <?php echo (($bot['POSTURA3']=="13")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó tijera ahora saco piedra
		</option>
		<option value="14" <?php echo (($bot['POSTURA3']=="14")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó tijera ahora saco papel 
		</option>
		<option value="15" <?php echo (($bot['POSTURA3']=="15")?" selected='selected' ":"")?>>
		Si en la anterior el adversario sacó tijera ahora saco tijera 
		</option>
	</select>
	<b>(si no se cumple la regla elegida se aplican los porcentajes)</b>
	</div>
	<?php } if ($getPropsAlummo['ppt1']==1) {?>
	<label class="col-sm-2 control-label">Porcentajes</label>
	<div class="col-sm-4">
PIEDRA:<div id="slider13" class="slider3"></div>
<div id="slider1-val3" name="slider1-val3" class="sliderVal3"></div>%

<div>PAPEL:</div><div id="slider23" class="slider3"></div>
<div id="slider2-val3" name="slider2-val3" class="sliderVal3"></div>%

<div>TIJERA:</div><div id="slider33" class="slider3"></div>
<div id="slider3-val3" name="slider3-val3" class="sliderVal3"></div>%
	</div>
	<?php } ?>
</div>


	<div class="col-sm-4 col-sm-offset-2">
		<button class="btn btn-primary" name="submit" onclick="manageSubmit()" >Guardar cambios</button>

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
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<script src="js/bootstrap-select.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<script src="js/jquery.dataTables.min.js"></script>
	<script src="js/dataTables.bootstrap.min.js"></script>
	<script src="js/Chart.min.js"></script>
	<script src="js/fileinput.js"></script>
	<script src="js/chartData.js"></script>
	<script src="js/main.js"></script>
	<script type="text/javascript">
	function manageSubmit()
	{ 
	if (document.getElementById('slider1-val'))
	{
		document.getElementById('slider1-valh').value=document.getElementById("slider1-val").innerHTML;
		document.getElementById('slider2-valh').value=document.getElementById("slider2-val").innerHTML;
		document.getElementById('slider3-valh').value=document.getElementById("slider3-val").innerHTML;

		document.getElementById('slider1-valh2').value=document.getElementById("slider1-val2").innerHTML;
		document.getElementById('slider2-valh2').value=document.getElementById("slider2-val2").innerHTML;
		document.getElementById('slider3-valh2').value=document.getElementById("slider3-val2").innerHTML;

		document.getElementById('slider1-valh3').value=document.getElementById("slider1-val3").innerHTML;
		document.getElementById('slider2-valh3').value=document.getElementById("slider2-val3").innerHTML;
		document.getElementById('slider3-valh3').value=document.getElementById("slider3-val3").innerHTML;

	}

	}
	function cargaInicial()
	{ 
	if (document.getElementById('slider1-val'))
	{
		<?php
		$datosAll = explode('|', htmlentities($bot['PORCENT_PPT']));
		if ((Count($datosAll)<3)||($datosAll[0]==',,'))
		{
			$datos = array();
			$datos[]='33';$datos[]='33';$datos[]='33';
			$datos2 = array();
			$datos2[]='33';$datos2[]='33';$datos2[]='33';
			$datos3 = array();
			$datos3[]='33';$datos3[]='33';$datos3[]='33';
		}
		else
		{
			$datos = explode(',', $datosAll[0]);
			$datos2 = explode(',', $datosAll[1]);
			$datos3 = explode(',', $datosAll[2]);
		}
		?>
	document.getElementById("slider1-val").innerHTML=<?php echo (($datos[0]=='')?'\'\'':$datos[0])?>;
	document.getElementById("slider2-val").innerHTML=<?php echo (($datos[1]=='')?'\'\'':$datos[1])?>;
	document.getElementById("slider3-val").innerHTML=<?php echo (($datos[2]=='')?'\'\'':$datos[2])?>;
	var allSliders = $(".slider");
    var arr = [<?php echo $datos[0]?>,<?php echo $datos[1]?>,<?php echo $datos[2]?>];
    var ii =0;
    allSliders.each(function() {
        $(this).slider("value", arr[ii]);
        ii++;
    });

	document.getElementById("slider1-val2").innerHTML=<?php echo (($datos2[0]=='')?'\'\'':$datos2[0])?>;
	document.getElementById("slider2-val2").innerHTML=<?php echo (($datos2[1]=='')?'\'\'':$datos2[1])?>;
	document.getElementById("slider3-val2").innerHTML=<?php echo (($datos2[2]=='')?'\'\'':$datos2[2])?>;
	var allSliders2 = $(".slider2");
    var arr = [<?php echo $datos2[0]?>,<?php echo $datos2[1]?>,<?php echo $datos2[2]?>];
    var ii =0;
    allSliders2.each(function() {
        $(this).slider("value", arr[ii]);
        ii++;
    });

	document.getElementById("slider1-val3").innerHTML=<?php echo (($datos3[0]=='')?'\'\'':$datos3[0])?>;
	document.getElementById("slider2-val3").innerHTML=<?php echo (($datos3[1]=='')?'\'\'':$datos3[1])?>;
	document.getElementById("slider3-val3").innerHTML=<?php echo (($datos3[2]=='')?'\'\'':$datos3[2])?>;
	var allSliders3 = $(".slider3");
    var arr = [<?php echo $datos3[0]?>,<?php echo $datos3[1]?>,<?php echo $datos3[2]?>];
    var ii =0;
    allSliders3.each(function() {
        $(this).slider("value", arr[ii]);
        ii++;
    });


 }
	}


	$(document).ready(function () {          
		setTimeout(function() {
			$('.succWrap').slideUp("slow");
		}, 3000);
		});

	$(function() {
    // set the initial value of the sliders
    var sliderStartPosition = 100 / $(".slider").length;
    // create the sliders
    $(".slider").slider({
    	
        range: "min",
        value: sliderStartPosition,
        min: 0,
        max: 100,
        slide: function(event, ui) {
            var movement = ui.value - $(this).slider("value");        
            //console.log($(this));
            // move the following sliders
            var allSliders = $(".slider");
            var currentSliderIndex = allSliders.index($(this));
            var previousSliders = allSliders.filter(":lt(" + currentSliderIndex + ")");
            var followingSliders = allSliders.filter(":gt(" + currentSliderIndex + ")");
            var numFollowingSliders = followingSliders.length;
           

            // if the last slider is being moved, then adjust the previous slider
            if (currentSliderIndex == (allSliders.length - 1))
            {
                followingSliders = allSliders.filter(":eq(" + (currentSliderIndex - 1) + ")");
                previousSliders = allSliders.filter(":lt(" + (currentSliderIndex - 1) + ")");
            }
            
            // get the total value of all sliders
            var allSlidersTotal = 0;
            allSliders.each(function() {
                allSlidersTotal += $(this).slider("value");
            });
            allSlidersTotal = allSlidersTotal - $(this).slider("value") + ui.value;
            
            // get the total value of the previous sliders
            var previousSlidersTotal = 0;
            previousSliders.each(function() {
                previousSlidersTotal += $(this).slider("value");
            });

            // reset the following sliders
            var allSlidersDifference = 100 - allSlidersTotal;
            var singleSliderDifference = Math.round(allSlidersDifference / followingSliders.length);
            followingSliders.each(function() {
              var currentVal = $(this).slider("value");
              var newVal = currentVal + singleSliderDifference;
              $(this).slider("value", newVal);
            });

            // lock slider so we don't go over 100%
            if ((previousSlidersTotal + ui.value) > 100)
                event.preventDefault();
            else {
                // set sliderVal display val
                followingSliders.each(function() {
                    $(this).next().html($(this).slider("value"))
                });
                
                // reset current value
                $(this).next().html(ui.value);
            }
        }
    });

        $(".slider2").slider({
    	
        range: "min",
        value: sliderStartPosition,
        min: 0,
        max: 100,
        slide: function(event, ui) {
            var movement = ui.value - $(this).slider("value");        
            //console.log($(this));
            // move the following sliders
            var allSliders = $(".slider2");
            var currentSliderIndex = allSliders.index($(this));
            var previousSliders = allSliders.filter(":lt(" + currentSliderIndex + ")");
            var followingSliders = allSliders.filter(":gt(" + currentSliderIndex + ")");
            var numFollowingSliders = followingSliders.length;
           

            // if the last slider is being moved, then adjust the previous slider
            if (currentSliderIndex == (allSliders.length - 1))
            {
                followingSliders = allSliders.filter(":eq(" + (currentSliderIndex - 1) + ")");
                previousSliders = allSliders.filter(":lt(" + (currentSliderIndex - 1) + ")");
            }
            
            // get the total value of all sliders
            var allSlidersTotal = 0;
            allSliders.each(function() {
                allSlidersTotal += $(this).slider("value");
            });
            allSlidersTotal = allSlidersTotal - $(this).slider("value") + ui.value;
            
            // get the total value of the previous sliders
            var previousSlidersTotal = 0;
            previousSliders.each(function() {
                previousSlidersTotal += $(this).slider("value");
            });

            // reset the following sliders
            var allSlidersDifference = 100 - allSlidersTotal;
            var singleSliderDifference = Math.round(allSlidersDifference / followingSliders.length);
            followingSliders.each(function() {
              var currentVal = $(this).slider("value");
              var newVal = currentVal + singleSliderDifference;
              $(this).slider("value", newVal);
            });

            // lock slider so we don't go over 100%
            if ((previousSlidersTotal + ui.value) > 100)
                event.preventDefault();
            else {
                // set sliderVal display val
                followingSliders.each(function() {
                    $(this).next().html($(this).slider("value"))
                });
                
                // reset current value
                $(this).next().html(ui.value);
            }
        }
    });


        $(".slider3").slider({
    	
        range: "min",
        value: sliderStartPosition,
        min: 0,
        max: 100,
        slide: function(event, ui) {
            var movement = ui.value - $(this).slider("value");        
            //console.log($(this));
            // move the following sliders
            var allSliders = $(".slider3");
            var currentSliderIndex = allSliders.index($(this));
            var previousSliders = allSliders.filter(":lt(" + currentSliderIndex + ")");
            var followingSliders = allSliders.filter(":gt(" + currentSliderIndex + ")");
            var numFollowingSliders = followingSliders.length;
           

            // if the last slider is being moved, then adjust the previous slider
            if (currentSliderIndex == (allSliders.length - 1))
            {
                followingSliders = allSliders.filter(":eq(" + (currentSliderIndex - 1) + ")");
                previousSliders = allSliders.filter(":lt(" + (currentSliderIndex - 1) + ")");
            }
            
            // get the total value of all sliders
            var allSlidersTotal = 0;
            allSliders.each(function() {
                allSlidersTotal += $(this).slider("value");
            });
            allSlidersTotal = allSlidersTotal - $(this).slider("value") + ui.value;
            
            // get the total value of the previous sliders
            var previousSlidersTotal = 0;
            previousSliders.each(function() {
                previousSlidersTotal += $(this).slider("value");
            });

            // reset the following sliders
            var allSlidersDifference = 100 - allSlidersTotal;
            var singleSliderDifference = Math.round(allSlidersDifference / followingSliders.length);
            followingSliders.each(function() {
              var currentVal = $(this).slider("value");
              var newVal = currentVal + singleSliderDifference;
              $(this).slider("value", newVal);
            });

            // lock slider so we don't go over 100%
            if ((previousSlidersTotal + ui.value) > 100)
                event.preventDefault();
            else {
                // set sliderVal display val
                followingSliders.each(function() {
                    $(this).next().html($(this).slider("value"))
                });
                
                // reset current value
                $(this).next().html(ui.value);
            }
        }
    });

    // set initial slider value
/*    $(".sliderVal").each(function() {
    	alert($(this).prev().slider("value"));
        $(this).html($(this).prev().slider("value")) 
    });
*/
});
	</script>
</body>
</html>
<?php } ?>