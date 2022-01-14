<?php
session_start();
//error_reporting(0);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$msg="";
//var_export($_POST);
try
  {
$sql = "SELECT username from admin where username='ADMIN_FCT' or username='ADMIN'";
    $result = array();
    $stmt = $dbh -> query($sql);
    while ($fila = $stmt->fetch(PDO::FETCH_ASSOC))
    {
      $result [] = $fila;
    }
if(
	((!isset($_SESSION['alogin']))
	||
	(strlen($_SESSION['alogin'])==0))
	||(
	($_SESSION['alogin']!=$result[0]['username'])
	&&
	($_SESSION['alogin']!=$result[1]['username'])
))
  { 
header('location:index.php');
}
else{

function getInfoColumnaFromTablaId($dbh,$nombreCol,$idAux)
{
	$valor ="";

	if (substr( $nombreCol, 0, 3 ) === "ID_")
	{
		$nombreTab=substr( $nombreCol, 3, strlen($nombreCol) )."S";

		if (existeColumnaEnTabla($dbh,"INFO", $nombreTab))
		{
			$aData = ejecutarQuery($dbh,"SELECT * FROM ".$nombreTab." WHERE ID =".$idAux);
			if (Count($aData)>0)
			{
				$valor =$aData[0]['INFO'];
			}
		}
		else if (existeColumnaEnTabla($dbh,"NOMBRE", $nombreTab))
		{
			$aData = ejecutarQuery($dbh,"SELECT * FROM ".$nombreTab." WHERE ID =".$idAux);
			if (Count($aData)>0)
			{
				$valor =$aData[0]['NOMBRE'];
			}
			
		}
	}

	return $valor;
}
function splitx($str) {

    $arr		= [];$len = 1;
    $length 	= mb_strlen($str, 'UTF-8');

    for ($i = 0; $i < $length; $i += $len) {

        $arr[] = mb_substr($str, $i, $len, 'UTF-8');

    }

    return $arr;

}

function setCampoDeLista($dbh,$fila,$columna)
{

//echo ($columna['COLUMN_NAME']=='ID')?'':'<td><input  name="in__'.$fila['ID'].'__'.$columna['COLUMN_NAME'].'" class="form-control" type="text" value="'.$fila[$columna['COLUMN_NAME']].'" /><span style="visibility:hidden">'.$fila[$columna['COLUMN_NAME']].'</span>'.getInfoColumnaFromTablaId($dbh,$columna['COLUMN_NAME'],$fila[$columna['COLUMN_NAME']]).'</td>';

	$sTextSelect = "";
	if (substr( $columna['COLUMN_NAME'], 0, 3 ) === "ID_")
	{
		$nombreTab=substr( $columna['COLUMN_NAME'], 3, strlen($columna['COLUMN_NAME']) )."S";

		
		$bEsisteInfo = existeColumnaEnTabla($dbh,"INFO", $nombreTab);
		$bEsisteNombre = existeColumnaEnTabla($dbh,"NOMBRE", $nombreTab);

		if ($bEsisteInfo || $bEsisteNombre)
		{
			$aDataTotal = ejecutarQuery($dbh,"SELECT * FROM ".$nombreTab);
			$sTextSelect .='<td class="table-info"><select name="in__'.$fila['ID'].'__'.$columna['COLUMN_NAME'].'"  class="form-control selectpicker" id="in__'.$fila['ID'].'__'.$columna['COLUMN_NAME'].'"  data-live-search="true" >';
			$sValorSeleccionado = "";
			foreach ($aDataTotal as $filaTabla) 
			{
				$bInfoOk = false;
				$valor ="";
				$aData = ejecutarQuery($dbh,"SELECT * FROM ".$nombreTab." WHERE ID =".$filaTabla['ID']);
				if ($bEsisteInfo)
				{
					if (Count($aData)>0)
					{
						$valor =$aData[0]['INFO'];
						$bInfoOk = ($valor!="");
					}
				}
				if (!$bInfoOk)
				{
					if ($bEsisteNombre)
					{
						$valor ="ID:".$aData[0]['ID']." NOMBRE:".$aData[0]['NOMBRE'].(($bEsisteInfo)?" (RELLENAR INFO)":"");
					}
					else
					{
						$valor ="ID:".$aData[0]['ID'].(($bEsisteInfo)?" (RELLENAR INFO)":"");
					}
				}
				$sTextSelect .="<option ".(($aData[0]['ID']==$fila[$columna['COLUMN_NAME']])?" selected='selected' ":"")." value ='".$aData[0]['ID']."'>".str_replace('_', '&nbsp;',implode(' ',splitx(str_replace(' ', '_', $valor),"UTF-8")))."</option>";
				if ($aData[0]['ID']==$fila[$columna['COLUMN_NAME']])
				{
					$sValorSeleccionado = $valor;
				}
			}
			$sTextSelect .='</select><span style="visibility:hidden">'.$sValorSeleccionado.'</span></td>';
		}
		else
		{
			$sTextSelect='<td><input  name="in__'.$fila['ID'].'__'.$columna['COLUMN_NAME'].'" class="form-control" type="text" value="'.$fila[$columna['COLUMN_NAME']].'" /><span style="visibility:hidden">'.$fila[$columna['COLUMN_NAME']].'</span></td>';
		}
	}
	else
	{
		$sTextSelect='<td><input  name="in__'.$fila['ID'].'__'.$columna['COLUMN_NAME'].'" class="form-control" type="text" value="'.$fila[$columna['COLUMN_NAME']].'" /><span style="visibility:hidden">'.$fila[$columna['COLUMN_NAME']].'</span></td>';
	}
	return $sTextSelect;
}
function setCampoParaInsert($dbh,$nombreCol)
{
	$sTextSelect = "";
	if (substr( $nombreCol, 0, 3 ) === "ID_")
	{
		$nombreTab=substr( $nombreCol, 3, strlen($nombreCol) )."S";

		
		$bEsisteInfo = existeColumnaEnTabla($dbh,"INFO", $nombreTab);
		$bEsisteNombre = existeColumnaEnTabla($dbh,"NOMBRE", $nombreTab);

		if ($bEsisteInfo || $bEsisteNombre)
		{
			$aDataTotal = ejecutarQuery($dbh,"SELECT * FROM ".$nombreTab);
			$sTextSelect .='<td class="table-info"><select  class="form-control selectpicker" id="gg__'.$nombreCol.'" data-live-search="true" name="gg__'.$nombreCol.'" >';
			foreach ($aDataTotal as $filaTabla) 
			{
				$bInfoOk = false;
				$valor ="";
				$aData = ejecutarQuery($dbh,"SELECT * FROM ".$nombreTab." WHERE ID =".$filaTabla['ID']);
				if ($bEsisteInfo)
				{
					if (Count($aData)>0)
					{
						$valor =$aData[0]['INFO'];
						$bInfoOk = ($valor!="");
					}
				}
				if (!$bInfoOk)
				{
					if ($bEsisteNombre)
					{
						$valor ="ID:".$aData[0]['ID']." NOMBRE:".$aData[0]['NOMBRE'].(($bEsisteInfo)?" (RELLENAR INFO)":"");
					}
					else
					{
						$valor ="ID:".$aData[0]['ID'].(($bEsisteInfo)?" (RELLENAR INFO)":"");
					}
				}
				$sTextSelect .="<option value ='".$aData[0]['ID']."'>".str_replace('_', '&nbsp;',implode(' ',splitx(str_replace(' ', '_', $valor),"UTF-8")))."</option>";
			}
			$sTextSelect .="</select></td>";
		}
		else
		{						
			$sTextSelect='<td><input name="gg__'.$nombreCol.'" class="form-control" type="text" value="" /></td>';
		}
	}
	else
	{
		$sTextSelect='<td><input name="gg__'.$nombreCol.'" class="form-control" type="text" value="" /></td>';
	}
	return $sTextSelect;
}

if (isset($_GET['tabla']))
{
  $nombreTabla=$_GET['tabla'];
}
if (isset($_POST['tabla']))
{
  $nombreTabla=$_POST['tabla'];
}
$idSearch=-1;
if (isset($_GET['idSearch']))
{
  $idSearch=$_GET['idSearch'];
}
if (isset($_POST['idSearch']))
{
  $idSearch=$_POST['idSearch'];
}


$alumno = getAlumnoFromCorreo($dbh,$_SESSION['alogin']);
//var_export($_POST);

$aColumnas = getColumnasFromTabla($dbh,$nombreTabla);
//var_export($aColumnas);

if(isset($_POST['esInsertar'])&&($_POST['esInsertar']!=0))
{   

		
		$comma2="";
		$columnasEntreComas="";
		$interrogaciones ="";
		foreach($_POST as $key => $value)
		{	    
				if	(substr( $key, 0, 4 ) === "gg__")
				{
					    
							$nombreColll = substr($key,4,strlen($key));
							$columnasEntreComas.=$comma2.$nombreColll;
							$interrogaciones.=$comma2."'".$value."'";							
							$comma2=",";
					    
				}

		}  
		$sql = "INSERT INTO ".$nombreTabla." (".$columnasEntreComas.") VALUES (".$interrogaciones.")"; 
		$msg.=' Registro: '.insertarQuery($dbh,$sql);	
}
else if(isset($_POST['esDelete'])&&($_POST['esDelete']!=0))
{   
		$cont=1;
		foreach($_POST as $key => $value)
		{	    
				if	(substr( $key, 0, 4 ) === "cb__")
				{
					$msg.=' Registro '.$cont . ': '.borrarFilaFromId($dbh,"DELETE FROM ".$nombreTabla." WHERE ID=".$value);	
					$cont++;
				}
		}   
}
else if(isset($_POST['esModificar'])&&($_POST['esModificar']!=0))
{   
		ksort($_POST);
		$aIdsToModif = array();
		foreach($_POST as $key => $value)
		{	    
				if	(substr( $key, 0, 4 ) === "cb__")
				{
					$aIdsToModif[]= $value;
				}
		} 

		$cont=1;
		foreach ($aIdsToModif as $idAux) 
		{
			$setsColumnas = "";
			$comma="";
			foreach($_POST as $key => $value)
			{	    
					if	(substr( $key, 0, 5 + (strlen((string)$idAux))) === "in__".$idAux."_")
					{
						
						$nombreCol=substr($key,6 + (strlen((string)$idAux)),strlen($key));
						//echo 'key:'.$key.' idaxu:'.$idAux.' lenAux:'.strlen((string)$idAux).' nombreCol:'.$nombreCol;
						$setsColumnas.= " ".	$comma.$nombreCol."='".$value."' ";
						$comma=",";

						
					}	
			}
			//echo ("UPDATE ".$nombreTabla." SET ".$setsColumnas." WHERE ID=".$idAux);
$msg.=' Registro '.$cont . ': '.modificarQuery($dbh,"UPDATE ".$nombreTabla." SET ".$setsColumnas." WHERE ID=".$idAux);	
						$cont++;

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
	<meta name="theme-color" content="#3e454c">
	
	<title><?php echo $nombreTabla?></title>

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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet" />

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
tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }

		</style>


</head>

<body>


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
	<?php if($msg){?><div class="succWrap"><strong>INFO: </strong><?php echo htmlentities($msg); ?> </div><?php }?>								
	<div class="panel-heading"><?php echo $nombreTabla?></div>

<form method="post" id="form1" class="form-horizontal" enctype="multipart/form-data" >
<input type="hidden" name="esDelete" id="esDelete" value="0">
<input type="hidden" name="esModificar" id="esModificar" value="0">
<input type="hidden" name="esInsertar" id="esInsertar" value="0">
<input type="hidden" name="tabla" id="tabla" value="<?php echo $nombreTabla?>">
<div class="form-group">
	<div class="col-sm-4">
	</div>
	<div class="col-sm-4 text-center">
	</div>
	<div class="col-sm-4">
	</div>
</div>
<div class="panel panel-default">
						
	<div class="panel-heading">INSERTAR</div>

<div class="container-fluid">

<div class="form-group">
	<div class="col-sm-4">
	</div>
	<div class="col-sm-4 text-center">
	</div>
	<div class="col-sm-4">
	</div>
</div>
<div class="col-md-12">
	<button type="button" class="btn btn-success" onclick="manageInsertar()">Insertar</button>
</div>
<div class="form-group">
	<div class="col-sm-4">
	</div>
</div>
<table ID="zctb2" class="display table table-hover" cellspacing="0" width="100%">
  <!--Table head-->


  <thead>


    <tr>

<?php

foreach ($aColumnas as $columna) 
{
	echo ($columna['COLUMN_NAME']=='ID')?'':'<th>'.$columna['COLUMN_NAME'].'['.$columna['COLUMN_TYPE'].']</th>';
}

?>      

   </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
<?php

	echo '<tr class="table-info">';
	foreach ($aColumnas as $columna) 
	{
		echo (($columna['COLUMN_NAME']=='ID')?'':setCampoParaInsert($dbh,$columna['COLUMN_NAME']));
	}
  echo '</tr>';	
	
	
?>
  </tbody>
  <!--Table body-->
</table>

</div>
</div>


<div class="panel panel-default">
						
	<div class="panel-heading">DATOS</div>

<div class="container-fluid">

<div class="form-group">
	<div class="col-sm-4">
	</div>
	<div class="col-sm-4 text-center">
	</div>
	<div class="col-sm-4">
	</div>
</div>
<div class="col-md-12">
	<button type="button" class="btn btn-danger pull-left" onclick="manageDelete()">Borrar</button>
	<button type="button" class="btn btn-warning pull-left" onclick="manageModificar()">Modificar</button>
	<span class="text-right"><a type="button" href="manageTabla.php?tabla=<?php echo $nombreTabla?>" class="btn btn-primary pull-right" >Ver todos los datos de la tabla</a></span>

</div>
<div class="form-group">
	<div class="col-sm-4">
	</div>
	<div class="col-sm-4 text-center">
	</div>
	<div class="col-sm-4">
	</div>
</div>
<table ID="example2" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
  <!--Table head-->
  <thead>
    <tr>

<th></th>
<th>Select</th>
<?php

foreach ($aColumnas as $columna) 
{
	echo ($columna['COLUMN_NAME']=='ID')?'':'<th>'.$columna['COLUMN_NAME'].'</th>';
}

?>      

   </tr>
  </thead>
  <!--Table head-->

  <!--Table body-->
  <tbody>
<?php




$aDatosTabla = ejecutarQuery($dbh,"SELECT * FROM ".$nombreTabla);
foreach ($aDatosTabla as $fila) 
{
	echo '<tr class="table-info">';

echo '<td><span style="visibility:hidden">_'.$fila['ID'].'_</span></td>';

echo '<td><input class="form-check-input" type="checkbox" value="'.$fila['ID'].'"  name="cb__'.$fila['ID'].'" id="cb__'.$fila['ID'].'"></td>';
//	 echo '<td onclick="manageDelete('.$fila['ID'].')" align="center" >&nbsp; <i class="fa fa-trash"></i></td>';
	foreach ($aColumnas as $columna) 
	{
		//echo ($columna['COLUMN_NAME']=='ID')?'':'<td><input  name="in__'.$fila['ID'].'__'.$columna['COLUMN_NAME'].'" class="form-control" type="text" value="'.$fila[$columna['COLUMN_NAME']].'" /><span style="visibility:hidden">'.$fila[$columna['COLUMN_NAME']].'</span>'.getInfoColumnaFromTablaId($dbh,$columna['COLUMN_NAME'],$fila[$columna['COLUMN_NAME']]).'</td>';
		echo ($columna['COLUMN_NAME']=='ID')?'':setCampoDeLista($dbh,$fila,$columna);

	}
  echo '</tr>';
}
?>
  </tbody>
  <!--Table body-->

  <tr>
<th></th>
<th>Select</th>
<?php

foreach ($aColumnas as $columna) 
{
	echo ($columna['COLUMN_NAME']=='ID')?'':'<th>'.$columna['COLUMN_NAME'].'['.$columna['COLUMN_TYPE'].']</th>';
}

?>      

   </tr>
     <tfoot>
            <tr>
            	<td></td>
            	<td></td>
<?php
foreach ($aColumnas as $columna) 
{
	echo ($columna['COLUMN_NAME']=='ID')?'':'<th></th>';

}
?>

            </tr>
        </tfoot>      

</table>

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

	<!-- Loading Scripts -->
	<script type="text/javascript">


$(function() {
  $('.selectpicker').selectpicker({
      container: 'body'
    });
});

			 $(document).ready(function () {          
					setTimeout(function() {
						$('.succWrap').slideUp("slow");
					}, 5000);
					});

	function manageDelete()
	{
				Swal.fire({
          title: '¿Seguro que quieres borrar el/los seleccionados?',
          text: "¿Seguro, Seguro, Seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, los borro para siempre!'
        }).then((result) => {
          if (result.value) {
          	document.getElementById("esDelete").value=1;
    document.getElementById("form1").submit(); 
          }
        });
	}

	function manageVerTodosDatos()
	{
		document.getElementById("form1").submit(); 
	}
	function manageModificar()
	{
				Swal.fire({
          title: '¿Seguro que quieres modificar todos los valores de el/los seleccionados?',
          text: "¿Seguro, Seguro, Seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, los modifico para siempre!'
        }).then((result) => {
          if (result.value) {
          	document.getElementById("esModificar").value=1;
    document.getElementById("form1").submit(); 
          }
        });
	}
	function manageInsertar()
	{
				Swal.fire({
          title: '¿Seguro que quieres insertar los valores introducidos?',
          text: "¿Seguro, Seguro, Seguro?",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Sí, los inserto hasta nueva orden!'
        }).then((result) => {
          if (result.value) {
          	document.getElementById("esInsertar").value=1;
    document.getElementById("form1").submit(); 
          }
        });
	}


	tabbb = $('#zctb').DataTable( {
    "order": [[ 2, "asc" ]],
    "scrollX": true
} );
	<?php echo ($idSearch!=-1)?'tabbb.column(0).search("_'.$idSearch.'_").draw();':''?>




	$('#zctb2').DataTable( {
    "scrollX": true
} );



$(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#example2 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder=" '+title+'" />' );
    } );
 
    // DataTable
    var table3 = $('#example2').DataTable({
    	    	"order": [[ 2, "asc" ]],
    "scrollX": true,
        initComplete: function () {
					

            // Apply the search
            this.api().columns().every( function () {
                var that = this;
 
                $( 'input', this.footer() ).on( 'keyup change clear', function () {
                    if ( that.search() !== this.value ) {
                        that
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
        }
    });
  <?php echo ($idSearch!=-1)?'table3.column(0).search("_'.$idSearch.'_").draw();':''?>
} );







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