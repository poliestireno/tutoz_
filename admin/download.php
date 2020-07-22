<?php 
session_start();
////error_reporting(0);
session_regenerate_id(true);
include('../includes/config.php');
require_once("../UTILS/dbutils.php");
$sql = "SELECT username from admin;";
		$query = $dbh -> prepare($sql);
		$query->execute();
		$result=$query->fetch(PDO::FETCH_OBJ);

if((!isset($_SESSION['alogin']))||((strlen($_SESSION['alogin'])==0)||($_SESSION['alogin']!=$result->username)))

	{	
	header("Location: index.php"); //
	}
	else{?>
<table border="1">
									<thead>
										<tr>
										<th>#</th>
											<th>Nombre</th>
											<th>Apellido1</th>
											<th>Apellido2</th>
											<th>Correo</th>
											<th>Curso</th>
											
										</tr>
									</thead>

<?php 
$filename="ALUMNOS list";
$sql = "SELECT * from ALUMNOS WHERE ID <>-1";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
$listaCursos= getAsignaturasConCurso($dbh);
foreach($results as $result)
{	

$nombrAsig="";
foreach ($listaCursos as $curso)
        {
           $pos = strrpos($curso, "--");
           $IDAs=substr($curso,$pos+2,strlen($curso));
           $nombre = substr($curso,0,$pos);

          if($result->ID_CURSO==$IDAs)
          {  
          	$nombrAsig=$nombre;
          	break;
          }
        }


echo '  
<tr>  
<td>'.$cnt.'</td> 
<td>'.$Name= $result->NOMBRE.'</td> 
<td>'.$Phone= $result->APELLIDO1.'</td> 
<td>'.$APELLIDO2= $result->APELLIDO2.'</td> 	
<td>'.$CORREO= $result->CORREO.'</td> 
<td>'.$Gender= $nombrAsig.'</td> 				
</tr>  
';
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=".$filename."-report.xls");
header("Pragma: no-cache");
header("Expires: 0");
			$cnt++;
			}
	}
?>
</table>
<?php } ?>